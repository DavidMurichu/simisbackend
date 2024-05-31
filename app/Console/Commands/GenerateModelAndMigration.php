<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


class GenerateModelAndMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-model-and-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $fields = $this->option('fields');

        if (empty($name) || empty($fields)) {
            $this->error('Model name and fields are required.');
            return;
        }

        // Create model
        Artisan::call('make:model', ['name' => $name]);

        // Create migration
        Artisan::call('make:migration', [
            'name' => 'create_' . strtolower($name) . '_table',
            '--create' => strtolower($name)
        ]);

        // Get the migration file
        $migrationFile = $this->getMigrationFileName($name);
        if ($migrationFile) {
            // Update migration file with fields
            $this->updateMigrationFile($migrationFile, $fields);

            // Run migration
            Artisan::call('migrate');

            $this->info('Model and migration created successfully.');
        } else {
            $this->error('Migration file not found.');
        }
    }

    protected function getMigrationFileName($name)
    {
        $migrationPath = database_path('migrations');
        $files = File::files($migrationPath);

        foreach ($files as $file) {
            if (strpos($file->getFilename(), 'create_' . strtolower($name) . '_table') !== false) {
                return $file->getPathname();
            }
        }

        return null;
    }

    protected function updateMigrationFile($filePath, $fields)
    {
        $fieldDefinitions = $this->generateFieldDefinitions($fields);
        $content = File::get($filePath);

        $upPos = strpos($content, 'Schema::create');
        $upEndPos = strpos($content, '});', $upPos);
        $upContent = substr($content, $upPos, $upEndPos - $upPos);

        $newUpContent = substr_replace($upContent, $fieldDefinitions, strpos($upContent, '->increments(\'id\');') + 20, 0);
        $newContent = str_replace($upContent, $newUpContent, $content);

        File::put($filePath, $newContent);
    }

    protected function generateFieldDefinitions($fields)
    {
        $fieldsArray = explode(',', $fields);
        $fieldDefinitions = '';

        foreach ($fieldsArray as $field) {
            [$name, $type] = explode(':', $field);
            $fieldDefinitions .= "\$table->$type('$name');\n            ";
        }

        return $fieldDefinitions;
    }
}
