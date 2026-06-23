<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Landing'))->name('home');
Route::get('/chat', fn () => Inertia::render('Chat'))->name('chat');
Route::get('/about', fn () => Inertia::render('About'))->name('about');
