@php
    $r = $rechnung ?? null;
    $betragOld = old('betrag', $r ? number_format($r->betrag_cent / 100, 2, '.', '') : '');
@endphp

<div class="space-y-6">
    <div>
        <x-input-label for="mieter_id" :value="__('Tenant')" />
        <select id="mieter_id" name="mieter_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="">{{ __('Choose…') }}</option>
            @foreach ($mieter as $m)
                <option value="{{ $m->id }}" @selected((int) old('mieter_id', $r?->mieter_id) === $m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('mieter_id')" />
    </div>

    <div>
        <x-input-label for="einheit_id" :value="__('Unit (optional)')" />
        <select id="einheit_id" name="einheit_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">{{ __('None') }}</option>
            @foreach ($einheiten as $e)
                <option value="{{ $e->id }}" @selected((int) old('einheit_id', $r?->einheit_id) === $e->id)>{{ $e->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('einheit_id')" />
    </div>

    <div>
        <x-input-label for="betrag" :value="__('Amount (EUR)')" />
        <x-text-input id="betrag" name="betrag" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="$betragOld" required />
        <x-input-error class="mt-2" :messages="$errors->get('betrag')" />
    </div>

    <div>
        <x-input-label for="faellig_am" :value="__('Due date')" />
        <x-text-input id="faellig_am" name="faellig_am" type="date" class="mt-1 block w-full" :value="old('faellig_am', $r?->faellig_am?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('faellig_am')" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="{{ \App\Models\Rechnung::STATUS_OFFEN }}" @selected(old('status', $r?->status ?? \App\Models\Rechnung::STATUS_OFFEN) === \App\Models\Rechnung::STATUS_OFFEN)>{{ __('Open') }}</option>
            <option value="{{ \App\Models\Rechnung::STATUS_BEZAHLT }}" @selected(old('status', $r?->status) === \App\Models\Rechnung::STATUS_BEZAHLT)>{{ __('Paid') }}</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <div>
        <x-input-label for="bezahlt_am" :value="__('Paid at (when status is paid)')" />
        <x-text-input id="bezahlt_am" name="bezahlt_am" type="datetime-local" class="mt-1 block w-full" :value="old('bezahlt_am', $r?->bezahlt_am?->format('Y-m-d\TH:i'))" />
        <x-input-error class="mt-2" :messages="$errors->get('bezahlt_am')" />
    </div>
</div>
