<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SentCouponRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'couponCode' => 'required|string|max:50',
            'discountAmount' => 'required|numeric|min:0',
            'discountType' => 'required|string|in:percentage,fixed',
            'isActive' => 'boolean',
            'expiresAt' => 'nullable|date|after:now',
            'usageLimit' => 'nullable|integer|min:1',
            'usedCount' => 'integer|min:0',
        ];
    }
}
