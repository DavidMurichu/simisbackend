<?php

namespace App\Traits;

use App\Http\Controllers\giant\AddDataController;
use App\Http\Controllers\giant\DeleteController;
use App\Http\Controllers\giant\EditTableDetailsController;
use App\Models\Audit;
use App\Models\SchemaAlert;
use Illuminate\Http\Request;

use Log;

trait DemonTrait
{

    public function schemaAlert($D_Alert){
        try{
            SchemaAlert::create($D_Alert);
        }catch(\Exception $e){
        Log::error(json_encode($D_Alert) . 'Schema Alert ' . $e->getMessage());
        }
    }
    public function makeAudit( $auditData) {
        try{
        $audit=Audit::create($auditData);
        // Log::info('Audit Created status: '.$audit);
        return $auditData;
    }catch(\Exception $e){
        Log::error(json_encode($auditData) . 'Audit ' . $e->getMessage());
    }
    }


    public function demonDelete($tableName, $id){
        $deleteController=new DeleteController;
        $data=[
            'id'=>$id
        ];

        $response=$deleteController->delete(new Request($data), $tableName);
        return $response;

    }
    public function demonEdit($tableName, $data){
         // Instantiate EditTableDetailsController
         $editController = new EditTableDetailsController();
         // Call the update method
         $response = $editController->update(new Request($data), $tableName);
         return $response;
    }
    public function demonAdd($request, $tableName){
        if (is_array($request)) {
            // Create a new Request object from the array data
            $request = new Request($request);
        } elseif (!($request instanceof Request)) {
            throw new \InvalidArgumentException('request must be an array or an instance of Request.');
        }
        // Instantiate EditTableDetailsController
        $addController = new AddDataController();
        // Call the update method
        $response = $addController->create( $request, $tableName);
        return $response;
   }

  public function isAllowed($tableName, string $operation){
    $allowed=config('crud.'.$operation);
    if(!in_array($tableName,$allowed)){
        return false;
    }else{
        return true;
    }
  }
}
