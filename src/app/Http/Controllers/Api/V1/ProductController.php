<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Storage;

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
    $data = $request->validated();

    if (!empty($data['image'])) {
        $data['image'] = $this->saveBase64Image($data['image']);
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

        // Delete old image
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }

        $data['image'] = $this->saveBase64Image($data['image']);
    }

    $product->update($data);

    return response()->json($product);
}


    private function saveBase64Image(string $base64, string $folder = 'products'): string
{
    // Check if image has data:image header
    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
        $base64 = substr($base64, strpos($base64, ',') + 1);
        $extension = $type[1]; // png, jpg, jpeg
    } else {
        // Default extension if header missing
        $extension = 'png';
    }

    $imageData = base64_decode($base64);

    if ($imageData === false) {
        throw new \Exception('Invalid base64 image');
    }

    $fileName = $folder . '/' . uniqid() . '.' . $extension;

    \Storage::disk('public')->put($fileName, $imageData);

    return $fileName;
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
