<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Traits\AuthTrait;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Audits;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;


class VerifyOtpController extends Controller
{
    use AuthTrait;
    use DemonTrait;

    public function otp_authenticate(Request $request)
    {
        try {
            // Retrieve and hash the OTP code from the request
            $otpCode = $request->input('code.verificationCode');
            
            $userHash = $this->encrptCode($otpCode);
            Log::info($userHash);

            // Find the user based on the hashed OTP code
            $user = User::where('otp_code', $userHash)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid OTP',
                    'status' => 401,
                ], 401);
            }

            // Check if the OTP has expired
            if ($user->otp_expires_at < Carbon::now()) {
                $user->otp_code = null;
                $user->save();
                
                return response()->json([
                    'message' => 'OTP has expired',
                    'status' => 400,
                ], 400);
            }

            // Generate JWT token
            $token = $this->generateToken($user);

            // Clear the OTP code from the user record
            $user->otp_code = null;
            $user->active=1;
            $user->save();
            $auditData=[
                'user_name' => $user->name,
                'activity_type' => 'login',
        'ip_address'=>$request->ip(),

            ];
            // create an Audit
            $this->makeAudit($auditData);

            return response()->json([
                'status' => 200,
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not create token',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status'=> 500,
            ], 500);
        }
    }
}