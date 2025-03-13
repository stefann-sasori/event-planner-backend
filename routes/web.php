<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::get('/debug-proxy', function (\Illuminate\Http\Request $request) {
    return [
        'full_url' => $request->fullUrl(),
        'secure' => $request->isSecure(),
        'scheme' => $request->getScheme(),
        'host' => $request->getHost(),
        'headers' => $request->headers->all(),
        'app_url' => config('app.url'),
    ];
});
