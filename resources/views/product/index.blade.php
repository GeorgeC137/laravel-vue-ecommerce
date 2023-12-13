<x-app-layout>
    <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-5">
        @foreach ($products as $product)
            <!-- Product Item -->
            <div x-data="productItem({
                id: 1,
                image: 'img/1_1.jpg',
                title: 'Logitech G502 HERO High Performance Wired Gaming Mouse, HERO 25K Sensor, 25,600 DPI, RGB, Adjustable Weights, 11',
                price: 17.99
            })"
                class="border border-1 border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white">
                <a href="{{ route('products.show', $product->slug) }}" class="block overflow-hidden aspect-w-3 aspect-h-2">
                    <img src="{{ $product->image }}" alt=""
                        class="rounded-lg hover:scale-105 hover:rotate-1 transition-transform object-cover" />
                </a>
                <div class="p-4">
                    <h3 class="text-lg">
                        <a href="{{ route('products.show', $product->slug) }}">
                            {{ $product->title }}
                        </a>
                    </h3>
                    <h5 class="font-bold">${{ $product->price }}</h5>
                </div>
                <div class="flex justify-between py-3 px-4">
                    <button @click="addToWatchlist()"
                        class="w-10 h-10 rounded-full border border-1 border-purple-600 flex items-center justify-center hover:bg-purple-600 hover:text-white active:bg-purple-800 transition-colors"
                        :class="isInWatchlist(id) ? 'bg-purple-600 text-white' : 'text-purple-600'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                    <button class="btn-primary" @click="addToCart(id)">
                        Add to Cart
                    </button>
                </div>
            </div>
            <!--/ Product Item -->
        @endforeach
    </div>

    {{ $products->links() }}
</x-app-layout>
