<?php

namespace App\Traits\service;
use App\Traits\SchemaTrait;
use Illuminate\Http\Request;

trait ServiceTraight
{
    use SchemaTrait;
    public function addService($serviceData){
        $response = $this->demonAdd(new Request($serviceData), 'sch_student_service_invoices');
        $response = $this->demonAdd(new Request($serviceData), 'sch_student_service_invoices');
        $response = $this->demonAdd(new Request($serviceData), 'sch_student_service_invoices');

    }
}
