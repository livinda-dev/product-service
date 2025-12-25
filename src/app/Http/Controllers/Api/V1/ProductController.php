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


    private function saveBase64Image(string $base64): string
{
    // Remove whitespace and newlines
    $base64 = preg_replace('/\s+/', '', $base64);

    // Match data URI
    if (!preg_match('/^data:image\/([a-zA-Z0-9+]+);base64,/', $base64, $matches)) {
        throw new \Exception('Invalid image data');
    }

    $extension = strtolower($matches[1]);

    $data = substr($base64, strpos($base64, ',') + 1);
    $data = base64_decode($data, true);

    if ($data === false) {
        throw new \Exception('Base64 decode failed');
    }

    $fileName = 'products/' . Str::uuid() . '.' . $extension;

    Storage::disk('public')->put($fileName, $data);

    return $fileName;
}


    public function index()
    {
        return response()->json(
            Product::orderBy('created_at', 'desc')->paginate(10)
        );
    }

    public function store(ProductStoreRequest $request)
{
    $data = $request->validated();

    if (!empty($data['image'])) {
    try {
        $data['image'] = $this->saveBase64Image($data['image']);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Invalid image data'
        ], 422);
    }
}


    $product = Product::create($data);

    return response()->json($product, 201);
}



    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(ProductUpdateRequest $request, Product $product)
{
    $data = $request->validated();
    if (!empty($data['image'])) {
        // delete old image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $data['image'] = $this->saveBase64Image($data['image']);
    }
    $product->update($data);
    return response()->json($product);

}



    public function destroy(Product $product)
    {
        // Delete image on product delete
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
