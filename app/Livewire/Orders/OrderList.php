<?php

namespace App\Livewire\Orders;

use App\Service\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Orders')]
class OrderList extends Component
{
    public function render()
    {
        $orders = (new OrderService)->getOrders(Auth::user());

        return view('livewire.orders.order-list', [
            'orders' => $orders,
        ]);
    }
}
