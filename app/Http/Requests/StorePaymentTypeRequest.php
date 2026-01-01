<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'bank' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'mfo' => ['nullable', 'string', 'max:9'],
            'inn' => ['nullable', 'string', 'max:9'],
            'treasury_account' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'commission_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'commission_fixed' => ['nullable', 'numeric', 'min:0'],
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
            'name.required' => 'Название обязательно для заполнения',
            'name.string' => 'Название должно быть строкой',
            'name.max' => 'Название не должно превышать 255 символов',
            'organization.string' => 'Организация должна быть строкой',
            'organization.max' => 'Организация не должна превышать 255 символов',
            'bank.string' => 'Банк должен быть строкой',
            'bank.max' => 'Банк не должен превышать 255 символов',
            'account_number.string' => 'Номер счета должен быть строкой',
            'account_number.max' => 'Номер счета не должен превышать 255 символов',
            'mfo.string' => 'МФО должно быть строкой',
            'mfo.max' => 'МФО не должно превышать 9 символов',
            'inn.string' => 'ИНН должен быть строкой',
            'inn.max' => 'ИНН не должен превышать 9 символов',
            'treasury_account.string' => 'Казначейский счет должен быть строкой',
            'treasury_account.max' => 'Казначейский счет не должен превышать 255 символов',
            'type.string' => 'Тип должен быть строкой',
            'type.max' => 'Тип не должен превышать 255 символов',
            'commission_percentage.numeric' => 'Процент комиссии должен быть числом',
            'commission_percentage.min' => 'Процент комиссии не может быть отрицательным',
            'commission_percentage.max' => 'Процент комиссии не может превышать 100',
            'commission_fixed.numeric' => 'Фиксированная комиссия должна быть числом',
            'commission_fixed.min' => 'Фиксированная комиссия не может быть отрицательной',
        ];
    }
}
