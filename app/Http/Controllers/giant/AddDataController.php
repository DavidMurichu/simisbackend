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
        try {

            Log::info($request->all());
            $allowed=config('crud.create');
            if(!in_array($tableName,$allowed)){
                $data=[
                    'message'=>'unauthorized request',
                    'status'=>401
                ];
                return response()->json($data, 401);
            }
            // Ensure table name is valid and model exists
            if (!$this->isValidTableName($tableName)) {
                throw new Exception("Invalid table name provided.");
            }

            // Get fillable columns and validation rules
            $columns = Schema::getColumnListing($tableName);
            $validationRules = $this->get_validation_rules($tableName, $columns);

            // Validate the request data
            $validator = Validator::make($request->all(), $validationRules);
           
    
           
            if ($validator->fails()) {
                Log::error($validator->messages());
                Log::error($request->all());
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'status' => 401
                ], 401);
            }

            

            // Convert table name to model name
            $model = Str::singular(Str::studly($tableName)); 
            $modelPath = "App\\Models\\" . $model;

            // // Check if the class exists
            if (!class_exists($modelPath)) {
                throw new Exception("Model class '$modelPath' does not exist.");
            }

            

            // Instantiate the model class and create the record
            $modelInstance = new $modelPath();
          
            
            $modelInstance::create($request->all());
         

           
           
            $auditData=[
                'user_name' => $model,
                'activity_type' => 'table update',
                'ip_address'=>$request->ip(),
            ];
            $this->makeAudit($auditData);
            // Return a success response
            return response()->json([
                'message' => 'Data created successfully',
                'status' => 201
            ], 201);

        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'message' => $e->validator->errors()->first(),
                'status' => 422
            ], 422);

        } catch (Exception $e) {
            // Log other exceptions and return a generic error response
            Log::error('Error creating data: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while creating data.',
                'status' => 500
            ], 500);
        }
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