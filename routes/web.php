<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\ClassDiagramController;

Route::get('/class-diagram', [ClassDiagramController::class, 'generateDiagram']);

// use SchemaVisualizer\Http\Livewire\Tables;

// Route::group(['middleware' => ['web'], 'prefix' => 'schema-visualizer'], function () {
//     Route::get('/table', Tables::class)->name('schema.visualizer');
// });
