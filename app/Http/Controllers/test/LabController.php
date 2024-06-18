<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use App\Http\Controllers\giant\EditTableDetailsController;
use App\Models\SchStudent;
use App\Traits\DemonTrait;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;

class LabController extends Controller
{
    use SchemaTrait;
    use DemonTrait;

    public function index()
    {
       
        
        
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

