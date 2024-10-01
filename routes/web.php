<?php

use App\Http\Controllers\S3Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', [S3Controller::class, 'index'])->name('index');
Route::get('/upload-test-file', [S3Controller::class, 'uploadTestFile']);
