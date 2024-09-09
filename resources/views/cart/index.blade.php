<x-app-layout>
    <div class="container mx-auto p-5 lg:w-2/3">
        <h1 class="text-3xl font-bold mb-2">My Cart Items</h1>

        @if (session('error'))
            <div class="bg-red-500 py-2 px-3 text-white mb-2 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div x-data="{
            cartItems: {{ json_encode(
                $products->map(
                    fn($product) => [
                        'id' => $product->id,
                        'title' => $product->title,
                        'image' => $product->image,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'quantity' => $cartItems[$product->id]['quantity'],
                        'href' => route('product.show', $product->slug),
                        'removeUrl' => route('cart.remove', $product->slug),
                        'updateQuantityUrl' => route('cart.update-quantity', $product->slug),
                    ],
                ),
            ) }},
            get cartTotal() {
                return this.cartItems.reduce((accum, next) => accum + next.price * next.quantity, 0).toFixed(2)
            }
        }" class="bg-white rounded-lg p-3">
            <template x-if="cartItems.length">
                <div>
                    <template x-for="product of cartItems" :key="product.id">
                        <div x-data="productItem(product)">
                            {{-- Cart Item  --}}
                            <div class="flex flex-col sm:flex-row items-center gap-4">
                                <a :href="product.href"
                                    class="flex items-center overflow-hidden justify-center w-32 h-32">
                                    <img :src="product.image" alt="" class="object-cover">
                                </a>
                                <div class="flex flex-col justify-between flex-1">
                                    <div class="flex justify-between mb-3">
                                        <h3 x-text="product.title">
                                        </h3>
                                        <span class="text-lg font-semibold" x-text="`KSH ${product.price}`"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            Qty:
                                            <input x-model="product.quantity" @change="changeQuantity()" min="1"
                                                type="number"
                                                class="ml-3 py-1 border-gray-200 focus:border-purple-600 focus:ring-purple-600 w-16" />
                                        </div>
                                        <a href="#" @click.prevent="removeItemFromCart()"
                                            class="text-purple-600 hover:text-purple-500">Remove</a>
                                    </div>
                                </div>
                            </div>
                            {{-- Cart Item  --}}
                            <hr class="my-3">
                        </div>
                    </template>

                    <div class="border-t border-gray-300 mt-5 pt-5">
                        <div class="flex justify-between">
                            <span class="font-bold">Subtotal</span>
                            <span x-text="`KSH ${cartTotal}`"></span>
                        </div>
                        <p>Shipping and tax will be applied on checkout</p>
                    </div>

                    <form action="{{ route('cart.mpesa-checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full mt-3 py-3">Proceed To Checkout</button>
                    </form>

                </div>
            </template>

            <template x-if="!cartItems.length">
                <div class="text-gray-500 text-center py-8">
                    You don't have any items in cart
                </div>
            </template>
        </div>
    </div>
</x-app-layout>
