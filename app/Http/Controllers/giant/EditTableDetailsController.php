<?php

namespace App\Http\Controllers\giant;

use App\Http\Controllers\Controller;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;
use DB;

class EditTableDetailsController extends Controller
{
    use SchemaTrait;
    public function update(Request $request , $tableName){
        
        //Check if operationis allowed
        $allowed=['users', 'clients', 'auth_branches'];
        if(!in_array($tableName,$allowed)){
            $data=[
                'message'=>'unauthorized edit',
                'status'=>401
            ];
            return response()->json($data, 401);
        }
        //data to update in table
        $data_to_update = $request->input('info.changedFields');
        $id = $request->input('info.id');
        //check if the id is loaded
        if(!$id){
            $data=[
                'message'=>'Unauthorized ',
                'status'=>401,
            ];
            return response()->json($data, 401);
        }
        //fetch the columns neeeded to update 
        $columns=$this->validateColumns($data_to_update, $tableName);
        if(!$columns){
        $data=[
            'message'=>'We do not torrelate this',
            'status'=>401
        ];
        return response()->json($data, 401);
        }

        
        
        //get the details in id
        $validationrules=$this->get_validation_rules($tableName, $columns);
        $validator = Validator::make($request->input('info.changedFields'), $validationrules);
       
        if ($validator->fails()) {
            // Validation failed, handle errors
            $data=[
                'message'=> $validator->errors()->first(),
                'status'=> 401
            ];
            return response()->json($data, 401);
        }
      
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
}
