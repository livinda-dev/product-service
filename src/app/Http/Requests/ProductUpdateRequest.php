<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{

    // public function authorize(): bool
    // {
    //     return true;
    // }
    public function rules()
{
    return [
        'name' => 'sometimes|string|max:255',
        'sku' => 'sometimes|string|max:255|unique:products,sku,' . $this->product,
        'description' => 'nullable|string',
        'price' => 'sometimes|numeric|min:0',
        'stock' => 'sometimes|integer|min:0',
        'is_active' => 'sometimes|boolean',
        'image_url' => 'nullable|string',
    ];
}


}
