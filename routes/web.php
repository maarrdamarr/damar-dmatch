<?php




use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupportController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/', fn() => redirect()->route('events.index'));


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', [TicketController::class, 'addToCart'])->name('cart.add');
    Route::get('/checkout', [TicketController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [TicketController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/checkout/success/{reference}', [TicketController::class, 'success'])->name('checkout.success');
    Route::get('/tickets/print/{reference}', [TicketController::class, 'print'])
        ->name('tickets.print');
});

Route::middleware(['auth','kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('dashboard');
    Route::post('/offline-sale', [CashierController::class, 'offlineSale'])->name('offline.sale');
    Route::post('/confirm/{order}', [CashierController::class, 'confirmPayment'])->name('confirm');

    Route::get('/refund', [CashierController::class, 'refundForm'])->name('refund.form');
    Route::post('/refund', [CashierController::class, 'processRefund'])->name('refund.process');
    Route::post('/swap-seat', [CashierController::class, 'swapSeat'])->name('swap.seat');

    Route::get('/riwayat', [CashierController::class, 'history'])->name('history');
    Route::get('/riwayat/export', [CashierController::class, 'exportCsv'])->name('history.export');

    Route::get('/cetak', [CashierController::class, 'printForm'])->name('print.form');


    Route::get('/riwayat', [\App\Http\Controllers\CashierController::class, 'history'])->name('history');
    Route::get('/riwayat/export', [\App\Http\Controllers\CashierController::class, 'exportCsv'])->name('history.export');

    // BARU: Menu Lain-lain (bantuan)
    Route::get('/bantuan', fn () => view('kasir.help'))->name('help');
});

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('/events', EventController::class)->except(['show','index']);
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
});

// Publik
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Admin
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('/events', EventController::class)->except(['show','index']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/me', [UserController::class,'dashboard'])->name('user.dashboard');

    Route::post('/support/refund', [SupportController::class,'storeRefund'])->name('support.refund');
    Route::post('/support/swap',   [SupportController::class,'storeSwap'])->name('support.swap');
});




require __DIR__.'/auth.php';
