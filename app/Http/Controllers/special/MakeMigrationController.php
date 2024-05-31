<?php

namespace App\Http\Controllers\special;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMigrationController extends Controller
{
    public function createModelSchema(Request $request)
    {
        $modelName = $request->modelName;
        $modelFields = $request->fields;

        // Validate model name and fields
        if (empty($modelName) || empty($modelFields)) {
            return response()->json(['error' => 'Model name and fields are required.'], 400);
        }

        // Create the model
        Artisan::call('make:model', [
            'name' => $modelName,
            '-m' => true,
        ]);

        $migrationFile = $this->getMigrationFileName($modelName);

        // Check if migration file creation failed
        if (!$migrationFile) {
            return response()->json(['error' => 'Failed to create migration file.'], 500);
        }

        // Update the migration file
        $this->updateMigrationFile($migrationFile, $modelFields);

        return response()->json(['message' => 'Model and migration created successfully.'], 200);
    }

    protected function getMigrationFileName($modelName)
    {
        $migrationPath = database_path('migrations');
        $tableName = Str::snake(Str::pluralStudly($modelName));
        $pattern = 'create_' . $tableName . '_table';

        $files = File::files($migrationPath);

        foreach ($files as $file) {
            if (Str::contains($file->getFilename(), $pattern)) {
                return $file->getPathname();
            }
        }

        return null;
    }

    protected function updateMigrationFile($filePath, $fields)
    {
        $fieldDefinitions = $this->generateFieldDefinitions($fields);
        $content = File::get($filePath);

        // Find the position of Schema::create
        $upPos = strpos($content, 'Schema::create');

        // Check if Schema::create is found
        if ($upPos === false) {
            return; // Handle error: Schema::create not found
        }

        // Find the closing curly brace of Schema::create
        $upEndPos = strpos($content, '});', $upPos);

        // Extract the schema definition section
        $upContent = substr($content, $upPos, $upEndPos - $upPos);

        // Find the position of the semicolon after the id definition
        $idSemicolonPos = strpos($upContent, ';', strpos($upContent, '->increments(\'id\');'));

        // Calculate the offset for field insertion
        $offset = $idSemicolonPos - strpos($upContent, '->increments(\'id\');') + 1;

        // Replace the existing schema definition with the updated one
        $newUpContent = substr_replace($upContent, $fieldDefinitions, $offset, 0);
        $newContent = str_replace($upContent, $newUpContent, $content);

        // Save the updated content to the file
        File::put($filePath, $newContent);
    }

    protected function generateFieldDefinitions($fields)
    {
        $parsedFields = explode(',', $fields);
        $fieldDefinitions = '';

        foreach ($parsedFields as $field) {
            [$name, $type] = explode(':', $field);

            // Validate field type
            if (!in_array($type, $this->supportedFieldTypes())) {
                return response()->json(['error' => "Invalid field type '$type'."], 400);
            }

            $fieldDefinitions .= "\$table->$type('$name');\n            ";
        }

        return $fieldDefinitions;
    }

    private function supportedFieldTypes()
    {
        return [
            // List of supported Laravel database column types
            'string',
            'integer',
            'text',
            'boolean',
            // ... add more supported types
        ];
    }
}