<?php

namespace App\Http\Controllers\auth;


use App\Http\Controllers\giant\AddDataController;

use App\Traits\DemonTrait;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;
use App\Traits\AuthTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str; 
use App\Models\User;
use App\Models\Audits;
use Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class RegisterController extends AddDataController
{
    use AuthTrait;
    use DemonTrait;
    use SchemaTrait;
    public function register(Request $request )

{
    try{
        Log::info($request);
    $tableName='users';
    $columns = Schema::getColumnListing($tableName);
    $validationRules = $this->get_validation_rules($tableName, $columns);
    unset($validationRules['password']);
    //validate data for regestration data
    $user_details=$request->user;
    //generate unique username and password for user
    $user_details['password'] = Str::random(8);
    $user_details[ 'created_on']=Carbon::now();
    $user_details[ 'active']=0;

     // Validate the request data
     $validator = Validator::make($user_details, $validationRules);
     if ($validator->fails()) {
        return response()->json([
            'message' => $validator->errors()->first(),
            'status' => 401
        ], 401);
    }
    //create user to the db
    $user = User::create($user_details);
    // //initialize 2FA
    $data=$this->send_reg_mail($user, $user_details['password']);

    // create audit
    $auditData=[
        'user_name' => $user->usename,
        'activity_type' => 'register',
        'ip_address'=>$request->ip(),
    ];

    $this->makeAudit($auditData);


    return response()->json($data, $data['status']);
    // return $columns;
    


}
catch(ValidationException $e){
    Log::error($e->getMessage());
    $data=[
        'message'=> $e->getMessage(),
        'status'=>401
    ];
    return response()->json($data, 401); 
}catch(\Exception $e){
    Log::error($e->getMessage());
    $data=[
        'message'=> $e->getMessage(),
        'status'=>500
    ];
    return response()->json($data,500);

}
}

public function update(Request $request, $id){
    
}

}
