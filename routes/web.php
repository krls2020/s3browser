<?php

use App\Http\Controllers\S3controller;
use Illuminate\Support\Facades\Route;

Route::get('/', [S3controller::class, 'index'])->name('index');
Route::get('/upload-test-file', [S3controller::class, 'uploadTestFile']);
