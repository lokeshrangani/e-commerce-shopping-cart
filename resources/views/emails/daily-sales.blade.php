<h2>    
    Daily Sales Report - {{ $date }}  
    <br>
    Total Sales: ${{ number_format($total, 2) }} ({{ $orderCount }} orders)
</h2>

@if($orders->isEmpty())
    <p>No sales recorded today.</p>
@else
    @foreach($orders as $order)
        <hr>
        <p><strong>Order #</strong>{{ $order->id }}</p>
        <p><strong>Total:</strong> ${{ number_format($order->total_amount, 2) }}</p>

        <ul>
            @foreach($order->items as $item)
                <li>
                    {{ $item->product->name }}
                    - Qty: {{ $item->quantity }}
                    - Price: ${{ number_format($item->price_at_purchase, 2) }}
                </li>
            @endforeach
        </ul>
    @endforeach
@endif