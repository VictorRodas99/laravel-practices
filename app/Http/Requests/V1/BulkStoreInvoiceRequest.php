<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

class BulkStoreInvoiceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "*.customerId" => ["required", "integer"],
            "*.amount" => ["required", "numeric"],
            "*.status" => ["required", Rule::in(['B', 'b', 'P', 'p', 'V', 'v'])],
            "*.billedDate" => ["required", "date_format:Y-m-d H:i:s"],
            "*.paidDate" => ["date_format:Y-m-d H:i:s", "nullable"]
        ];
    }

    /**
     * This function work as a parser for the received data (the name of this function must not be changed)
     */
    protected function prepareForValidation()
    {
        // This occurs before the validation
        $data = [];

        foreach ($this->toArray() as $request_body) {
            $request_body['customer_id'] = $request_body['customerId'] ?? null;
            $request_body['billed_date'] = $request_body['billedDate'] ?? null;
            $request_body['paid_date'] = $request_body['paidDate'] ?? null;

            $data[] = $request_body;
        }

        $data = array_map(
            (fn ($payload) => Arr::except($payload, ['customerId', 'billedDate', 'paidDate'])),
            $data
        );

        $this->merge($data);
    }
}
