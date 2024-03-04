<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductListResource;
use App\Models\ProductImage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search', false);
        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Product::query()
            ->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return ProductListResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by'] = $request->user()->id;
        $validatedData['updated_by'] = $request->user()->id;

        $images = $validatedData['images'] ?? [];
        $imagePositions = $validatedData['image_positions'] ?? [];

        $product = Product::create($validatedData);

        $this->saveImages($images, $imagePositions, $product);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $validatedData = $request->validated();

        $validatedData['updated_by'] = $request->user()->id;

        $images = $validatedData['images'] ?? [];
        $deletedImages = $validatedData['deleted_images'] ?? [];
        $imagePositions = $validatedData['image_positions'] ?? [];

        $this->saveImages($images, $imagePositions, $product);

        if (count($deletedImages) > 0) {
            $this->deleteImages($deletedImages, $product);
        }

        $product->update($validatedData);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }

    private function saveImages($images, $positions, Product $product)
    {
        foreach ($positions as $id => $position) {
            ProductImage::query()
                ->where('id', $id)
                ->update(['position' => $position]);
        }

        foreach ($images as $id => $image) {
            $path = 'images/' . Str::random();

            if (!Storage::exists($path)) {
                Storage::makeDirectory($path, 0755, true);
            }

            $name = Str::random() . '.' . $image->getClientOriginalExtension();

            if (!Storage::putFileAs('public/' . $path, $image, $name)) {
                throw new Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
            }

            $relativePath = $path . '/' . $name;

            ProductImage::create([
                'product_id' => $product->id,
                'url' => URL::to(Storage::url($relativePath)),
                'path' => $relativePath,
                'mime' => $image->getClientMimeType(),
                'size' => $image->getSize(),
                'position' => $positions[$id] ?? $id + 1,
            ]);
        }
    }

    private function deleteImages($imageIds, Product $product)
    {
        $images = ProductImage::query()
            ->where('product_id', $product->id)
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($images as $image) {
            // Delete old image if it exists
            if ($image->path) {
                Storage::deleteDirectory('/public/' . dirname($image->path));
            }

            $image->delete();
        }
    }
}
