<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use DemonTrait;
    public function deactivate(Request $request, $tableName)
    {
        try {
            // Validate input parameters
            $request->validate([
                'admin_id' => 'required',
                'user_id' => 'required',
            ]);
    
            $admin_id = $request->input("admin_id");
            $user_id = $request->input("user_id");
    
            // Check if the user exists
            $user = User::find($user_id);
            if (!$user) {
                return response()->json(['message' => 'User not found', 'status' => 404], 404);
            }
    
            // Check if the user is already inactive
            if ($user->active == 0) {
                return response()->json(['message' => 'User is already inactive', 'status' => 401], 401);
            }
    
            // Deactivate the user
            $user->active = 0;
            $user->save();
    
            // Audit the deactivation
            $auditData = [
                'user_id' => $user_id,
                'activity_type' => 'deactivated',
                'user_agent' => $admin_id
            ];
            $this->makeAudit($auditData);
    
            // Return success response
            return response()->json(['message' => 'User deactivated', 'status' => 200], 200);
        } catch (\Exception $e) {
            // Log and return error response
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error deactivating user', 'status' => 500], 500);
        }
    }
}
