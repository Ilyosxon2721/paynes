<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRateRequest extends FormRequest
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
            'buy_rate' => ['required', 'numeric', 'min:0'],
            'sell_rate' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
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
            'buy_rate.required' => 'Курс покупки обязателен для заполнения',
            'buy_rate.numeric' => 'Курс покупки должен быть числом',
            'buy_rate.min' => 'Курс покупки не может быть отрицательным',
            'sell_rate.required' => 'Курс продажи обязателен для заполнения',
            'sell_rate.numeric' => 'Курс продажи должен быть числом',
            'sell_rate.min' => 'Курс продажи не может быть отрицательным',
            'date.required' => 'Дата обязательна для заполнения',
            'date.date' => 'Дата должна быть корректной датой',
        ];
    }
}
