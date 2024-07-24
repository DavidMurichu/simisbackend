<?php


use App\Http\Controllers\giant\AddDataController;
use App\Http\Controllers\giant\DeleteController;
use App\Http\Controllers\giant\EditTableDetailsController;
use App\Http\Controllers\giant\GetTableDetailsController;
use App\Http\Controllers\giant\MassAddController;
use App\Http\Controllers\unique\InvoicesController;
use App\Http\Controllers\unique\PaymentController;
use App\Http\Controllers\unique\PromotedStudentsController;
use App\Http\Controllers\unique\ServicesController;
use App\Http\Controllers\unique\StudentController;
use App\Http\Controllers\unique\StudentReportingController;

Route::group([
    // 'middleware'=> 'jwt.auth',
    'prefix'=>'home'
],function () {
//

Route::get("/get_data/{tableName}/{getForeign?}/{id?}", [GetTableDetailsController::class, 'get_table_data']);
Route::post("/edit/{tableName}", [EditTableDetailsController::class, 'update']);
Route::post("/delete/{tableName}", [DeleteController::class, 'delete']);
Route::post("/add_data/{tableName}", [AddDataController::class, 'create']);
Route::get("/promoted", [PromotedStudentsController::class, 'getAllPromoted']);
# student 
Route::post("/register/student", [StudentController::class, 'RegisterStudent']);

Route::get("/promoted/students/{tableName?}", [PromotedStudentsController::class, 'getAllPromoted']);
Route::post("/promoted/students/{tableName?}", [PromotedStudentsController::class, 'getAllPromoted']);
Route::post("/get_non_reported_students", [PromotedStudentsController::class, 'getAllPromotedStudents']);
Route::post("/student_transition/{tableName}", [PromotedStudentsController::class, 'promoteOrDemoteStudent']);
Route::post("/report", [StudentReportingController::class, 'studentClassTermReporting']);
Route::post("/voteheads", [StudentReportingController::class, 'studentClassTermReporting']);
// invoices
Route::post("/create_invoice", [InvoicesController::class, 'processInvoice']);
Route::post("/create_arear", [InvoicesController::class, 'create_arear']);
Route::post("/get_invoice_data", [InvoicesController::class, 'getInvoicingData']);
Route::post("/delete_arear", [InvoicesController::class, 'delete_arear']);
Route::post("/reverse-invoice", [InvoicesController::class, 'reverseInvoice']);
Route::get('/invoice-students', [StudentController::class, 'index']);
Route::post('/services', [ServicesController::class, 'addService']);

//payments

Route::post('/add_payment', [PaymentController::class, 'processPayment']);
Route::post('/get_payment_data', [PaymentController::class, 'getstudents']);



}
);