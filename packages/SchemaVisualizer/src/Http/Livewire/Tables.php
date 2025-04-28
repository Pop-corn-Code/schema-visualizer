<?php

namespace SchemaVisualizer\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Tables extends Component
{
    public $tables = [];
    public $search = '';
    public $openTable = null;

    public function mount()
    {
        $this->fetchTables();
    }

    public function toggleTable($tableName)
    {
        if ($this->openTable === $tableName) {
            $this->openTable = null;
        } else {
            $this->openTable = $tableName;
        }
    }
    protected function getForeignKeys($table)
    {
        $foreignKeys = [];

        $results = \DB::select("
            SELECT
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE
                TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table]);

        foreach ($results as $row) {
            $foreignKeys[] = [
                'column' => $row->COLUMN_NAME,
                'referenced_table' => $row->REFERENCED_TABLE_NAME,
                'referenced_column' => $row->REFERENCED_COLUMN_NAME,
            ];
        }

        return $foreignKeys;
    }


    public function fetchTables()
    {
        $databaseName = config('database.connections.mysql.database');

        $tables = DB::select("SHOW TABLES");

        $keyName = "Tables_in_" . $databaseName;

        $this->tables = collect($tables)->map(function ($table) use ($keyName) {
            $tableName = $table->$keyName;

            // Get columns
            $columns = DB::select("SHOW COLUMNS FROM `$tableName`");

            return [
                'name' => $tableName,
                'columns' => $columns,
            ];
        })->toArray();
    }
    public function generateMermaidClassDiagramOld()
    {
        $diagram = "---
                    title: ClassDiagram of the system
                    ---
                    classDiagram\n";

        $allTables = DB::select('SHOW TABLES');
        $tableNameKey = 'Tables_in_' . DB::getDatabaseName();

        foreach ($allTables as $table) {
            $tableName = ucfirst($table->$tableNameKey);

            // Start table block
            $diagram .= "    class $tableName {\n";

            // Add columns
            $columns = DB::select("SHOW COLUMNS FROM `$tableName`");
            foreach ($columns as $column) {
                $type = $this->mapColumnType($column->Type);
                $visibility = ($column->Field == 'id') ? '+' : '-'; // example rule
                $diagram .= "        {$visibility}{$type} {$column->Field}\n";
            }

            $diagram .= "    }\n";
        }

        // Now visualize relationships
        foreach ($allTables as $table) {
            $tableName = ucfirst($table->$tableNameKey);

            $foreignKeys = $this->getForeignKeys($tableName);
            foreach ($foreignKeys as $fk) {
                $referencedTable = ucfirst($fk['referenced_table']);
                $diagram .= "    $referencedTable <|-- $tableName : \"{$fk['column']}\"\n";
            }
        }

        return $diagram;
    }

    private function mapColumnType($dbType)
    {
        if (str_contains($dbType, 'int')) {
            return 'int';
        } elseif (str_contains($dbType, 'varchar') || str_contains($dbType, 'text')) {
            return 'String';
        } elseif (str_contains($dbType, 'bool')) {
            return 'bool';
        } elseif (str_contains($dbType, 'timestamp') || str_contains($dbType, 'date')) {
            return 'Date';
        }
        return 'any';
    }
    public function generateMermaidClassDiagram()
    {
        $diagram = "<div class=\"mermaid\">\n";
        $diagram .= "    %%{\n";
        $diagram .= "      init: {\n";
        $diagram .= "        'theme': 'base',\n";
        $diagram .= "        'themeVariables': {\n";
        $diagram .= "          'primaryColor': '#BB2528',\n";
        $diagram .= "          'primaryTextColor': '#fff',\n";
        $diagram .= "          'primaryBorderColor': '#7C0000',\n";
        $diagram .= "          'lineColor': '#F8B229',\n";
        $diagram .= "          'secondaryColor': '#006100',\n";
        $diagram .= "          'tertiaryColor': '#fff'\n";
        $diagram .= "        },\n";
        $diagram .= "        'fontFamily': 'monospace'\n";
        $diagram .= "      }\n";
        $diagram .= "    }%%\n";
        $diagram .= "    classDiagram\n";

        $allTables = DB::select('SHOW TABLES');
        $tableNameKey = 'Tables_in_' . DB::getDatabaseName();

        foreach ($allTables as $table) {
            $tableName = ucfirst($table->$tableNameKey);

            // Start table block
            $diagram .= "        class $tableName {\n";

            // Add columns
            $columns = DB::select("SHOW COLUMNS FROM `$tableName`");
            foreach ($columns as $column) {
                $type = $this->mapColumnType($column->Type);
                $visibility = ($column->Field == 'id') ? '+' : '-'; // example rule
                $diagram .= "            {$visibility}{$type} {$column->Field}\n";
            }

            $diagram .= "        }\n";
        }

        // Now visualize relationships
        foreach ($allTables as $table) {
            $tableName = ucfirst($table->$tableNameKey);

            $foreignKeys = $this->getForeignKeys($tableName);
            foreach ($foreignKeys as $fk) {
                $referencedTable = ucfirst($fk['referenced_table']);
                $diagram .= "        $referencedTable <|-- $tableName : \"{$fk['column']}\"\n";
            }
        }

        $diagram .= "</div>\n";
        $diagram .= "<script type=\"module\">\n";
        $diagram .= "  import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';\n";
        $diagram .= "  mermaid.initialize({ startOnLoad: true });\n";
        $diagram .= "</script>\n";

        return $diagram;
    }


    public function generateMermaidERD()
    {
        $erd = "erDiagram\n";

        $allTables = DB::select('SHOW TABLES');
        $tableNameKey = 'Tables_in_' . DB::getDatabaseName();

        foreach ($allTables as $table) {
            $tableName = $table->$tableNameKey;

            // Start table block
            $erd .= "    $tableName {\n";

            // Add columns
            $columns = DB::select("SHOW COLUMNS FROM `$tableName`");
            foreach ($columns as $column) {
                $erd .= "        {$column->Type} {$column->Field}\n";
            }

            // Close table block
            $erd .= "    }\n";

            // Foreign keys
            $foreignKeys = $this->getForeignKeys($tableName);
            foreach ($foreignKeys as $fk) {
                $erd .= "    $tableName ||--|| {$fk['referenced_table']} : \"$fk[column]\"\n";
            }
        }

        return $erd;
    }

    public function render()
    {
        // dd($this->generateMermaidClassDiagram());

        $filteredTables = collect($this->tables);

        if (!empty($this->search)) {
            $filteredTables = $filteredTables->filter(function ($table) {
                return stripos($table['name'], $this->search) !== false;
            });
        }

        return view('schema-visualizer::livewire.tables', [
            'tables' => $filteredTables->toArray(),
        ]);
    }

}
