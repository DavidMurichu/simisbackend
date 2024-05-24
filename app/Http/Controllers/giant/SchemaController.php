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


   //Edit data by use of tablename
  

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
