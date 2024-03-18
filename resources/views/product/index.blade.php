<?php
$categoryList = App\Models\Category::getActiveAsTree();
?>

<x-app-layout>
    <x-category-list :category-list="$categoryList" class="-ml-5 -mt-5 px-4 -mr-5" />

    <div class="p-3 pb-0 flex items-center gap-2" x-data="{
        selectedSort: '{{ request()->get('sort', '-updated_at') }}',
        searchKeyword: '{{ request()->get('search') }}',
        updateUrl() {
            const params = new URLSearchParams(window.location.search)
            if (this.selectedSort && this.selectedSort !== '-updated_at') {
                params.set('sort', this.selectedSort)
            } else {
                params.delete('sort')
            }
            if (this.searchKeyword) {
                params.set('search', this.searchKeyword)
            } else {
                params.delete('search')
            }
            window.location.href = window.location.origin + window.location.pathname + '?' + params.toString();
        }
    }">
        <form action="" method="GET" class="flex-1" @submit.prevent="updateUrl">
            <x-text-input type="text" name="search" x-model="searchKeyword"
                placeholder="Search products..." />
        </form>
        <x-text-input type="select" @change="updateUrl" x-model="selectedSort" name="sort"
            class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
            <option value="price">Price (ASC)</option>
            <option value="-price">Price (DESC)</option>
            <option value="title">Title (ASC)</option>
            <option value="-title">Title (DESC)</option>
            <option value="-updated_at">Last Modified at the top</option>
            <option value="updated_at">Last Modified at the bottom</option>
        </x-text-input>
    </div>

    @if ($products->count() === 0)
        <div class="text-center text-gray-600 py-16 text-xl">
            There are no products published
        </div>
    @else
        <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-3">
            @foreach ($products as $product)
                <!-- Product Item -->
                <div x-data="productItem({{ json_encode([
                    'id' => $product->id,
                    'title' => $product->title,
                    'price' => $product->price,
                    'image' => $product->image ?: '/img/no-image.jpg',
                    'addToCartUrl' => route('cart.add', $product->slug),
                ]) }})"
                    class="border border-1 border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white">
                    <a href="{{ route('product.show', $product->slug) }}"
                        class="block overflow-hidden aspect-w-3 aspect-h-2">
                        <img src="{{ $product->image ?: '/img/no-image.jpg' }}" alt=""
                            class="rounded-lg hover:scale-105 hover:rotate-1 transition-transform object-cover" />
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg">
                            <a href="{{ route('product.show', $product->slug) }}">
                                {{ $product->title }}
                            </a>
                        </h3>
                        <h5 class="font-bold">${{ $product->price }}</h5>
                    </div>
                    <div class="flex justify-between py-3 px-4">
                        <button class="btn-primary" @click="addToCart()">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <!--/ Product Item -->
            @endforeach
        </div>

        {{ $products->appends(['sort' => request('sort'), 'search' => request('search')])->links() }}
    @endif
</x-app-layout>
