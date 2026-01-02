<div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- PRODUCTS --}}
    <div class="lg:col-span-2">
        <h2 class="text-2xl font-bold mb-4">Products</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($products as $product)
                <div class="border rounded p-4 flex flex-col justify-between">
                    <div>
                        <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                        <p class="text-gray-600">${{ number_format($product->price, 2) }}</p>
                        <p class="text-sm text-gray-500">
                            Stock: {{ $product->stock_quantity }}
                        </p>
                    </div>

                    <flux:button 
                        variant="primary" 
                        type="submit" 
                        class="px-4 py-2 w-full mt-4 cursor-pointer" 
                        wire:click="addToCart({{ $product->id }})"
                    >
                        Add to Cart
                    </flux:button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- CART --}}
    <div class="lg:sticky lg:top-10 self-start">
        <h2 class="text-2xl font-bold mb-4">Your Cart</h2>

        @if ($this->cart && $this->cart->items->count())
            <div class="space-y-4">
                @foreach ($this->cart->items as $item)
                    <div class="border rounded p-3 flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-600">
                                ${{ number_format($item->product->price, 2) }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <div class="flex items-center space-x-2">
                                <flux:button
                                    onclick="if (!confirm('You are about to remove {{ $item->product->name }} from your cart.')) { event.stopImmediatePropagation(); }"
                                    wire:click.prevent="removeItem({{ $item->id }})"
                                    class="px-2 py-1 cursor-pointer"
                                    tooltip="Remove" 
                                >
                                    <flux:icon.trash class="w-5 h-5 text-red-600" />
                                </flux:button>

                                @if($item->quantity > 1)
                                <flux:button
                                    wire:click="decrementQuantity({{ $item->id }})"
                                    class="px-2 py-1 cursor-pointer"
                                >
                                    -
                                </flux:button>
                                @endif

                                <span class="px-3">{{ $item->quantity }}</span>

                                <flux:button
                                    wire:click="addToCart({{ $item->product->id }})"
                                    class="px-2 py-1 cursor-pointer disabled:opacity-100"
                                    :disabled="($item->quantity >= $item->product->stock_quantity)"
                                    >+</flux:button>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- TOTAL --}}
                <div class="mt-6 border-t pt-4 flex justify-between text-xl font-bold">
                    <span>Total</span>
                    <span>${{ number_format($this->cart->total, 2) }}</span>
                </div>

                <div class="flex justify-end mt-4">
                    <flux:button 
                        variant="filled"
                        wire:click="checkout"
                        class="mt-4 px-4 py-2 rounded cursor-pointer"
                        :disabled="!$this->canCheckout"
                    >
                        Checkout
                    </flux:button>
                </div>
            </div>
        @else
            <p class="text-gray-500">Your cart is empty.</p>
        @endif
    </div>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-alert', ({ message }) => {
            alert(message);
        });
    });
</script>
</div>
