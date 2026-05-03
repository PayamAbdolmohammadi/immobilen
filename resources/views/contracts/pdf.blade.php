<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Wohnraummietvertrag</title>
    <style>
        @page { margin: 22mm 18mm 24mm 18mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5pt;
            line-height: 1.42;
            color: #111;
            margin: 0;
        }
        h1 {
            font-size: 15pt;
            font-weight: bold;
            text-align: center;
            margin: 0 0 16pt 0;
            page-break-after: avoid;
        }
        h2 {
            font-size: 10.8pt;
            font-weight: bold;
            margin: 12pt 0 6pt 0;
            padding-bottom: 2pt;
            border-bottom: 0.6pt solid #999;
            page-break-after: avoid;
        }
        p { margin: 0 0 5pt 0; }
        ul { margin: 4pt 0 8pt 16pt; padding: 0; }
        li { margin: 0 0 3pt 0; }
        .parties-block { margin: 10pt 0 14pt 0; }
        .muted { color: #444; font-size: 9.2pt; }
        .dyn { font-weight: normal; }
        .box-meta {
            margin-top: 14pt;
            padding: 8pt 10pt;
            border: 0.6pt solid #ccc;
            background: #fafafa;
            page-break-inside: avoid;
        }
        .box-meta p { margin-bottom: 4pt; }
        .footer {
            margin-top: 18pt;
            padding-top: 8pt;
            border-top: 0.5pt solid #ccc;
            font-size: 8.5pt;
            color: #555;
            page-break-inside: avoid;
        }
        .sig-wrap { margin-top: 20pt; page-break-inside: avoid; }
        .sig-table { width: 100%; border-collapse: collapse; margin-top: 8pt; table-layout: fixed; }
        .sig-table td {
            width: 50%;
            vertical-align: top;
            padding-top: 36pt;
            padding-right: 12pt;
            border-top: 0.6pt solid #000;
            font-size: 9.5pt;
        }
    </style>
</head>
<body>
@php
    /** @var \App\Models\Mietvertrag $vertrag */
    /** @var string $totalEuro */
    /** @var \Illuminate\Support\Carbon $pdfGeneratedAt */
    /** @var \App\Models\ContractEvent|null $acceptedAuditEvent */
    /** @var int|null $kaution_cent */
    /** @var string|null $additional_notes */
    /** @var string|null $zimmeranzahl */
    /** @var string|null $mieter_adresse */
    /** @var string|null $objekt_adresse */

    $kaution_cent = $kaution_cent ?? null;
    $additional_notes = $additional_notes ?? null;
    $zimmeranzahl = $zimmeranzahl ?? null;
    $mieter_adresse = $mieter_adresse ?? null;
    $objekt_adresse = $objekt_adresse ?? null;

    $fmtEuro = static function (?int $cent): string {
        if ($cent === null) {
            return '—';
        }

        return number_format($cent / 100, 2, ',', '.').' €';
    };

    $vertragsstatusLabel = match ($vertrag->status) {
        \App\Models\Mietvertrag::STATUS_DRAFT => 'Entwurf',
        \App\Models\Mietvertrag::STATUS_SENT => 'Versendet (offen zur Bestätigung)',
        \App\Models\Mietvertrag::STATUS_ACTIVE => 'Aktiv',
        \App\Models\Mietvertrag::STATUS_ENDED => 'Beendet',
        default => (string) $vertrag->status,
    };

    $einheit = $vertrag->einheit;
    $objekt = $einheit?->objekt;
    $mieter = $vertrag->mieter;
    $mandant = $vertrag->mandant;

    $vermieterZeile = $mandant?->name
        ? $mandant->name
        : '—';

    $objektAnschrift = $objekt_adresse
        ? $objekt_adresse
        : ($objekt?->name ?? '—');

    $mieterEmail = trim((string) ($mieter?->email ?? '')) ?: '—';
    $mieterAddrZeile = $mieter_adresse ? $mieter_adresse : '—';
    $zimmerText = $zimmeranzahl ? $zimmeranzahl : '—';

    $ua = $acceptedAuditEvent?->user_agent;
    $uaKurz = $ua && strlen($ua) > 160 ? substr($ua, 0, 157).'…' : $ua;
@endphp

    <h1>Wohnraummietvertrag</h1>

    <div class="parties-block">
        <p>Zwischen</p>
        <p><strong>Vermieter / Verwaltung:</strong><br>
            <span class="dyn">{{ $vermieterZeile }}</span></p>
        <p style="margin-top:8pt;">und</p>
        <p><strong>Mieter:</strong><br>
            <span class="dyn">{{ $mieter?->name ?? '—' }}</span><br>
            <span class="dyn">E-Mail: {{ $mieterEmail }}</span><br>
            <span class="dyn">Anschrift: {{ $mieterAddrZeile }}</span></p>
    </div>

    <h2>§ 1 Mietsache</h2>
    <p><strong>Objekt / Anschrift:</strong> <span class="dyn">{{ $objektAnschrift }}</span></p>
    <p><strong>Einheit / Wohnung:</strong> <span class="dyn">{{ $einheit?->name ?? '—' }}</span></p>
    <p><strong>Zimmer:</strong> <span class="dyn">{{ $zimmerText }}</span></p>
    <p><strong>Nutzung:</strong> ausschließlich zu Wohnzwecken.</p>

    <h2>§ 2 Mietzeit / Kündigung</h2>
    <p><strong>Mietbeginn:</strong> <span class="dyn">{{ $vertrag->starts_on?->format('d.m.Y') ?? '—' }}</span></p>
    <p>Der Vertrag wird auf unbestimmte Zeit geschlossen.</p>
    <p>Die Kündigung erfolgt nach den gesetzlichen Vorschriften.</p>

    <h2>§ 3 Miete</h2>
    <p><strong>Kaltmiete (monatlich):</strong> <span class="dyn">{{ $fmtEuro((int) $vertrag->kaltmiete_cent) }}</span></p>
    <p><strong>Nebenkosten / Betriebskosten-Vorauszahlung (monatlich):</strong> <span class="dyn">{{ $fmtEuro((int) $vertrag->nebenkosten_vorauszahlung_cent) }}</span></p>
    <p><strong>Gesamtmiete (monatlich):</strong> <span class="dyn">{{ $totalEuro }} €</span></p>
    <p>Die Miete ist monatlich im Voraus spätestens bis zum dritten Werktag eines Monats zu zahlen.</p>

    <h2>§ 4 Kaution</h2>
    @if ($kaution_cent !== null && $kaution_cent > 0)
        <p><strong>Kaution:</strong> <span class="dyn">{{ $fmtEuro($kaution_cent) }}</span></p>
    @else
        <p><strong>Kaution:</strong> nicht vereinbart</p>
    @endif
    <p>Die Kaution dient der Sicherung aller Ansprüche aus dem Mietverhältnis.</p>

    <h2>§ 5 Nutzung der Mietsache</h2>
    <ul>
        <li>Die Mietsache ist nur zu Wohnzwecken zu nutzen.</li>
        <li>Eine Untervermietung ist nur mit vorheriger Zustimmung des Vermieters zulässig.</li>
        <li>Die Tierhaltung ist nur nach Vereinbarung bzw. im Rahmen gesetzlicher Vorgaben zulässig.</li>
    </ul>

    <h2>§ 6 Pflichten des Mieters</h2>
    <ul>
        <li>Sorgfältiger Umgang mit der Wohnung und den Gemeinschaftsflächen.</li>
        <li>Schäden und Mängel sind unverzüglich zu melden.</li>
        <li>Die Hausordnung ist zu beachten, sofern eine solche besteht oder ergänzend vereinbart wird.</li>
    </ul>

    <h2>§ 7 Betriebskosten</h2>
    <p>Die Betriebskosten werden gemäß Vereinbarung als monatliche Vorauszahlung erhoben.</p>
    <p>Die Abrechnung erfolgt nach den gesetzlichen Vorschriften.</p>

    <h2>§ 8 Instandhaltung / Kleinreparaturen</h2>
    <p>Der Mieter hat Mängel unverzüglich mitzuteilen.</p>
    <p>Kleinreparaturen erfolgen nur im gesetzlich zulässigen Rahmen und soweit ausdrücklich vereinbart.</p>

    <h2>§ 9 Datenschutz</h2>
    <p>Personenbezogene Daten werden nur zur Durchführung des Mietverhältnisses verarbeitet.</p>
    <p>Eine Weitergabe erfolgt nur soweit erforderlich, beispielsweise an die Verwaltung, beauftragte Dienstleister oder Steuerberater.</p>

    <h2>§ 10 Sonstige Vereinbarungen</h2>
    @if (! empty($additional_notes))
        <p class="dyn" style="white-space: pre-wrap;">{{ $additional_notes }}</p>
    @else
        <p>Es bestehen keine weiteren Nebenabreden.</p>
    @endif

    <div class="box-meta">
        <p><strong>Nachweis / Bearbeitungsstand (keine qualifizierte elektronische Signatur)</strong></p>
        <p><strong>Vertragsstatus:</strong> {{ $vertragsstatusLabel }}</p>
        <p><strong>PDF erzeugt am:</strong> {{ $pdfGeneratedAt->format('d.m.Y') }}</p>
        @if ($vertrag->accepted_at)
            <p><strong>Bestätigt am:</strong> {{ $vertrag->accepted_at->format('d.m.Y H:i') }}</p>
            <p>Dieser Vertrag wurde über den digitalen Mieterlink bestätigt.</p>
            @if ($acceptedAuditEvent)
                <p class="muted"><strong>Technische Angaben zur Bestätigung (protokollarisch):</strong>
                    IP: {{ $acceptedAuditEvent->ip_address ?? '—' }}<br>
                    User-Agent: {{ $uaKurz ?? '—' }}</p>
            @endif
        @endif
        <p class="muted">Dieses Dokument dient der Dokumentation des vereinbarten Inhalts und ersetzt keine Rechtsberatung.</p>
    </div>

    <div class="sig-wrap">
        <h2>Unterschriften (Vereinbarung)</h2>
        <table class="sig-table">
            <tr>
                <td>
                    Vermieter / Verwaltung<br><br>
                    Ort, Datum: ____________________<br><br>
                    Unterschrift: ____________________
                </td>
                <td>
                    Mieter<br><br>
                    Ort, Datum: ____________________<br><br>
                    Unterschrift: ____________________
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Vorlage: Wohnraummietvertrag v1.0</p>
        <p class="muted">Erstellt mit der Software. Inhaltliche Prüfung durch den Vermieter / die Verwaltung ist erforderlich.</p>
    </div>
</body>
</html>
