<?php

namespace App\Http\Controllers\giant;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Traits\DemonTrait;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Schema;
use Log;
use Exception;
use Str;

class AddDataController extends Controller
{
    use SchemaTrait;
    use DemonTrait;

    public function create(Request $request, $tableName)
{
    // Handle both single entry and multiple entries
    $dataEntries = isset($request->all()[0]) ? $request->all() : [$request->all()];
    $results = [];
    
    // Iterate over each data entry
    foreach ($dataEntries as $data) {
        try {
            Log::info($data);
            $allowed = config('crud.create');
            if (!in_array($tableName, $allowed)) {
                throw new Exception('Unauthorized request', 401);
            }

            // Ensure table name is valid and model exists
            if (!$this->isValidTableName($tableName)) {
                throw new Exception("Invalid table name provided.");
            }

            // Get fillable columns and validation rules
            $columns = Schema::getColumnListing($tableName);
            $validationRules = $this->get_validation_rules($tableName, $columns);

            // Validate the request data
            $validator = Validator::make($data, $validationRules);

            if ($validator->fails()) {
                Log::error($validator->messages());
                Log::error($data);
                $results[] = [
                    'status' => 'error' ,
                    'message' => $validator->errors()->first()
                ];
                continue; // Skip to the next entry
            }

            // Convert table name to model name
            $model = Str::singular(Str::studly($tableName));
            $modelPath = "App\\Models\\" . $model;

            // Check if the class exists
            if (!class_exists($modelPath)) {
                throw new Exception("Model class '$modelPath' does not exist.");
            }

            // Instantiate the model class and create the record
            $modelInstance = new $modelPath();
            $data['ipaddress'] = request()->ip();
            $modelInstance::create($data);

            // Audit logging
            $auditData = [
                'user_name' => $model,
                'activity_type' => 'table update',
                'ip_address' => request()->ip(),
            ];
            $this->makeAudit($auditData);

            // Record successful creation
            $results[] = [
                'status' => 'success',
                'message' => 'Data created successfully'
            ];

        } catch (ValidationException $e) {
            $results[] = [
                'status' => 'error',
                'message' => $e->validator->errors()->first()
            ];

        } catch (Exception $e) {
            Log::error('Error creating data: ' . $e->getMessage());
            $status = $e->getCode() ?: 500;
            $results[] = [
                'status' => 'error',
                'message' => 'An error occurred while creating data.'
            ];
        }
    }

    $overallStatus = collect($results)->contains('status', 'error') ? 401 : 201;

    // Return all results
    return response()->json($results, $overallStatus);
}

    /**
     * Validate if the provided table name is valid
     *
     * @param string $tableName
     * @return bool
     */
    protected function isValidTableName($tableName)
    {
        // Add your logic to validate table name if needed
        return !empty($tableName);
    }
}