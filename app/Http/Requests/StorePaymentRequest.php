<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'payment_type_id' => ['required', 'exists:payment_types,id'],
            'payer_name' => ['required', 'string', 'max:255'],
            'purpose' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'in:cash,card,transfer,p2p'], // Made optional for backward compatibility
            'currency' => ['required', 'in:UZS,USD'],
            'list_number' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'cash_back' => ['nullable', 'numeric', 'min:0'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'payment_system' => ['nullable', 'string', 'in:uzcard,humo,visa,mastercard'],
            // Multiple payment methods support
            'method_details' => ['required', 'array', 'min:1'],
            'method_details.*.method' => ['required', 'in:cash,card,transfer,p2p'],
            'method_details.*.amount' => ['required', 'numeric', 'min:0.01'],
            'method_details.*.payment_system' => ['nullable', 'string', 'in:uzcard,humo,visa,mastercard,unionpay'],
            'method_details.*.reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Perform additional validation after rules pass.
     */
    public function after(): array
    {
        return [
            function ($validator) {
                // Validate that sum of method_details equals total with commission
                if ($this->has('method_details') && is_array($this->method_details)) {
                    $methodsSum = array_sum(array_column($this->method_details, 'amount'));

                    // Get payment type to calculate total
                    $paymentType = \App\Models\PaymentType::find($this->payment_type_id);
                    if ($paymentType) {
                        $commission = $paymentType->calculateCommission($this->amount);
                        $expectedTotal = $this->amount + $commission;

                        // Allow small rounding difference (0.01)
                        if (abs($methodsSum - $expectedTotal) > 0.01) {
                            $validator->errors()->add(
                                'method_details',
                                sprintf(
                                    'Сумма методов оплаты (%.2f) должна равняться итоговой сумме с комиссией (%.2f)',
                                    $methodsSum,
                                    $expectedTotal
                                )
                            );
                        }
                    }
                }
            }
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
            'payment_type_id.required' => 'Тип платежа обязателен для заполнения',
            'payment_type_id.exists' => 'Выбранный тип платежа не существует',
            'payer_name.required' => 'Имя плательщика обязательно для заполнения',
            'payer_name.string' => 'Имя плательщика должно быть строкой',
            'payer_name.max' => 'Имя плательщика не должно превышать 255 символов',
            'purpose.string' => 'Назначение платежа должно быть строкой',
            'amount.required' => 'Сумма обязательна для заполнения',
            'amount.numeric' => 'Сумма должна быть числом',
            'amount.min' => 'Сумма не может быть отрицательной',
            'payment_method.required' => 'Способ оплаты обязателен для заполнения',
            'payment_method.in' => 'Способ оплаты должен быть наличными, картой или переводом',
            'currency.required' => 'Валюта обязательна для заполнения',
            'currency.in' => 'Валюта должна быть UZS или USD',
            'list_number.string' => 'Номер списка должен быть строкой',
        ];
    }
}
