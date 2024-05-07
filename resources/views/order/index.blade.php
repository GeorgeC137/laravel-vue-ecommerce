<x-app-layout>
    <div class="container lg:w-2/3 xl:w-2/3 mx-auto">
        <h1 class="text-3xl font-bold mb-6">My Orders</h1>

        <div class="bg-white p-3 rounded-md shadow-md">
            <table class="table table-auto w-full">
                <thead class="border-b-2">
                    <tr class="text-left">
                        <th>Order</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Items</th>
                        <th class="w-64">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-b">
                            <td>
                                <a href="{{ route('order.view', $order) }}" class="text-purple-600 hover:text-purple-500">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td>{{ $order->created_at }}</td>
                            <td>
                                <small
                                    class=" text-white p-1 rounded {{ $order->isPaid() ? 'bg-emerald-500' : 'bg-gray-500' }}">{{ $order->status }}</small>
                            </td>
                            <td>KSH {{ $order->total_price }}</td>
                            <td class="whitespace-nowrap">{{ $order->items_count }} items(s)</td>
                            <td class="flex gap-3">
                                @if (!$order->isPaid())
                                    <form action="{{ route('cart.checkout-order', $order) }}" method="POST">
                                        @csrf
                                        <button class="btn-primary py-1 px-2 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Pay
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
