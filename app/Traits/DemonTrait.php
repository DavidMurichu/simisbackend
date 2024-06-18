<?php

namespace App\Traits;

use App\Http\Controllers\giant\AddDataController;
use App\Http\Controllers\giant\EditTableDetailsController;
use App\Models\Audit;
use Illuminate\Http\Request;

use Log;

trait DemonTrait
{
    public function makeAudit( $auditData) {
        try{
        $audit=Audit::create($auditData);
        // Log::info('Audit Created status: '.$audit);
        return $auditData;
    }catch(\Exception $e){
        Log::error(json_encode($auditData) . ' ' . $e->getMessage());
    }
    }

    public function demonEdit($tableName, $data){
         // Instantiate EditTableDetailsController
         $editController = new EditTableDetailsController();
         // Call the update method
         $response = $editController->update(new Request($data), $tableName);
         return $response;
    }
    public function demonAdd($request, $tableName){
        // Instantiate EditTableDetailsController
        $addController = new AddDataController();
        // Call the update method
        $response = $addController->create(new Request($request->all()), $tableName);
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
