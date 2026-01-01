<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ActivityLog extends Component
{
    public function render()
    {
        return view('livewire.activity-log', [
            'activities' => Auth::user()
                ->activities()
                ->latest()
                ->take(50)
                ->get(),
        ]);
    }
}
