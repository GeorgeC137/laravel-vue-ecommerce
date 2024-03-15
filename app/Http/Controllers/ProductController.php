<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->where('published', '=', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('product.index', [
            'products' => $products
        ]);
    }

    public function byCategory(Category $category)
    {
        $products = Product::query()
            ->select('products.*')
            ->join('product_categories AS pc', 'pc.product_id', '=', 'products.id')
            ->where('published', '=', 1)
            ->where('pc.category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('product.index', [
            'products' => $products
        ]);
    }

    public function show(Product $product)
    {
        return view('product.show', [
            'product' => $product
        ]);
    }
}
