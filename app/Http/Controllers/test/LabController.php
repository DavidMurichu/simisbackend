<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use App\Http\Controllers\giant\EditTableDetailsController;
use App\Models\SchStudent;
use App\Models\test\InvoiceHolder;
use App\Traits\DemonTrait;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;

class LabController extends Controller
{
    use SchemaTrait;
    use DemonTrait;
    
   
 

    
    public function index()
    {
       return $this->recordPaymentTerm();
        
    }

    public function recordPaymentTerm(){
        try{
        $paymentTermData = [
                'name' => '1222' ,
                'remarks' => 'remarks',
                'createdby' => 1,
                'laseditedby' => 1,
                'branch_name' => 'School',
            ]; 
        $data=$this->demonAdd(new Request($paymentTermData), 'sch_payment_terms', true);
       
        \Log::info('payment', $data);
        return $data['results'];
        }catch(\Exception $e){
            \Log::info("error". $e);
            throw new \Exception("error creating payment term");
        }
        
    }
    public function getData()
    {
        $results = [
            'data' => [
                'example_key' => 'example_value',
                // Add other key-value pairs for your actual data here
            ],
            'message' => 'Success',
        ];
    
        $overallStatus = 200; // HTTP status code
    
        return $results; // Return the constructed data array
    }
    
    public function fetchDataFromFirstModule()
    {
        // Make an HTTP request to the first module's endpoint (modify if needed)
        $data = $this->getData(); // Retrieve data from the getData function
    
        return json_decode($data, true); // Decode the retrieved data
    }
    

    public static function getRelationshipNames()
    {
        $relationships = [];
        $model = new SchStudent;

        // Use reflection to inspect all methods of the model
        $class = new \ReflectionClass($model);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $methodName = $method->name;

            // Check if method is a relationship (assuming standard naming convention)
            if (strpos($methodName, 'relation') === 0 || strpos($methodName, 'get') === 0) {
                // Get the return type of the method
                $returnType = $method->getReturnType();

                if ($returnType && is_subclass_of($returnType->getName(), \Illuminate\Database\Eloquent\Relations\Relation::class)) {
                    // This method returns a relationship
                    $relationships[] = $methodName;
                }
            }
        }

        return $relationships;
    }
   
    public function getStudentWithServices(){
        $students=SchStudent::all();
        
    }

    public function get_table_data(Request $request){

        $tableName='member_payables_arears';
        $id=null;
        //check if operation is allowed
        
        $allowed=config('crud.get');
        if(!in_array($tableName,$allowed)){
            $data=[
                'message'=>'unauthorized request',
                'status'=>401
            ];
            return response()->json($data, 401);
        }
  
      if ($id) {
          $data = \DB::table($tableName)->find($id);
  
          if (!$data) {
              return response()->json(['error' => 'Record not found'], 404);
          }
          return response()->json($data);
      } else {
          
      
      //   $data=$this->getDataByTableName(strtolower($tableName));
      $columns =$this->get_model_fillable($tableName);
      return $columns;
      $columns=array_merge(['id','created_at',
      'updated_at'],$columns);
  
      $data=$this->getDataByTableName($tableName, $columns);
        // Audit logging
      $auditData = [
          'user_name' => $tableName,
          'activity_type' => 'table update',
          'ipaddress' => request()->ip(),
      ];
      $this->makeAudit($auditData);
        return response()->json($data);
       }
      
      }
  }

