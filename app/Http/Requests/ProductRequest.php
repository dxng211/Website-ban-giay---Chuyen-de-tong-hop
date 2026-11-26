<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $productId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'description' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'in:male,female,unisex'],
            'has_sizes' => ['nullable'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
            'image' => ['nullable', 'image', 'max:4096'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'variants' => ['nullable', 'array'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
