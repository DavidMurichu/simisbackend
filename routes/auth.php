<?php
use App\Http\Controllers\auth\PasswordController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\auth\VerifyOtpController;
// use App\Http\Controllers\AuthController;
use App\Http\Controllers\auth\LogInController;
use App\Http\Controllers\auth\LogOutController;

use App\Http\Controllers\test\LabController;
use App\Http\Controllers\test\LabHelperController;
use App\Http\Controllers\user\UserController;
use Illuminate\Support\Facades\Route;



Route::group([
    'middleware' => ['api', 'throttle:apiLimiter'],
    'prefix' => 'auth'
], function () {

Route::post("/2FAcode", [VerifyOtpController::class, 'otp_authenticate']);
Route::post('/login', [LogInController::class, 'login']);
Route::post("/register", [RegisterController::class, "register"]);
Route::post("changepassword", [PasswordController::class, 'changePassword'])->middleware('jwt.auth');
Route::post("/logout", [LogOutController::class, 'logout'])->middleware('jwt.auth');
Route::post("/deactivate", [UserController::class, 'deactivate'])->middleware('jwt.auth');


});







Route::get("/token", [PasswordController::class, 'get_token']);
Route::get("/home/lab", [LabController::class, 'get_table_data']);
Route::get("/home/getValidations", [LabHelperController::class, 'getTableColumns']);



// Route::get("/token", [PasswordController::class, 'get_token']);
