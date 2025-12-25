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
    // data:image/png;base64,xxxx
    if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
        throw new \Exception('Invalid image format');
    }

    $extension = $type[1];
    $image = substr($base64, strpos($base64, ',') + 1);
    $image = base64_decode($image);

    if ($image === false) {
        throw new \Exception('Base64 decode failed');
    }

    $fileName = 'products/' . Str::uuid() . '.' . $extension;

    Storage::disk('public')->put($fileName, $image);

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
        // delete old image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $data['image'] = $this->saveBase64Image($data['image']);
    }
    $product->update($data);
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
