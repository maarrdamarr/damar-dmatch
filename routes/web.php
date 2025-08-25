<?php




use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', [TicketController::class, 'addToCart'])->name('cart.add');
    Route::get('/checkout', [TicketController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [TicketController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/checkout/success/{reference}', [TicketController::class, 'success'])->name('checkout.success');
});

Route::middleware(['auth','kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('dashboard');
    Route::post('/offline-sale', [CashierController::class, 'offlineSale'])->name('offline.sale');
    Route::post('/confirm/{order}', [CashierController::class, 'confirmPayment'])->name('confirm');
});

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('/events', EventController::class)->except(['show','index']);
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
});

require __DIR__.'/auth.php';
