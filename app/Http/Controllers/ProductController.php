<?php

namespace App\Http\Controllers;

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

    public function show(Product $product)
    {
        return view('product.show', [
            'product' => $product
        ]);
    }
}
