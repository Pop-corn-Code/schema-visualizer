<?php

namespace SchemaVisualizer\Services;

use Illuminate\Support\Facades\File;
use ReflectionClass;

class ModelAnalyzer
{
    public function getModels()
    {
        $models = [];

        $files = File::allFiles(app_path('Models'));

        foreach ($files as $file) {
            $class = $this->getClassFullNameFromFile($file->getRealPath());

            if (class_exists($class)) {
                $reflection = new ReflectionClass($class);

                if ($reflection->isSubclassOf('Illuminate\Database\Eloquent\Model')) {
                    $models[] = $this->analyzeModel($class);
                }
            }
        }

        return $models;
    }

    protected function analyzeModel($class)
    {
        $instance = new $class;

        return [
            'class' => $class,
            'fillable' => $instance->getFillable(),
            'hidden' => $instance->getHidden(),
            'casts' => $instance->getCasts(),
            'table' => $instance->getTable(),
        ];
    }

    protected function getClassFullNameFromFile($file)
    {
        $content = file_get_contents($file);
        if (preg_match('/namespace\s+(.+?);/', $content, $matches)) {
            $namespace = $matches[1];
        }

        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            $class = $matches[1];
        }

        return $namespace . '\\' . $class;
    }
}
