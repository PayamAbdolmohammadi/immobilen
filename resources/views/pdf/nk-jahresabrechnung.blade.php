<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .muted { color: #6b7280; font-size: 10px; margin-top: 16px; }
        .sum { font-weight: bold; }
    </style>
    <title>Jahresabrechnung NK</title>
</head>
<body>
    <h1>{{ __('Annual NK settlement') }}</h1>
    <p><strong>{{ __('Property') }}:</strong> {{ $abrechnung->objekt->name }}</p>
    <p><strong>{{ __('Year') }}:</strong> {{ $abrechnung->jahr }}</p>
    <p><strong>{{ __('Distribution') }}:</strong> {{ $abrechnung->verteilungs_art }}</p>
    <p><strong>{{ __('Status') }}:</strong> {{ $abrechnung->status }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($abrechnung->positionen as $p)
                <tr>
                    <td>{{ $p->beschreibung }}</td>
                    <td>{{ number_format($p->betrag_cent / 100, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="sum">
                <td>{{ __('Total costs') }}</td>
                <td>{{ number_format($totalCent / 100, 2, ',', '.') }} €</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 12px;">
        <strong>{{ __('Units') }}:</strong> {{ $einheitenCount }} &mdash;
        <strong>{{ __('Share per unit (equal)') }}:</strong> {{ number_format($anteilCent / 100, 2, ',', '.') }} €
    </p>

    <p class="muted">
        {{ __('Invoice snapshot disclaimer: advance payments and tenant-specific adjustments are not included in this MVP.') }}
        {{ config('app.name') }} — {{ now()->format('Y-m-d') }}
    </p>
</body>
</html>
