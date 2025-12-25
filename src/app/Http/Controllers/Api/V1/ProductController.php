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
    // Remove data:image/...;base64, if present
    if (str_contains($base64, ',')) {
        [, $base64] = explode(',', $base64, 2);
    }

    // Fix spaces turned from "+"
    $base64 = str_replace(' ', '+', $base64);

    // Decode
    $binary = base64_decode($base64);

    if ($binary === false) {
        throw new \Exception('Invalid image data');
    }

    // Detect image type from binary
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_buffer($finfo, $binary);
    finfo_close($finfo);

    $extension = match ($mime) {
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/webp' => 'webp',
        default      => throw new \Exception('Unsupported image type'),
    };

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
