<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Log;
use Str;
use PDO;
use Exception;


trait SchemaTrait
{





// public function edit_tablee(Request $request, $tableName, $id){
//         // Check if the table exists
//             if (!Schema::hasTable($tableName)) {
//                 return response()->json(['error' => 'Table not found'], 404);
//             }
//             // Generate validation rules dynamically
//             $rules = $this->generateValidationRules($tableName);
    
//             // Validate the input
//             $data = $request->validate($rules);
//             // return response()->json($data);

//             // Update the record in the specified table
//             // try {
//             //     DB::table($tableName)
//             //         ->where('id', $id)
//             //         ->update($data);
    
//             //     return response()->json(['success' => 'Record updated successfully'], 200);
//             // } catch (\Exception $e) {
//             //     return response()->json(['error' => 'Update failed: ' . $e->getMessage()], 500);
//             // }
//         }


public function getDataByTableIdAndColumns($table, $id, $columns)
{
    try {
        // Check if the table exists
        if (!Schema::hasTable($table)) {
            $data=[
                "message"=>'Table does not exist',
                "status"=>401
            ];
            return $data;
        }

     

        // Ensure the columns exist in the table
        $tableColumns = Schema::getColumnListing($table);
        foreach ($columns as $column) {
            if (!in_array($column, $tableColumns)) {
                return response()->json(['error' => "Column $column does not exist in table $table"], 400);
            }
        }

        // Query the table for the given ID and select the specified columns
        $data = DB::table($table)->select($columns)->where('id', $id)->first();

        // Check if data was found
        if (!$data) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Return the data
        return response()->json($data, 200);
    } catch (\Exception $e) {
        // Log and return the error
        \Log::error($e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
private function get_validation_rules($tableName, $columns)
{

    Log::info($tableName."".json_encode($columns));
    if (!Schema::hasTable($tableName)) {
        return response()->json(['error' => 'Table not found'], 404);
    }

    $rules = [];
    
    foreach ($columns as $column) {
        if (!Schema::hasColumn($tableName, $column)) {
            // If the column doesn't exist in the table, skip it
           return response()->json(['error'=> 'Column do not exist'. $column ],404);
        }

        if (in_array($column, ['id', 'created_at', 'updated_at'])) {
            // Skip these special columns
            continue;
        }

        // Get column type and nullability
        $columnType = Schema::getColumnType($tableName, $column);
        $isNullable = $this->isColumnNullable($tableName, $column);
        $is_unique=$this->isColumnUnique($tableName, $column);
        $foreign_key = $this->getForeignKeyDetails($tableName, $column);
        


        // Initialize an array to hold the rules for this column
        $columnRules = [];

        // Add rules based on the type of the column
        switch ($columnType) {
            case strpos($columnType, 'bigint') !== false:
            case strpos($columnType, 'int') !== false && $columnType !=='tinyint':
                 $columnRules[] = 'integer';
                break;
     
            case strpos($columnType, 'varchar') !== false:
                preg_match('/varchar\((\d+)\)/',$columnType, $matches);
                $maxLength = isset($matches[1]) ? $matches[1] : 255;
                $columnRules[] = 'string|max:' . $maxLength;
                break;
     
            case strpos($columnType, 'tinyint') !== false:
                 $columnRules[] = 'boolean';
                break;
            case strpos($columnType, 'double') !== false:
                $columnRules[] = 'numeric';
                break;
     
            case strpos($columnType, 'timestamp') !== false:
            case strpos($columnType, 'datetime') !== false:
            case strpos($columnType, 'date') !== false:
            
                 $columnRules[] = 'date';
                break;
     
            case strpos($columnType, 'text') !== false:
                 $columnRules[] = 'string';
                break;
     
            default:
                break;
        }

       
        // Add nullable or required rule based on column nullability
        if ($isNullable) {
            $columnRules[] = 'nullable';
        } else {
            $columnRules[] = 'required';
        }
        if($is_unique){
            $columnRules[] = 'unique:'.$tableName.','.$column;
        }
        if($foreign_key){
            $columnRules[] = 'exists:'. $foreign_key;
        }
        

        // Common validation rules based on column name patterns
        if (str_contains($column, 'email')) {
            $columnRules[] = 'email';
        }

        if (str_contains($column, 'password')) {
            $columnRules[] = 'confirmed';
        }

        // Additional rules based on column names
        if (str_contains($column, 'url')) {
            $columnRules[] = 'url';
        }

        if (str_contains($column, 'file')) {
            $columnRules[] = 'file';
        }
            // Check if the column is of ENUM type
        if ($this->isEnumColumn($tableName, $column)) {
            $enumValues = $this->getEnumValues($tableName, $column);
            $columnRules[] = 'in:' . implode(',', $enumValues);
        }

        

        // Compile the rules for this column into a string
        $rules[$column] = implode('|', $columnRules);
    }

    return $rules;
 
}



// Check if a column is of ENUM type
protected function isEnumColumn($tableName, $column)
{
    $type = DB::select("SHOW COLUMNS FROM {$tableName} WHERE Field = ?", [$column])[0]->Type;
    return strpos($type, 'enum') === 0;
}

// Get ENUM values for a column
protected function getEnumValues($tableName, $column)
{
    $type = DB::select("SHOW COLUMNS FROM {$tableName} WHERE Field = ?", [$column])[0]->Type;
    preg_match('/^enum\((.*)\)$/', $type, $matches);
    $enumValues = str_getcsv($matches[1], ',', "'");
    return $enumValues;
}


private function getColumnMaxLength($tableName, $column)
{
    $databaseName = DB::connection()->getDatabaseName();
    $query = "SELECT CHARACTER_MAXIMUM_LENGTH 
              FROM information_schema.COLUMNS 
              WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $result = DB::select($query, [$databaseName, $tableName, $column]);

    return $result[0]->CHARACTER_MAXIMUM_LENGTH ?? null;
}

private function isColumnNullable($tableName, $column)
{
    $databaseName = DB::connection()->getDatabaseName();
    $query = "SELECT IS_NULLABLE 
              FROM information_schema.COLUMNS 
              WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $result = DB::select($query, [$databaseName, $tableName, $column]);

    return $result[0]->IS_NULLABLE === 'YES';
}

//get data
     public function getDataByTableName($tableName, $columns=['*'], $getForeign=false)
        {
         try{

            if (!Schema::hasTable($tableName)) {
                return response()->json(['error' => 'Table does not exist'], 404);
            }
            // Get the fillable attributes from the model

            
            $query = DB::table($tableName);
            if (!empty($columns)) {
                $query->select($columns);
            }
            $relationship=$this->getRelationships($tableName);
            $data = $query->get();
            return $data;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error'=> $e->getMessage()],500);
        }

        }

        private function getRelationships($tableName)
    {
        // Convert table name to model name
        $model = Str::singular(Str::studly($tableName));
        $modelPath = "App\\Models\\" . $model;
        // Check if the class exists
        if (!class_exists($modelPath)) {
            throw new Exception("Model class '$modelPath' does not exist.");
        }
        // Instantiate the model class and create the record
        $modelClass = new $modelPath();
        $relationships = [];

        // Use reflection to inspect model relationships
        $reflectionClass = new \ReflectionClass($modelClass);
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            // Check if the method returns a Relation instance
            $returnType = $method->getReturnType();
            if ($returnType && is_subclass_of($returnType->getName(), Relation::class)) {
                $relationships[] = $method->getName();
            }
        }

        return $relationships;
    }


        //delete Data
        
        public function deleteData($tableName, $id)
{
    try {
        // Check if the table exists
        if (!Schema::hasTable($tableName)) {
            return response()->json(['error' => 'Table does not exist'], 404);
        }

        // Execute the DELETE query
        DB::table($tableName)->where('id', $id)->delete();

        // Return success response
        return response()->json(['message' => 'Data deleted successfully'], 200);
    } catch (\Exception $e) {
        Log::error($e->getMessage());

        // Return error response
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


//validate table columns

public function validateColumns($requestData, $tableName){
    $fillable=$this->get_model_fillable($tableName);
    $columns=[];
    foreach ($requestData as $key => $value) {
        // Check if the column exists in the table(error may occur if the frontend send wrong data)
        if (!in_array($key, $fillable)) {
            Log::info( $key);
            return false;
        }
        array_push( $columns, $key);
    }
    return $columns;

}


public function get_model_fillable($tableName)
    {
        try {
            // Validate input
            if (empty($tableName) || !is_string($tableName)) {
                throw new Exception('Invalid table name provided.');
            }

            // Convert table name to model name (assumes the table names are plural and models are singular)
            $model = Str::singular(Str::studly($tableName)); 
            // Define the fully qualified class name for the model
            $modelPath = "App\\Models\\" . $model;

            // Check if the class exists
            if (!class_exists($modelPath)) {
                throw new Exception("Model class '$modelPath' does not exist.");
            }

            // Instantiate the model class
            $modelInstance = new $modelPath();

            // Check if the model has the fillable property and return it
            if (!property_exists($modelInstance, 'fillable')) {
                throw new Exception("Model class '$modelPath' does not have a fillable property.");
            }

      
            return $modelInstance->get_fillable();

        } catch (Exception $e) {
            // Log the error
            Log::error('Error in get_model function: ' . $e->getMessage());

            // Return an error message
            return 'An error occurred: ' . $e->getMessage();
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
    $schema = DB::connection()->getDatabaseName(); // Get current database name

    try {
        $foreignKeys = DB::table('information_schema.KEY_COLUMN_USAGE AS kcu')
            ->select('kcu.REFERENCED_TABLE_NAME', 'kcu.REFERENCED_COLUMN_NAME')
            ->join('information_schema.TABLES AS t', function ($join) use ($schema) {
                $join->on('t.TABLE_NAME', '=', 'kcu.TABLE_NAME')
                    ->where('t.TABLE_SCHEMA', '=', $schema);
            })
            ->where('kcu.TABLE_NAME', $tableName)
            ->where('kcu.COLUMN_NAME', $column)
            ->whereNotNull('kcu.REFERENCED_TABLE_NAME')
            ->first();

        if ($foreignKeys) {
            return $foreignKeys->REFERENCED_TABLE_NAME . ',' . $foreignKeys->REFERENCED_COLUMN_NAME;
        }

        return null;
    } catch (\Exception $e) {
        // Log or handle the exception here
        Log::error("Error fetching foreign key details: " . $e->getMessage());
        return null;
    }
}


public function getCrossForeignData($tableName, $foreignTable, $foreignColumn, $foreignColumnDPointer)
{

}


}