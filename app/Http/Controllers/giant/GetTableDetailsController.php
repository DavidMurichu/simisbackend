<?php

namespace App\Http\Controllers\giant;

use App\Http\Controllers\Controller;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;

class GetTableDetailsController extends Controller
{
    use SchemaTrait;

    // GET data from table by table name
   public function get_table_data(Request $request, $tableName){
      //check if operation is allowed
      $allowed=['users', 'clients', 'audits'];
      $restricted_fields=['password', 'created_by', 'createdby','createdon', 'showCompanyLogo'];
      if(!in_array($tableName,$allowed)){
          $data=[
              'message'=>'unauthorized request',
              'status'=>401
          ];
          return response()->json($data, 401);
      }
    //   $data=$this->getDataByTableName(strtolower($tableName));
    $columns =$this->get_model_fillable($tableName);
    $columns=array_diff($columns,$restricted_fields);
    $data=$this->getDataByTableName($tableName, $columns);
      return response()->json($data);
     }
}
