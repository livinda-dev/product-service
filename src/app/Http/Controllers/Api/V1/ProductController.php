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
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            throw new \Exception('Invalid image data');
        }

        $extension = strtolower($matches[1]);
        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'webp'])) {
            throw new \Exception('Unsupported image type');
        }

        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $binary = base64_decode($base64);

        if ($binary === false) {
            throw new \Exception('Invalid image data');
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
    $product = Product::create($request->validated());

    return response()->json($product, 201);
}






    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(ProductUpdateRequest $request, Product $product)
{
    $data = $request->validated();
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
