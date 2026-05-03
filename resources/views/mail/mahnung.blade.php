<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Payment reminder') }}</title>
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #111827;">
    <p>{{ __('Hello') }},</p>
    <p>
        {{ __('Our records show invoice #:id is overdue.', ['id' => $rechnung->id]) }}
        {{ __('Amount: :amount €', ['amount' => number_format($rechnung->openAmountCent() / 100, 2, ',', '.')]) }}
        @if ($rechnung->faellig_am)
            — {{ __('Due date: :date', ['date' => $rechnung->faellig_am->format('Y-m-d')]) }}
        @endif
    </p>
    <p>{{ __('Please arrange payment at your earliest convenience.') }}</p>
    <p style="margin-top: 2rem; color: #6b7280; font-size: 0.875rem;">
        {{ __('Regards') }},<br>
        {{ config('app.name') }}
    </p>
</body>
</html>
