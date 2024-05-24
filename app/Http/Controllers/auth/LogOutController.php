<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Audits;
use Log;


class LogOutController extends Controller
{
    use DemonTrait;
public function logout(Request $request)
{
    try {
        //get the token from headers
        $token = JWTAuth::getToken();

        if (!$token) {
            $data=[
                'message' => 'Token not provided',
                'status'=>400
            ];
            return response()->json($data, 400);
        }

        //extract user 
        $user=User::find(JWTAuth::getPayload($token)->get('user_id'));

        //unauthenticate token
        JWTAuth::invalidate($token);

        //deactivate user
        $user->active=0;
        $user->save();

          // create an Audit
        $auditData=[
            'user_name' => $user->name,
            'activity_type' => 'logout',
            'ip_address'=>$request->ip(),

        ];
        $this->makeAudit($auditData);

        $data=[
            'message' => 'User logged out successfully',
            'status'=>200
        ];
        return response()->json($data,200);
    } catch (\Exception $e) {
        $message = $e->getMessage();
        return response()->json(['error' => $message], 500);
    }
}
}
