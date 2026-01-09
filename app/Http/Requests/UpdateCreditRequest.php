<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreditRequest extends FormRequest
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
            'recipient' => ['nullable', 'string', 'max:255'],
            'branch' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric', 'min:0'],
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
            'recipient.string' => 'Получатель должен быть строкой.',
            'recipient.max' => 'Получатель не должен превышать 255 символов.',
            'branch.string' => 'Филиал должен быть строкой.',
            'amount.numeric' => 'Сумма должна быть числом.',
            'amount.min' => 'Сумма не должна быть меньше 0.',
        ];
    }
}

