<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{


    public function index()
    {
        return response()->json(
            Product::orderBy('created_at', 'desc')->paginate(10)
        );
    }

    public function store(ProductStoreRequest $request)
{
    $validatedData = $request->validated();
    $images = $validatedData['images'];
    unset($validatedData['images']);

    $product = Product::create($validatedData);

    foreach ($images as $base64Image) {
        $imagePath = $this->saveBase64Image($base64Image);
        $product->images()->create(['image_path' => $imagePath]);
    }

    return response()->json($product->load('images'), 201);
}

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(ProductUpdateRequest $request, Product $product)
{
    $validatedData = $request->validated();
    $images = $validatedData['images'];
    unset($validatedData['images']);

    // Delete old images
    foreach ($product->images as $image) {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
    }

    $product->update($validatedData);

    // Add new images
    foreach ($images as $base64Image) {
        $imagePath = $this->saveBase64Image($base64Image);
        $product->images()->create(['image_path' => $imagePath]);
    }

    return response()->json($product->load('images'));
}

    public function destroy(Product $product)
    {
        // Delete images on product delete
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

