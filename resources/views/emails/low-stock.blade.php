<h2>Low Stock Alert</h2>

<p>
    Attention: The following product is running low on available stock.
</p>

<ul>
    <li><strong>Product:</strong> {{ $product->name }}</li>
    <li><strong>Remaining Stock:</strong> {{ $product->stock_quantity }}</li>
    <li><strong>Units in Active Shopping Carts:</strong> {{ $product->cart_items_count }}</li>
</ul>

<p>
    Please review inventory levels and take appropriate action to avoid stockouts.
</p>