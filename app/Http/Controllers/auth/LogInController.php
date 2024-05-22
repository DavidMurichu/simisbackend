<?php

namespace App\Http\Controllers\auth;

use App\Models\Audits;
use App\Traits\AuthTrait;
use App\Http\Controllers\Controller;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;




class LogInController extends Controller
{
    //Log in
    use AuthTrait;
    use DemonTrait;

public function login(Request $request)
{

    try {
        //Step 1 Validate and sanitize request data
        $request->validate([
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);


        // Find user by email
        $user = User::where('email', strtolower(trim($request->email)))->first();
        // $user->active = 1;
        // $user->save();
        // Check user existence and verify password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            $data=[
                "message"=> "Invalid login credentials",
                "status"=>401
            ];
            return response()->json($data, 200);
        }
        
        //check if user is active (if active login else move to 2FA)
        if(!$user->active){
        // 2FA 
        $data=$this->twoFA($user);
        return response()->json($data, $data['status']);
    }
    try{
        $token = JWTAuth::fromUser($user);
        JWTAuth::parseToken()->authenticate();

    }catch(\Exception $e){
        $token = $this->generateToken($user);
    }
    // get existing token
   
     // create an Audit
    $auditData=[
        'user_id' => $user->id,
        'activity_type' => 'login',
    ];
    $this->makeAudit($auditData);
    $data=[
        'status'=>200,
        'active'=>1,
        'token'=> $token
    ];
    return response()->json($data,200);
    

    } catch (\Exception $e) {
        Log::error('Login error: ' . $e->getMessage());
        $data=[
            'message'=>'Some error occured Try again later',
            'status'=> 500
        ];
        return response()->json($data, 500);
    }
}
}
