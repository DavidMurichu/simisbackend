<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

use Schema;
use DB;
use PDO;

class PasswordController extends Controller
{
    use DemonTrait;
    public function changePassword(Request $request)
    {
        // Validate the form data
       
    
        // Get the authenticated user
        $user = JWTAuth::user();
    
        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            $data=[
                "message"=>"wrong Password",
                "status"=>401
            ];
            return response()->json($data,401);
        }
    
        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        $data=[
            "message"=>"password Changes Successfully",
            "status"=>200,
        ];
        return response()->json($data, 200);
    }

    public function get_token(Request $request){
        $auditData=[
            'user_name' => 'david',
            'activity_type' => 'login',
            'ip_address'=>$request->ip(),

        ];
        $this->makeAudit($auditData);


        $token =csrf_token();
        return response()->json($token,200);
    }


    public function getTableDataTypes(Request $request)
{
    // Validate and get table name from request
    $tableName = $request->validate(['table_name' => 'required|string']);
    $tableName = $tableName['table_name'];

    // Check if the table exists
    if (!Schema::hasTable($tableName)) {
        return response()->json(['error' => "Table '$tableName' does not exist."], 404);
    }

    $columns = Schema::getColumnListing($tableName);
    $dataTypes = [];

    // foreach ($columns as $column) {
    //     $type = Schema::getColumnType($tableName, $column);
    //     $dataTypes[$column] = $this->getColumnDetails($tableName, $column)['type'];
    // }

    $dataTypes=$this->getColumnDetails($tableName, $columns[3]);

    return response()->json(['data' => $dataTypes]);
}

private function getColumnDetails($tableName, $column)
{
    // Use PDO query for compatibility across database drivers
    $columnDetails = DB::connection()->getPdo()->query("DESCRIBE `" . $tableName . "` `" . $column . "`")->fetch(PDO::FETCH_ASSOC);

    
    
    return $columnDetails;
}

}
