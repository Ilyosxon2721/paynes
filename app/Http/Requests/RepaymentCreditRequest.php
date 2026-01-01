<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepaymentCreditRequest extends FormRequest
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
            'credit_id' => ['required', 'exists:credits,id'],
            'amount' => ['required', 'numeric', 'min:0'],
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
            'credit_id.required' => 'Кредит обязателен для заполнения',
            'credit_id.exists' => 'Выбранный кредит не существует',
            'amount.required' => 'Сумма обязательна для заполнения',
            'amount.numeric' => 'Сумма должна быть числом',
            'amount.min' => 'Сумма не может быть отрицательной',
        ];
    }
}
