<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'pages::index')->name('/');

Route::livewire('/login', 'pages::auth.login')->name('auth.login');
Route::livewire('/register', 'pages::auth.register')->name('auth.register');

Route::livewire('/admin/categories', 'pages::categories.index')->name('categories.index');

Route::livewire('/admin/products', 'pages::products.index')->name('products.index');
Route::livewire('/admin/products/create', 'pages::products.create')->name('products.create');