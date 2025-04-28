<?php

namespace SchemaVisualizer\Services;

use Illuminate\Support\Facades\DB;

class SchemaExtractor
{
    public function getTables()
    {
        $tables = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . DB::getDatabaseName();

        return array_map(function($table) use ($key) {
            return $table->$key;
        }, $tables);
    }

    public function getTableColumns(string $table)
    {
        $columns = DB::select('SHOW COLUMNS FROM ' . $table);
        return array_map(function($col) {
            return [
                'Field' => $col->Field,
                'Type' => $col->Type,
                'Null' => $col->Null,
                'Key' => $col->Key,
                'Default' => $col->Default,
                'Extra' => $col->Extra,
            ];
        }, $columns);
    }
}
