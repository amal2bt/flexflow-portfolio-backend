<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'FlexFlow API',
        'status' => 'ok',
        'docs' => url('/api/site'),
    ]);
});
