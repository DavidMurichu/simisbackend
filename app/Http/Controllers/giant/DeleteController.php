<?php

namespace App\Http\Controllers\giant;

use App\Http\Controllers\Controller;
use DB;
use Schema;
use Log;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function delete(Request $request, $tableName){
        $allowed=config('crud.get');
        if(!in_array($tableName,$allowed)){
            $data=[
                'message'=>'unauthorized request',
                'status'=>401
            ];
            return response()->json($data, 401);
        }
        $id= $request->input("id");
        if(!$id){
            $data=[
                "message"=>"error deleting data",
                "status"=>400
            ];
            return response()->json($data, 400);
        }
        if (!Schema::hasTable($tableName)) {
            return response()->json(['message' => 'Table does not exist'], 404);
        }

        // Delete the record from the specified table based on the ID
        try {
            DB::table($tableName)->where('id', $id)->delete();
            return response()->json(['message' => 'Record deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to delete record', 'error' => $e->getMessage()], 500);
        }
    }
}
