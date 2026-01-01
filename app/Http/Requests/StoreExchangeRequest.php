<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExchangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usd_amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:buy,sell'],
            'rate_id' => ['nullable', 'exists:rates,id'],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'usd_amount.required' => 'Сумма в долларах обязательна для заполнения',
            'usd_amount.numeric' => 'Сумма в долларах должна быть числом',
            'usd_amount.min' => 'Сумма в долларах не может быть отрицательной',
            'type.required' => 'Тип обмена обязателен для заполнения',
            'type.in' => 'Тип обмена должен быть покупка или продажа',
            'rate_id.exists' => 'Выбранный курс не существует',
        ];
    }
}
