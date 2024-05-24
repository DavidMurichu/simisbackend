<?php

namespace App\Traits;

use App\Models\Audit;
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
}
