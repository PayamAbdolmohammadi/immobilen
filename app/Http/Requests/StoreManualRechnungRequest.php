<?php

namespace App\Http\Requests;

use App\Models\Rechnung;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualRechnungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Rechnung::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $mandantId = (int) $this->user()->mandant_id;

        return [
            'mieter_id' => [
                'required',
                'integer',
                Rule::exists('mieter', 'id')->where('mandant_id', $mandantId),
            ],
            'einheit_id' => [
                'nullable',
                'integer',
                Rule::exists('einheiten', 'id')->where('mandant_id', $mandantId),
            ],
            'betrag' => ['required', 'numeric', 'min:0.01'],
            'faellig_am' => ['nullable', 'date'],
            'status' => ['required', Rule::in([Rechnung::STATUS_OFFEN, Rechnung::STATUS_BEZAHLT])],
            'bezahlt_am' => ['nullable', 'date'],
        ];
    }

    public function betragCent(): int
    {
        return (int) round(((float) $this->validated('betrag')) * 100);
    }
}
