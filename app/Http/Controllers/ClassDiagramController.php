<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ClassDiagramController extends Controller
{
    public function generateDiagram()
    {
        $models = $this->getModelData();

        // Generate Mermaid-compatible diagram
        $diagram = $this->generateMermaidDiagram($models);
        // dd($diagram);
        return view('class-diagram', compact('diagram'));
    }

    private function getModelData()
    {
        // Retrieve all models from the app directory (can be filtered to specific folder)
        $modelsPath = app_path('Models');
        $models = [];

        foreach (File::allFiles($modelsPath) as $file) {
            $modelName = pathinfo($file)['filename'];
            $models[] = [
                'name' => $modelName,
                'relationships' => $this->getModelRelationships($modelName),
            ];
        }

        return $models;
    }

    private function getModelRelationships($modelName)
    {
        // Dummy relationships - You can expand based on actual relationships in your models
        $relationships = [];

        if (class_exists($modelName)) {
            $model = new $modelName;
            // Sample logic for detecting relationships (e.g., belongsTo, hasMany, etc.)
            // You might need to inspect your models and define the relationships manually
            // or use reflection or other methods
        }

        return $relationships;
    }

    private function generateMermaidDiagram($models)
    {
        $mermaidSyntax = "classDiagram\n";

        foreach ($models as $model) {
            $mermaidSyntax .= "class {$model['name']} {\n";
            $mermaidSyntax .= "}\n";
            foreach ($model['relationships'] as $relationship) {
                // Define relationships here (this is a placeholder)
                $mermaidSyntax .= "{$model['name']} --> {$relationship}\n";
            }
        }

        return $mermaidSyntax;
    }
}
