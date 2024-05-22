<?php

namespace App\Traits;

use App\Models\Audits;
use Log;

trait DemonTrait
{
    public function makeAudit( $auditData) {
        try{
        $audit=Audits::create($auditData);
        Log::info('Audit Created status: '.$audit);
    }catch(\Exception $e){
        Log::error($auditData.''.$e->getMessage());
    }


    }
}
