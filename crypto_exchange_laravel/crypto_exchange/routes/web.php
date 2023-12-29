<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PriceAlertController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ChatbotController;

// Routes that need authentication
Route::middleware(['checkUserSession'])->group(function () {
    Route::get('/trade', function () {
        return view('trading');
    });
    
    Route::get('/api/check-alerts', [PriceAlertController::class, 'checkAlerts']);
    Route::post('/api/send-alert', [PriceAlertController::class, 'sendAlert']);
    Route::get('/alerts', function () {
        return view('alerts');
    });

    Route::post('/api/wallet/action', [WalletController::class, 'handleAction']);
    Route::get('/wallet', function () {
        return view('wallet');
    });

    Route::get('/api/wallet/balance', [WalletController::class, 'getBalances']);

    Route::post('api/contact/send', [ChatbotController::class, 'sendMessage']);
    Route::get('/contact', function () {
        return view('contact');
    });
});

// Routes that do not need authentication
Route::get('/home', function () {
    return view('home');
});

Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', function () {
    return view('login');
});
Route::get('/logout', [AuthController::class, 'logout']);

Route::post('/register', [AuthController::class, 'register']);
Route::get('/register', function () {
    return view('register');
});

Route::get('/phpinfo', function () {
    return phpinfo();
});