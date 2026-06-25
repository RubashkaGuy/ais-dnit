<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/health', fn () => response()->json(['status' => 'ok']));
