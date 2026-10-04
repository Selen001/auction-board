<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSeller() ?? false;
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'base_price' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'closes_at' => ['required', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'closes_at.after' => 'The closing date and time must be in the future.',
        ];
    }
}