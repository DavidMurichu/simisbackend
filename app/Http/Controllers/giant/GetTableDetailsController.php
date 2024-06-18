<?php

namespace App\Http\Controllers\giant;

use App\Http\Controllers\Controller;
use App\Traits\DemonTrait;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;

class GetTableDetailsController extends Controller
{
    use SchemaTrait;
    use DemonTrait;

    // GET data from table by table name
   public function get_table_data(Request $request, $tableName, $id=null){
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
    $columns=array_merge(['id','created_at',
    'updated_at'],$columns);

    $data=$this->getDataByTableName($tableName, $columns);
      // Audit logging
    $auditData = [
        'user_name' => $tableName,
        'activity_type' => 'Get',
        'ipaddress' => request()->ip(),
    ];
    $this->makeAudit($auditData);
      return response()->json($data);
     }
    
    }
}
