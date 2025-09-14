<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
            'isActive' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|uuid',
            'items.*.productName' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.totalPrice' => 'required|numeric|min:0',
        ];
    }
}
