<?php


use App\Http\Controllers\giant\AddDataController;
use App\Http\Controllers\giant\DeleteController;
use App\Http\Controllers\giant\EditTableDetailsController;
use App\Http\Controllers\giant\GetTableDetailsController;

Route::group([
    // 'middleware'=> 'jwt.auth',
    'prefix'=>'home'
],function () {
//

Route::get("/get_data/{tableName}", [GetTableDetailsController::class, 'get_table_data']);
Route::post("/edit/{tableName}", [EditTableDetailsController::class, 'update']);
Route::post("/delete/{tableName}", [DeleteController::class, 'delete']);
Route::post("/add_data/{tableName}", [AddDataController::class, 'create']);

}
);