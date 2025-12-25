<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function rules()
{
    return [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:255|unique:products,sku',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'image' => 'nullable|string', // base64
    ];
}


}
