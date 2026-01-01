<div class="max-w-4xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold">My Orders</h1>

    @forelse ($orders as $order)
        <div class="border rounded-lg p-4 bg-white">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold">Order #{{ $order->id }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $order->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>
                <div class="text-lg font-bold">
                    ${{ number_format($order->total_amount, 2) }}
                </div>
            </div>

            <div class="border-t pt-3 space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <div>
                            <p class="font-medium">
                                {{ $item->product->name }}
                            </p>
                            <p class="text-gray-500">
                                Qty {{ $item->quantity }} x ${{ number_format($item->price_at_purchase, 2) }}
                            </p>
                        </div>

                        <div class="font-medium">
                            ${{ number_format($item->price_at_purchase * $item->quantity, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-gray-500">You have not placed any orders yet.</p>
    @endforelse
</div>
