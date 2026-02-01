<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'search');
Route::view('/search', 'search')->name('search');
