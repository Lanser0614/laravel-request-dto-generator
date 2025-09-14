<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class CreateCouponRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code',
            'discountType' => 'required|string|in:percentage,fixed',
            'discountValue' => 'required|numeric|min:0',
            'minOrderAmount' => 'nullable|numeric|min:0',
            'maxDiscountAmount' => 'nullable|numeric|min:0',
            'usageLimit' => 'nullable|integer|min:1',
            'expiresAt' => 'nullable|date|after:now',
            'isActive' => 'boolean',
            'description' => 'nullable|string|max:500',
            'applicableProducts' => 'array',
            'applicableProducts.*.productId' => 'required|uuid',
            'applicableProducts.*.category' => 'required|string',
            'applicableProducts.*.price' => 'required|numeric|min:0',
            'customer' => 'required|array',
            'customer.id' => 'required|uuid',
            'customer.email' => 'required|email',
            'customer.name' => 'required|string|max:255',
            'customer.tier' => 'required|string|in:bronze,silver,gold,platinum',
        ];
    }
}
