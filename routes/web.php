<?php

use App\Livewire\ActivityLog;
use App\Livewire\Orders\OrderList;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\ShopPage;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('preview-daily-sales-email/{date}', function () {
        $date = \Carbon\Carbon::parse(request('date', now()))->toDateString();
        $salesData = (new \App\Service\DailySalesCsvService)->prepareDailySalesData($date);

        return view('emails.daily-sales', [...$salesData]);
    });

    Route::get('preview-low-stock-email/{productId}', function () {
        $product = (new \App\Service\StockService)->getProduct(request('productId'));

        return view('emails.low-stock', ['product' => $product]);
    });

    Route::get('/downloads/reports/{filename}', [\App\Service\DailySalesCsvService::class, 'download'])->name('reports.download');

    Route::redirect('settings', 'settings/profile');
    Route::get('activity', ActivityLog::class)->name('activity');
    Route::get('shop', ShopPage::class)->name('shop');
    Route::get('orders', OrderList::class)->name('orders.index');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
