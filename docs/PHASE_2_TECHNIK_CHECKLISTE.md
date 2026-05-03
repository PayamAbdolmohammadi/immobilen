# Phase 2 — Technische Checkliste  
**Thema:** Mietvertrag → monatliche Miete → automatische Rechnung → (bestehender Flow) Bank Matching

**Randbedingungen (Projekt):** keine überladene HV-Software; Zuverlässigkeit bei Mieten/Zahlungen/Mahnungen steht im Vordergrund.

**Produktbotschaft (nicht Code):** *„Die Miete läuft automatisch.“* — wenn das zuverlässig ist, ist es ein echtes Produkt, nicht nur ein Demo-Tool.

---

## 0. Konzept-Upgrade: Mietvertrag = **Cashflow Engine**

Der Vertrag ist nicht nur Stammdaten-Formular, sondern **Treiber** des wiederkehrenden Geldforderungsflows.

| Ziel | Konsequenz |
|------|------------|
| Generator bleibt simpel | **`next_billing_period` optional** — reiner **operational pointer** (Komfort, Scheduling, UI-Preview) |
| **Source of Truth (faktisch)** | Immer aus **`rechnungen.billing_period`** rekonstruierbar; Zeiger am Vertrag nie alleinige „Wahrheit“ |
| Debug & Support | `last_billed_at` / Zeiger helfen; bei Drift: aus Rechnungsliste den nächsten zu fakturierenden Monat ableiten können |
| Skalierung | Gleiches Muster später für NK/Abrechnungen unter `Billing` wiederverwendbar |

---

## 1. Code-Struktur (Domain, future-proof minimal)

Pfadkonvention (Laravel autoload bereits `App\`):

- [ ] **`App\Domain\Leasing`** — `Mietvertrag`, Einheiten-Zuordnung, „aktiver Vertrag“, Endpoints die Verträge betreffen
- [ ] **`App\Domain\Billing`** — Rechnungserzeugung aus Vertrag, `RentAmountCalculator`, `GenerateMonthlyRentInvoices`, Perioden-Logik
- [ ] **`App\Domain\Payments`** — (Phase 2 nur Anbindung) Bank/Matching bleibt hier referenziert; später NK-Anteile

**Hinweis:** Kein Microservice — nur **Namespaces/Ordner + klare Grenzen**, damit NK (Phase 3) nicht „in den Vertragsspaghetti“ läuft.

---

## 2. Migrationen

- [ ] **Tabelle `mietvertraege`** (oder `leases` — ein Name, durchgängig verwenden)
  - `mandant_id` (FK, indexed)
  - `einheit_id` (FK)
  - `mieter_id` (FK)
  - `starts_on` (date)
  - `ends_on` (date, nullable — offenes Ende erlauben oder Policy festlegen)
  - `status` (`active`, `ended` — Enum/String + Index)
  - `kaltmiete_cent` oder `decimal(12,2)` (einheitliche Währungsstrategie wie Phase 1)
  - `nebenkosten_vorauszahlung_cent` (oder decimal)
  - optional: `zahlungsintervall` (für Phase 2 nur `monthly` erlauben, Default)
  - optional: `faelligkeit_tag` (1–28 oder pro Mandant-Default — vermeidet „29–31“-Randfälle)
  - **`next_billing_period`** (`CHAR(7)` `YYYY-MM`, **nullable**, optional — nur **operational pointer**; Rekonstruktion aus `rechnungen` bleibt möglich)
  - **`last_billed_at`** (`timestamp`, nullable — wann zuletzt erfolgreich eine Miet-Rechnung erzeugt wurde)
  - Timestamps + `ended_at` / `ended_by_user_id` optional für Audit light

- [ ] **`rechnungen` erweitern**
  - `mietvertrag_id` (nullable FK)
  - `typ` (`rent`, `manual` — später erweiterbar)
  - `billing_period` (z. B. `CHAR(7)` **`YYYY-MM`** oder zwei Spalten `billing_year`, `billing_month` — entscheiden und dokumentieren)
  - **`source_data`** (`json`, nullable) — Snapshot in **Cent** (keine Euro-Floats), z. B. `{"kaltmiete_cent":80000,"nebenkosten_vorauszahlung_cent":20000,"total_cent":100000}`
  - optional später: `storno_of_rechnung_id` / `storniert_am` wenn Storno-Modell explizit wird
  - garantieren: Kombination passt zur Idempotenz (siehe Abschnitt 6)

- [ ] **Indizes / Constraints**
  - Index auf `(mandant_id, status)` bei `mietvertraege`
  - **MySQL / Phase 2 (einfach):** `unique(['mietvertrag_id', 'billing_period'])` auf `rechnungen` — **`manual`-Rechnungen mit `mietvertrag_id = null`** kollidieren nicht (MySQL: mehrere Zeilen mit `NULL` in Unique-Keys erlaubt). App stellt sicher: **`typ=rent` ⇒ `mietvertrag_id` + `billing_period` gesetzt.**
  - Falls später Teilperioden/mehrere Rent-Typen pro Monat kommen: dann ggf. filtered unique oder Hilfstabelle neu bewerten.
  - SQLite (Tests/`php artisan test`): dasselbe Unique-Verhalten analog; Idempotenz weiterhin zusätzlich im Service (Lock + Checks).

- [ ] **`einheiten`** (falls noch nicht vorhanden)
  - keine Pflichtfeldänderungen für Phase 2 außer was für FK nötig ist

- [ ] **`mieter`**
  - nur FK-Integrität zu `mandant_id` prüfen (konsistent mit Phase 1)

---

## 3. Neue Felder (Kurzüberblick)

| Ort | Feld | Zweck |
|-----|------|--------|
| `mietvertraege.*` | `mandant_id`, `einheit_id`, `mieter_id` | Zuordnung + Mandantenschutz |
| | `starts_on`, `ends_on`, `status` | Laufzeit + nur ein aktiver Vertrag je Einheit (siehe Validierung) |
| | Beträge NK/Kalt (+ ggf. `faelligkeit_tag`) | Berechnungsgrundlage Mietzins |
| | **`next_billing_period`** | Optionaler **Operational-Pointer**, nicht alleinige Wahrheit (siehe §0) |
| | **`last_billed_at`** | Letzte erfolgreiche Miet-Rechnung (Ops/Debug) |
| `rechnungen.*` | `mietvertrag_id` | Quelle bei `rent` |
| | `typ` | `manual` vs `rent` |
| | `billing_period` | Idempotenz + Reporting |
| | bestehendes `betrag_*` | **Snapshot** des fälligen Gesamtbetrags zum Erstellzeitpunkt |
| | **`source_data` (json)** | Snapshot-Komponenten in **Cent** (`*_cent`) für Audit/Streit, ohne Rundungs-Floating-Point |

---

## 4. Models / Relations

- [ ] **`Mietvertrag`**
  - `belongsTo`: `Mandant`, `Einheit`, `Mieter`
  - `hasMany`: `Rechnungen` (nur wo `typ=rent`)

- [ ] **`Rechnung`** (erweitern)
  - `belongsTo`: `Mietvertrag` (nullable), `Mieter`, `Einheit` (wie Phase 1)
  - Casts: `source_data` als `array` (oder `AsArrayObject`)
  - Hilfsmethode oder Scope: `rent()`, `manual()`, `forPeriod($period)`
  - **Policy:** nach Erzeugung keine mutierenden Updates auf Betrag/Typ/Verknüpfung (siehe Abschnitt 7)

- [ ] **`Einheit`**
  - `hasMany`: `Mietvertraege`
  - Methode **`activeMietvertrag()`** oder Query-Scope für genau einen `status=active`

- [ ] **`Mieter`**, **`Mandant`**
  - `hasMany` zu Verträgen / Rechnungen wo sinnvoll (nur lesen/UI)

**Wichtig:** Alle Queries **immer** mit `mandant_id` aus dem authentifizierten Kontext — bestehendes Pattern aus Phase 1 wiederverwenden.

---

## 5. Service-Klassen (vorgeschlagene Schnittstellenteile, ohne Implementierung)

- [ ] **`MietvertragService`** (Domain **Leasing**)
  - `create(...)`: Validierung „nur ein aktiver Vertrag je Einheit“; initial `next_billing_period` aus Policy setzen (z. B. erster abrechenbarer Monat ab `starts_on`)
  - `endContract(...)`: setzt `ended`, `ends_on`; optional bestehende offene Rent-Rechnungen policy (dokumentieren: stornieren vs. stehen lassen)
  - `updateContract(...)`: **Phase 2 = Option A** — Bearbeitung erlaubt, wirkt nur auf **zukünftige** Generierung (siehe Risiken); keine retroaktive Änderung offener/alter Rechnungen

- [ ] **`RentAmountCalculator`** (Domain **Billing**, klein, testbar)
  - Eingabe: Vertrag (Kalt + NK-Vorauszahlung)
  - Ausgabe: Gesamtbetrag + Struktur für `source_data`
  - Keine Index-/Staffelmietlogik in Phase 2

- [ ] **`GenerateMonthlyRentInvoices`** (Domain **Billing**, Haupt-Use-Case)
  - Eingabe: Zielmonat/`billing_period`, optional `mandant_id`
  - Pro Vertrag: **pessimistisches Lock** (`lockForUpdate()` auf `mietvertraege`-Row) während Erzeugung — schützt vor Race bei parallelem Cron/Queue
  - **Checks (Reihenfolge):**  
    1. Vertrag `active`?  
    2. `billing_period` innerhalb Laufzeit (`starts_on` / `ends_on` Policy)?  
    3. Bereits abgerechnet? (DB-Unique + optional Abgleich `next_billing_period`)  
  - Nach erfolgreichem Insert: **`last_billed_at` = now**, **`next_billing_period`** auf nächsten Monat (oder konsistent mit eurer Perioden-Logik) setzen
  - **Transaktion** pro Vertrag empfohlen (Lock + Insert + State-Update)

- [ ] **`MietvertragEligibility`** (optional, für Lesbarkeit)
  - „Ist Vertrag für Periode X abrechenbar?“ — vermeidet doppelte Logik zwischen Command und Tests

- [ ] **`StornoRechnungService`** (minimal, kann Phase-2.5 sein)
  - Korrektur **nicht** durch Update: **Storno** + ggf. **neue** Rechnung (manuelle oder neue Periode); Bank-Matching-Historie bleibt konsistenter Gedanke

Scheduling:

- [ ] **`php artisan rents:generate-monthly`** (Command ruft nur `GenerateMonthlyRentInvoices` auf)
  - später: Scheduler + Queue-Worker; **Lock pro Vertrag** bleibt Pflicht, damit doppelte Jobs safe sind

---

## 6. Idempotenz-Regeln (inkl. „professionell“)

- [ ] **Pro `(mietvertrag_id, billing_period)` höchstens eine `Rechnung` mit `typ=rent`** — DB-Unique wo möglich, sonst Hilfstabelle / strikter App-Check — **Pflicht.**

- [ ] Cron/Command **beliebig oft ausführbar** ohne neue Duplikat-Rechnungen.

- [ ] `billing_period` **immer explizit** auf der Rechnung (nie nur implizit aus `faellig_am`).

- [ ] **Snapshot:** Betrag + `source_data` beim Erzeugen schreiben; spätere Vertragsänderung ändert **keine** bestehende Rechnung.

- [ ] **`faellig_am`** beim Erzeugen speichern (nicht für alte Rows aus aktuellem Vertrag neu berechnen).

- [ ] **Concurrency:** während `GenerateMonthlyRentInvoices` für einen Vertrag → **Row-Lock** auf `mietvertraege` (oder explizites Lock), damit zwei Worker dieselbe Periode nicht doppelt anlegen.

---

## 7. Business-Regel: Rechnung = **immutable**

- [ ] Kein `update` auf Kernfeldern einer ausgestellten Rechnung (Betrag, `typ`, `billing_period`, Verknüpfungen) über normale UI/CRUD.
- [ ] Korrekturpfad: **Storno** (und Buchung im System klar markieren) + **neue** Rechnung — auch wenn Storno in Phase 2 noch ein einfaches Flag/Record ist, das Prinzip festlegen.
- [ ] **Grund:** Vertrauen, Accounting, Bank-Matching, Mahnwesen.

---

## 8. UI-Seiten (Blade oder stack aus Phase 1)

- [ ] **`/mietvertraege`** — Liste (Filter: aktiv / beendet, Suche Einheit/Mieter)
- [ ] **`/mietvertraege/create`** — Formular: Einheit, Mieter, Start, Beträge, optional Fälligkeitstag
- [ ] **`/mietvertraege/{id}`** — Detail inkl. generierter Rechnungen + **Copy für Conversion:**
  - Kurztext: *„Monatliche Miete wird automatisch als Rechnung erstellt.“*
  - **Preview-Block:** *„Nächste Rechnung:“* Datum (aus `next_billing_period` + Fälligkeitsregel) **und** Betrag (aus aktuellem Vertrag berechnet — Hinweis: „Erste tatsächliche Forderung = Snapshot nach Lauf“)
- [ ] **Aktion** „Vertrag beenden“ (mit Bestätigung + Datum)
- [ ] **`/rechnungen`** — Spalten: `typ`, `billing_period`, Link zu Vertrag wenn `rent`; keine Editierfelder für gesperrte Kernfelder
- [ ] **Dashboard** — „Nächste / aktuelle Perioden“, letzte automatische Miet-Rechnungen, optional Fehler-Hinweis Generator

**UX:** Bei Erstellung optional Checkbox „Erste Rechnung für aktuellen Monat sofort erzeugen“ — gleicher Service wie Cron. **Klartext** auf der Seite erhöht Vertrauen und Demo-Verkauf.

---

## 9. Validierungsregeln

**Mietvertrag anlegen:**
- [ ] Einheit, Mieter, Mandant konsistent (`einheit.mandant_id == mieter.mandant_id == current`)
- [ ] `starts_on` erforderlich; `ends_on` optional ≥ `starts_on`
- [ ] Beträge > 0 (Policy für kostenfrei explizit ausschließen oder erlauben)
- [ ] **Genau ein `active` Vertrag pro `einheit_id`** vor `create`/`resume` prüfen
- [ ] `faelligkeit_tag` wenn genutzt: 1–28 empfohlen (oder dokumentierte Regel für 29–31)
- [ ] Mehrere **aktive** Verträge **desselben Mieters** an **verschiedenen Einheiten** erlauben; widersprüchliche Business-Fälle dokumentieren (z. B. gleiche Person, zwei Objekte)

**Manuelle Rechnung (Phase 1):**
- [ ] `typ=manual`, `mietvertrag_id` und `billing_period` **nullable** oder leer — konsistent halten

**Rent-Rechnung (nur System):**
- [ ] NIEMALS durch Standard-CRUD ohne Service erzeugbar (Policy/Gate oder nur interner Aufruf)

**Vertrag bearbeiten (Phase 2):**
- [ ] **Option A:** Bearbeitung erlaubt — gilt für **künftige** Monatsläufe; bestehende Rechnungen unberührt; UI-Hinweis anzeigen

---

## 10. Tests (Priorität)

- [ ] **Unit:** `RentAmountCalculator` (Grenzfälle: Kalt + NK Summe, Rundung)

- [ ] **Feature / Integration:**
  - Vertrag anlegen → Generator einmal → eine `rent`-Rechnung mit `billing_period`, Snapshot-Betrag und **`source_data`**
  - Zweiter Aufruf **gleiche Periode** → keine zweite Rechnung
  - Zwei Verträge, verschiedene Einheiten, gleiche Periode → zwei Rechnungen
  - **Vertrag Mitte des Monats angelegt** — Policy: erster `billing_period` wie definiert (z. B. ab aktuellem Monat vs. ab Folgemonat) → erwartbarer Test
  - **Vertrag Mitte des Monats beendet** — erwartbar: letzte volle / keine Teilperiode (gemäß Dokumentation)
  - **Mehrere Verträge / gleicher Mieter** (verschiedene `einheit_id`) — keine fälschliche Blockierung, korrekte Isolation
  - **Concurrency:** zwei parallele Generator-Aufrufe (z. B. `Bus::fake` oder DB-Transaktionen mit Lock) → keine Duplikat-Rechnung

- [ ] **Mandanten-Isolation:** User Mandant A sieht/ändert nichts von B

- [ ] **`active`-Vertrag-pro-Einheit** — zweiter aktiver Versuch scheitert

---

## 11. Risiken

| Risiko | Mitigation |
|--------|------------|
| Partial unique in DB nicht portabel | Unique-Hilfstabelle oder App-Check + **Lock pro Vertrag** |
| Monatsgrenzen / Zeitzone | Eine Policy (z. B. Europe/Berlin) + `billing_period` explizit |
| Vertrag bearbeitet | **Option A (Phase 2):** nur Zukunft; UI-Text; Snapshots bleiben |
| Generator + Queue doppelt | Row-Lock + Idempotenz-Unique |
| User erwartet Retro-Korrektur | Immutable invoices — Storno-Pfad kommunizieren |
| `next_billing_period` drifted von echten Rechnungen | Regelmäßig aus Rechnungen ableitbar machen (Repair-Command optional) oder nach jeder Generierung strikt updaten |

---

## 12. Umsetzungsreihenfolge (empfohlen)

1. Domain-Ordner anlegen: `Leasing`, `Billing`, `Payments` (Payments leer oder nur Typehints)
2. Migration `mietvertraege` inkl. **`next_billing_period`**, **`last_billed_at`**
3. Migration `rechnungen`: `mietvertrag_id`, `typ`, `billing_period`, **`source_data`**, Unique-Strategie Rent
4. Models + Casts + Factory/Seeder
5. `MietvertragService` (create/end/update Option A + ein aktiver Vertrag je Einheit + initiale Perioden-Pointer)
6. `RentAmountCalculator`
7. `GenerateMonthlyRentInvoices` mit **Checks 1–3**, **Lock**, State-Update nach Erfolg
8. Idempotenz- + Concurrency-Tests
9. Artisan `rents:generate-monthly`
10. UI inkl. **Auto-Miete**-Hinweis und **Nächste Rechnung**-Preview
11. Dashboard; Scheduling-Doku
12. Minimaler Storno-/Korrektur-Plan (auch wenn UI erst später)

---

## Abgleich mit euren Produktregeln

| Regel | Phase-2-Relevanz |
|-------|------------------|
| Jede Rechnung hat `mandant_id` | ✓ |
| Rent aus `Mietvertrag` | ✓ + **Cashflow-State** am Vertrag |
| `billing_period` + eine Rechnung pro Periode | ✓ + Lock |
| Beträge Snapshot | ✓ + **`source_data`** |
| Bank Matching nie ohne User-Freigabe | ✓ unverändert Phase 1 |
| Eine Einheit nur ein aktiver Vertrag | ✓ |

**Ergänzung:** Rechnungen **immutable**; Vertrag **Source of Truth** für künftige Läufe; UX **einfach** („läuft automatisch“ + Preview).

---

## 13. Fünf Prinzipien („kleine aber echte HV“)

1. Idempotenz (+ Lock)  
2. Snapshot (`betrag` + `source_data`)  
3. Immutable invoices (Korrektur über Storno/Neu)  
4. Vertrag = Source of Truth + **Cashflow Engine** (`next_billing_period` / `last_billed_at`)  
5. Einfache UX (Vertrauen + Conversion)

---

*Dokument-Version:* Phase-2-Checkliste inkl. Cashflow-State, Locking, Domain-Split, `source_data`, Immutable-Regel, erweiterte Tests und Vertrags-Edit-Policy (Option A) — ohne Codeimplementierung.
