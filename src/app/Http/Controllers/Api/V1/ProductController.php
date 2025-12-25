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
    // Remove whitespace & newlines
    $base64 = preg_replace('/\s+/', '', $base64);

    // Must contain comma
    if (!str_contains($base64, ',')) {
        throw new \Exception('Invalid image data');
    }

    [$meta, $data] = explode(',', $base64, 2);

    // Validate mime
    if (!str_starts_with($meta, 'data:image/') || !str_contains($meta, ';base64')) {
        throw new \Exception('Invalid image data');
    }

    $extension = str_replace(
        ['data:image/', ';base64'],
        '',
        $meta
    );

    $extension = strtolower($extension);

    // Decode strictly
    $binary = base64_decode($data, true);

    if ($binary === false) {
        throw new \Exception('Base64 decode failed');
    }

    $fileName = 'products/' . Str::uuid() . '.' . $extension;

    Storage::disk('public')->put($fileName, $binary);

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
