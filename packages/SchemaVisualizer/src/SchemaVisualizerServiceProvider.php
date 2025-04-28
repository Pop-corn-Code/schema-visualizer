<?php

namespace SchemaVisualizer;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SchemaVisualizerServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/Resources/views', 'schema-visualizer');

        // Register Livewire components
        Livewire::component('schema-visualizer.tables', \SchemaVisualizer\Http\Livewire\Tables::class);
    }
}
