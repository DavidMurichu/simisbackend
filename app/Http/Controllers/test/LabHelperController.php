<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Log;
use Str;

use Illuminate\Database\Eloquent\Model;
use Exception;

class LabHelperController extends Controller
{
    use SchemaTrait;




    public function getTableColumns(Request $request)
    {
        $tableName='sch_fee_structure_vote_heads';
        // Check if the table exists
        if (!Schema::hasTable($tableName)) {
            throw new \Exception("Table '$tableName' does not exist.");
        }

        // Get column information
        $columns = DB::select("SHOW COLUMNS FROM $tableName");
        $c_unique=[];

        $columnDetails = [];

        // Loop through the columns to get the field names and types
        foreach ($columns as $column) {
            // $columnDetails[$column->Field] = $column->Type;
            $columnDetails[] = $column->Field;
        }
        // foreach ($columnDetails as $column) {
        //     $is_unique=$this->isColumnUnique($tableName, $column);
        //     // $c_unique[]=$unique;

        // }

        // $dataType=$this->createValidationRules($columnDetails);
        $data=$this->get_validation_rules($tableName, $columnDetails);
        
        $is_foreignkey=$this->getForeignKeyDetails($tableName, 'parent_id');
        
        
        return $data;
    }


    





  
}


