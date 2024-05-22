<?php

namespace App\Http\Controllers\giant;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

use App\Traits\SchemaTrait;
class SchemaController extends Controller
{

    use SchemaTrait;

  // GET data from table by table name
 public function get_table_data(Request $request, $tableName){
    //check if operation is allowed
    $allowed=['users', 'clients', 'audits'];
    if(!in_array($tableName,$allowed)){
        $data=[
            'message'=>'unauthorized request',
            'status'=>401
        ];
        return response()->json($data, 401);
    }
    //call generate data trait to get the data
    $data=$this->getDataByTableName(strtolower($tableName));
    return response()->json($data);
   }


   //Edit data by use of tablename
   public function update(Request $request , $tableName){
    //Check if operationis allowed
    $allowed=['users', 'clients'];
    if(!in_array($tableName,$allowed)){
        $data=[
            'message'=>'unauthorized edit',
            'status'=>401
        ];
        return response()->json($data, 401);
    }
    //data to update in table
    $data_to_update = $request->input('clientInfo.changedFields');
    $id = $request->input('clientInfo.tableId');
    //check if the id is loaded
    if(!$id){
        $data=[
            'message'=>'Unauthorized',
            'status'=>401,
        ];
        return response()->json($data, 401);
    }
    //fetch the columns neeeded to update 
    $columns=[];
    foreach ($data_to_update as $key => $value) {
        // Check if the column exists in the table(error may occur if the frontend send wrong data)
        if(Schema::hasColumn($tableName, $key)) {
            array_push($columns, $key);

        }else{
            $data=[
                'message'=>'We do not torrelate this',
                'status'=>401
            ];
            return response()->json($data, 401);
        }
    }
    //get the details in id
    $validationrules=$this->get_validation_rules($tableName, $columns);
    //validate request
    $request->validate($validationrules);
    //retrive existing data
    $database_data = DB::table($tableName)->find($id);

    if (!$database_data) {
        return response()->json(['error' => 'Record not found'], 404);
    }
    // Perform the update
    try {
        DB::table($tableName)->where('id', $id)->update($data_to_update);
        
        Log::info('updated '.$id);
        return response()->json(['success' => 'Record updated successfully'], 200);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['error' => 'Update failed ' ], 500);
    }
    

}

public function delete(Request $request){
    try{
    $id = $request->input('id');
    $tableName = $request->input('data_id');
    $delete_status=$this->deleteData($tableName,$id);
    }catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['error'=> 'Can not delete'], 404);
    }
}


}
