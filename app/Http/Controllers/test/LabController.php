<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Log;

class LabController extends Controller
{
    use SchemaTrait;


    public function getTableColumns(Request $request)
    {
        $tableName='students';
        // Check if the table exists
        if (!Schema::hasTable($tableName)) {
            throw new \Exception("Table '$tableName' does not exist.");
        }

        // Get column information
        $columns = DB::select("SHOW COLUMNS FROM $tableName");
        $c_unique=[];

        $columnDetails = [];

        // Loop through the columns to get the field names and types
        foreach ($columns as $column) {
            // $columnDetails[$column->Field] = $column->Type;
            $columnDetails[] = $column->Field;
        }
        // foreach ($columnDetails as $column) {
        //     $is_unique=$this->isColumnUnique($tableName, $column);
        //     // $c_unique[]=$unique;

        // }

        // $dataType=$this->createValidationRules($columnDetails);
        $data=$this->get_validation_rules($tableName, $columnDetails);
        // $is_foreignkey=$this->getForeignKeyDetails($tableName, 'parent_id');
        
        
        return $data;
    }


    public function createValidationRules(array $columns)
    {
        $rules = [];

        foreach ($columns as $column => $type) {
            switch ($type) {
                case strpos($type, 'bigint') !== false:
                case strpos($type, 'int') !== false:
                    $rules[$column] = 'integer';
                    break;

                case strpos($type, 'varchar') !== false:
                    preg_match('/varchar\((\d+)\)/', $type, $matches);
                    $maxLength = isset($matches[1]) ? $matches[1] : 255;
                    $rules[$column] = 'string|max:' . $maxLength;
                    break;

                case strpos($type, 'tinyint') !== false:
                    $rules[$column] = 'boolean';
                    break;

                case strpos($type, 'timestamp') !== false:
                case strpos($type, 'datetime') !== false:
                case strpos($type, 'date') !== false:
                    $rules[$column] = 'date';
                    break;

                case strpos($type, 'text') !== false:
                    $rules[$column] = 'string';
                    break;

                default:
                    break;
            }
        }

        return $rules;
    }


    
    public function index()
    {
        $tableName = "users";

        try {
            // Check if the table exists
            if (!Schema::hasTable($tableName)) {
                throw new \Exception("Table '$tableName' does not exist.");
            }

            $columns = Schema::getColumnListing($tableName);
            $nonNullableColumns = [];

            foreach ($columns as $column) {
                $nullable = $this->isColumnNullable($tableName, $column);
                if (!$nullable) {
                    $nonNullableColumns[] = $column;
                }
            }

            return response()->json($nonNullableColumns);

        } catch (\Exception $e) {
            // Log the error message and return a response
            Log::error("Error in LabController@index: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function isColumnNullable($tableName, $columnName)
    {
        try {
            // Query the information schema to get column details
            $column = DB::table('information_schema.COLUMNS')
                ->select('IS_NULLABLE')
                ->where('TABLE_NAME', $tableName)
                ->where('COLUMN_NAME', $columnName)
                ->where('TABLE_SCHEMA', env('DB_DATABASE')) // Ensure correct database name
                ->first();

            if (!$column) {
                throw new \Exception("Column '$columnName' does not exist in table '$tableName'.");
            }

            // Check if the column is nullable
            return $column->IS_NULLABLE === 'YES';

        } catch (\Exception $e) {
            // Handle the error, log it, or rethrow it as needed
            Log::error("Error in LabController@isColumnNullable: " . $e->getMessage());
            return false;
        }
    }


public function isColumnUnique($tableName, $column)
{
    $uniqueConstraints = collect(DB::select("
    SELECT
        CONSTRAINT_NAME
    FROM
        INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE
        TABLE_NAME = '{$tableName}'
        AND CONSTRAINT_SCHEMA = DATABASE()
        AND CONSTRAINT_TYPE = 'UNIQUE'
"))->pluck('CONSTRAINT_NAME')->toArray();

$uniqueColumns = collect(DB::select("
    SELECT
        COLUMN_NAME
    FROM
        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
        TABLE_NAME = '{$tableName}'
        AND CONSTRAINT_SCHEMA = DATABASE()
        AND COLUMN_NAME = '{$column}'
        AND CONSTRAINT_NAME IN ('".implode("','", $uniqueConstraints)."')
"))->pluck('COLUMN_NAME')->toArray();

return in_array($column, $uniqueColumns);
}

public function getForeignKeyDetails($tableName, $column)
{
    $foreignTableName = DB::table('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')
        ->where('TABLE_NAME', $tableName)
        ->where('COLUMN_NAME', $column)
        ->where('CONSTRAINT_SCHEMA', DB::raw('DATABASE()'))
        ->whereNotNull('REFERENCED_TABLE_NAME')
        ->value('REFERENCED_TABLE_NAME');

    return $foreignTableName;

}
  
}


