<?php


use App\Http\Controllers\giant\AddDataController;
use App\Http\Controllers\giant\DeleteController;
use App\Http\Controllers\giant\EditTableDetailsController;
use App\Http\Controllers\giant\GetTableDetailsController;
use App\Http\Controllers\giant\MassAddController;
use App\Http\Controllers\unique\InvoicesController;
use App\Http\Controllers\unique\PromotedStudentsController;
use App\Http\Controllers\unique\StudentReportingController;

Route::group([
    // 'middleware'=> 'jwt.auth',
    'prefix'=>'home'
],function () {
//

Route::get("/get_data/{tableName}/{id?}", [GetTableDetailsController::class, 'get_table_data']);
Route::post("/edit/{tableName}", [EditTableDetailsController::class, 'update']);
Route::post("/delete/{tableName}", [DeleteController::class, 'delete']);
Route::post("/add_data/{tableName}", [AddDataController::class, 'create']);
Route::get("/promoted", [PromotedStudentsController::class, 'getAllPromoted']);

Route::get("/promoted/students/{tableName?}", [PromotedStudentsController::class, 'getAllPromoted']);
Route::post("/student_transition/{tableName}", [PromotedStudentsController::class, 'promoteOrDemoteStudent']);
Route::post("/report", [StudentReportingController::class, 'studentClassTermReporting']);
Route::post("/voteheads", [StudentReportingController::class, 'studentClassTermReporting']);
Route::post("/create_invoice", [InvoicesController::class, 'processInvoice']);
Route::post("/create_arear", [InvoicesController::class, 'create_arear']);
Route::post("/delete_arear", [InvoicesController::class, 'delete_arear']);

}
);