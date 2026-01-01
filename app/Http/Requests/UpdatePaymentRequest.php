<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'payment_type_id' => ['nullable', 'exists:payment_types,id'],
            'payer_name' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,card'],
            'currency' => ['nullable', 'in:UZS,USD'],
            'list_number' => ['nullable', 'string'],
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
            'payment_type_id.exists' => 'Выбранный тип платежа не существует',
            'payer_name.string' => 'Имя плательщика должно быть строкой',
            'payer_name.max' => 'Имя плательщика не должно превышать 255 символов',
            'purpose.string' => 'Назначение платежа должно быть строкой',
            'amount.numeric' => 'Сумма должна быть числом',
            'amount.min' => 'Сумма не может быть отрицательной',
            'payment_method.in' => 'Способ оплаты должен быть наличными или картой',
            'currency.in' => 'Валюта должна быть UZS или USD',
            'list_number.string' => 'Номер списка должен быть строкой',
        ];
    }
}
