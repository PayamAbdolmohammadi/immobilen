# WOW Demo – Verkaufsdemo (15 Minuten)

Ziel: In etwa **15 Minuten** den typischen Verwaltungsablauf zeigen: **Mietvertrag mit PDF und Link**, **Bestätigung durch den Mieter**, **automatische Mietrechnung**, **Bankimport und Zuordnung**, **Status bezahlt**, **DATEV-Export**.

**Vorbereitung (kurz):** Demo als **Inhaber** führen. Optional frische Daten mit `php artisan migrate:fresh --seed` — Login **`demo@example.com`** / **`password`** (`DemoDataSeeder`), aktiver Mietvertrag z. B. Anna Schmidt, Demo-Bankzeilen. WOW-Hilfsseite **`/demo-flow`** mit Links zu Kernbereichen. Demo-Beträge am **ersten aktiven Mietvertrag** ausgerichtet (sonst Fallback **1200,00 €** in der Demo-CSV); **`[DEMO]`** kennzeichnet Demo-Importe im Verwendungszweck bzw. Namen.

---

## 1. Eröffnung (30 Sekunden)

Ich zeige Ihnen in kurzer Zeit einen typischen Ablauf bei einer Hausverwaltung: vom Mietvertrag bis zur bezahlten Miete und dem Export für die Buchhaltung — ohne dass wir über Programmierung reden müssen.

**Kernaussage:** Ein durchgängiger Weg vom Vertrag bis DATEV, verständlich für Nicht-Techniker.

---

## 2. Demo Ablauf (Schritt für Schritt)

### Schritt 1

| Feld | Inhalt |
|------|--------|
| **Navigation** | Seitenleiste **Verwaltung** → **Mietverträge** → bei einem Eintrag **Ansehen**. |
| **Was zeigen** | Die Detailseite des Mietvertrags mit Status und Angaben zu Mieter und Einheit. |
| **Was sagen** | Hier liegt der Mietvertrag digital — nicht mehr nur in einer losen Excel-Liste. |
| **Kernaussage** | Der Vertrag ist der feste Ausgangspunkt für Rechnung und Nachweis. |
| **Nicht sagen** | Datenbank, Migration, Framework-Namen. |

### Schritt 2

| Feld | Inhalt |
|------|--------|
| **Navigation** | Auf derselben Seite **PDF erzeugen**, danach **Link erstellen** oder kopieren (je nach Beschriftung in der Oberfläche). |
| **Was zeigen** | Dass ein PDF erzeugt wird und ein Link für den Mieter bereitsteht. |
| **Was sagen** | Der Mieter bekommt einen sicheren Link und kann den Vertrag lesen und bestätigen — ähnlich wie heute per Post oder E-Mail, aber strukturierter nachvollziehbar. |
| **Kernaussage** | Weniger Rückfragen, klarer Zeitpunkt der Bestätigung. |
| **Nicht sagen** | Token, Hash, Verschlüsselungsdetails. |

### Schritt 3

| Feld | Inhalt |
|------|--------|
| **Navigation** | Zweites Browserfenster oder privates Fenster: eingefügter Mieter-Link öffnen → Formular zur Bestätigung absenden. |
| **Was zeigen** | Die einfache Mieteransicht und die erfolgreiche Bestätigung. |
| **Was sagen** | So sieht es beim Mieter aus — wenige Klicks. Die genaue rechtliche Bewertung klären Sie mit Ihrer Kanzlei; hier geht es um den praktischen Ablauf. |
| **Kernaussage** | Der Mieter kann ohne Zugriff auf fremde Daten nur seinen eigenen Vertrag sehen und bestätigen. |
| **Nicht sagen** | Interne Modellnamen oder API. |

### Schritt 4

| Feld | Inhalt |
|------|--------|
| **Navigation** | Zurück im Hauptfenster (Verwaltungsnutzer): optional **WOW-Demo** über die Übersicht oder die Navigation — Route **`/demo-flow`** — oder direkt den nächsten Schritt über die beschriebene Menüführung. Auf der WOW-Demo-Seite oder über den dortigen Hinweis die **Mietrechnungen für den aktuellen Monat erzeugen** (gleiche Logik wie der geplante Monatslauf `rents:generate-monthly`, nur für den eigenen Mandanten). |
| **Was zeigen** | Dass nach aktivem Vertrag eine Monatsmietrechnung erzeugt wird. |
| **Was sagen** | Wenn der Vertrag aktiv ist, kann die Software den wiederkehrenden Teil übernehmen — weniger manuelle Doppelarbeit jeden Monat. |
| **Kernaussage** | Feste Regeln statt jedes Mal neu rechnen und neu dokumentieren. |
| **Nicht sagen** | Artisan, Cron-Ausdrücke, Klassennamen im Code. |

### Schritt 5

| Feld | Inhalt |
|------|--------|
| **Navigation** | **FINANZEN** → **Bankimport** → Demo-Bank-CSV herunterladen (über WOW-Demo oder Hilfslink) und mit Profil **generisch** hochladen — alternativ **Demo-Bankzeile ohne Upload**, falls angeboten. |
| **Was zeigen** | Neue Bankzeilen in der Liste; Hinweise zu Demo-Kennzeichnung (z. B. `[DEMO]` im Verwendungszweck). |
| **Was sagen** | Die Bank kommt nicht aus zerstreuten Zellen — wir holen sie in einem strukturierten Schritt rein. Das reduziert Übertragungsfehler. |
| **Kernaussage** | Weniger Copy-and-Paste zwischen Portal und Excel. |
| **Nicht sagen** | Parser, Dateiformat-Technik im Detail. |

### Schritt 6

| Feld | Inhalt |
|------|--------|
| **Navigation** | **Zahlungen zuordnen** (Bankzuordnung) → passende Zeile zur **offenen Mietrechnung** zuordnen — Aktion **Zuordnen**. |
| **Was zeigen** | Dass Sie die Zuordnung bewusst bestätigen. |
| **Was sagen** | Sie entscheiden noch immer — aber Sie klicken statt lange zu suchen. Automatische Vorschläge ersetzen keine Freigabe durch die Verwaltung. |
| **Kernaussage** | Kontrolle und Buchhaltungstauglichkeit bleiben erhalten. |
| **Nicht sagen** | Matching-Algorithmus, interne Services. |

### Schritt 7

| Feld | Inhalt |
|------|--------|
| **Navigation** | **Übersicht** (Dashboard) → passende Rechnung in der Liste oder Detail prüfen. |
| **Was zeigen** | Status der Rechnung **bezahlt** nach erfolgreicher Zuordnung. |
| **Was sagen** | Hier sehen Sie auf einen Blick: ist das Geld da oder noch offen — gut für Gespräche mit Mandanten und Eigentümern. |
| **Kernaussage** | Cashflow wird lesbar ohne zusätzliche Listenpflege. |
| **Nicht sagen** | Widget-Technik, Datenbankfelder. |

### Schritt 8

| Feld | Inhalt |
|------|--------|
| **Navigation** | **FINANZEN** → **Buchhaltung / Export** → **DATEV-ready CSV** herunterladen. |
| **Was zeigen** | Die exportierte Datei (kurz öffnen oder Namen zeigen). |
| **Was sagen** | Das können Sie so oder nach kurzer Abstimmung mit dem Steuerberater weitergeben — ohne jede Woche neue Excel-Vorlagen zu pflegen. |
| **Kernaussage** | Anknüpfung an die DATEV-Welt bleibt bestehen; Details klärt das Team mit der Kanzlei. |
| **Nicht sagen** | „Offizieller DATEV-Import garantiert“, wenn ihr nur „ready“ anbietet — ehrlich bleiben wie in der Produktbezeichnung. |

---

## 3. Abschluss (30 Sekunden)

Das war der rote Faden: Vertrag → klare Bestätigung → wiederkehrende Miete → Bank → Zuordnung → bezahlt → Export. Passt das zu einem Bereich, in dem Sie oder Ihre Mandanten heute Zeit verlieren — dann können wir als Nächstes klären, ob ein kurzer Test mit echten Daten Sinn macht.

**Kernaussage:** Sie bieten Zeitersparnis und klare Übergabe Richtung Buchhaltung — keine „Software zum Spielen“.

---

## 4. Outreach (Steuerberater finden)

### Wo finden?

| Kanal | Vorgehen |
|-------|-----------|
| **Google Maps / Google** | Begriff „Steuerberater“ plus Stadt oder Region; Websites lesen, ob **Hausverwaltung**, **WEG**, **Vermietung** oder ähnliche Schwerpunkte genannt werden. |
| **LinkedIn** | Suche nach Steuerberatern mit Bezug zu **DATEV**, **Immobilien**, **Verwaltung** in der Region; eher kleine bis mittlere Profile als Großkanzleien ohne Mittelstandsfokus. |
| **Verbände und Empfehlungen** | Hinweise bei Hausverwaltungsverbänden oder bestehenden Mandanten auf „unsere Steuerkanzlei für DATEV“. |

### Wen ansprechen?

**Passend:** Kanzlei **klein bis mittel**, bereits **DATEV**, Mandanten aus **Hausverwaltung**, **Vermietung** oder **kleinen Holdings**.

**Weniger passend:** Berater ohne Immobilienbezug — dann ist das Gespräch oft kurz und weniger konkret.

---

## 5. Nachrichten

### Cold Email

**Betreff:** Kurze Demo — Miete bis DATEV

Hallo [Name],

ich arbeite an einem Tool für Hausverwaltungen: vom Mietvertrag über Bankzuordnung bis zum DATEV-Export.

Ich suche gezielt Feedback von Steuerkanzleien, die solche Mandanten betreuen — nicht zum Schnellverkauf, sondern um zu hören, ob der Ablauf in der Praxis passt.

Hätten Sie **15 Minuten** nächste Woche für einen kurzen Bildschirmtermin?

Viele Grüße  
[Ihr Name]  
[Rolle, eine Zeile]  
[Kontakt]

### LinkedIn Message

Hallo [Name],

ich zeige Hausverwaltungen einen durchgängigen Ablauf bis DATEV und möchte das kurz mit Steuerberatern abstimmen, die solche Mandanten haben. Darf ich Ihnen **15 Minuten** nächste Woche vorschlagen — nur Demo und Rückfragen, ohne klassisches Verkaufsgespräch?

### Follow-up

**Betreff:** Nur kurz nachgefragt

Hallo [Name],

ich melde mich einmal nach — falls der Posteingang voll ist.

Geht es bei Ihnen diese oder nächste Woche für **15 Minuten**?

Wenn das Thema gerade nicht passt, ein kurzes „Nein“ reicht — dann höre ich auf zu schreiben.

Danke und Grüße  
[Ihr Name]

---

## 6. Demo Call Struktur

| Block | Zeit | Inhalt |
|-------|------|--------|
| **Intro** | 2 Minuten | Wer Sie sind (ein Satz), warum der Gesprächspartner (Immobilienmandanten, DATEV), Ziel: einen Ablauf zeigen und fragen, wo es in der Praxis hakt. |
| **Demo** | 8 Minuten | Ablauf wie in Abschnitt 2 — langsam klicken, nach Bank und Zuordnung kurz fragen: „Ist das bei Ihren Mandanten vergleichbar?“ |
| **Fragen** | 5 Minuten | Die Fragen aus Abschnitt 7; mit Notizen zu Excel, DATEV-Feldern und Preisvorstellungen abschließen. |

---

## 7. Fragen

- Wo verlieren **Ihre Hausverwaltungsmandanten** heute die meiste Zeit — vor oder nach der Buchhaltung?
- Kommen die Unterlagen bei Ihnen meist als **Excel**, **DATEV**, **PDF** oder als **Mix**?
- Wie oft gibt es **offene Posten**, die schwer zur Bank passen?
- Was müsste ein Tool **mindestens** können, damit Sie es einem Mandanten empfehlen würden?
- Gibt es Bereiche, in denen Sie **nichts ändern** wollen (z. B. feste Vorlagen)?
- Wenn der **Preis** stimmt: Pilot mit **echten Daten** oder zuerst nur mit Demo-Daten?
- Wen sollte man auf **Mandantenseite** einbinden — Geschäftsführung oder Buchhaltung?

---

## 8. Umgang mit Einwänden

| Aussage | Antwort |
|---------|---------|
| **„Zu kompliziert.“** | „Welcher Teil war zu viel auf einmal — Vertrag, Bank oder Export? Ich kann beim nächsten Mal langsamer sein oder nur einen Abschnitt zeigen.“ |
| **„Brauchen wir nicht.“** | „Verstanden. Gibt es trotzdem einen Punkt — nur Bankzuordnung oder nur DATEV-Übergabe — bei dem Ihre Mandanten oft nachfragen?“ |
| **„Interessant, aber später.“** | „Gerne. Was wäre ein guter Zeitpunkt — eher nach der Jahresabschlusszeit oder Mitte des Jahres? Ich schlage ein kurzes Datum vor und Sie sagen Ja oder Nein.“ |

---

## 9. Erfolgskriterien

**Stark**

- Nachfrage nach **Zugang**, **Test** oder **Pilot mit einem Mandanten**.
- Direkte Frage nach **Preis** oder **Vertrag**.

**Solide**

- Nennung einer **konkreten Person** bei einem Verwaltungsmandanten zur Weiterleitung.
- **Zweiter Termin** mit klarer Agenda (z. B. nur Bankteil).

**Schwach aber okay**

- **Schriftliches Feedback** (z. B. welche DATEV-Spalte fehlt) — dokumentieren und nicht als Ablehnung werten.
