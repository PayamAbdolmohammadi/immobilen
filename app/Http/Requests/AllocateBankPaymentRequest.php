<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AllocateBankPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->mandant_id !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $mandantId = (int) $this->user()->mandant_id;

        return [
            'rechnung_id' => [
                'required',
                'integer',
                Rule::exists('rechnungen', 'id')->where('mandant_id', $mandantId),
            ],
            'betrag' => ['required', 'numeric', 'min:0.01'],
            'return_to_demo' => ['nullable', 'string', Rule::in(['1'])],
            'demo' => ['nullable', 'string', Rule::in(['1'])],
        ];
    }

    public function amountCent(): int
    {
        return (int) round(((float) $this->validated('betrag')) * 100);
    }
}
