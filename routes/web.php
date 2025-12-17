<?php

use Illuminate\Support\Facades\Route;

// Omega CMMS Dashboard
Route::get('/', function () {
    return view('dashboard');
});

// API routes documentation
Route::get('/api-docs', function () {
    return view('api-docs');
});