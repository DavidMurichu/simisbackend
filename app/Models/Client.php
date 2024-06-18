<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'name',
        'city',
        'town',
        'neighbourhood',
        'contactname',
        'website',
        'streetaddress',
        'mobile',
        'email',
        'remarks',
        'createdby',
        'createdon',
        'is_active',
        'ipaddress',
        'lasteditedby',
        'lasteditedon',
        'plotno',
        'clientno',
        'contactmobile',
        // 'companyLocation',
        // 'companyFacebook',
        // 'companyTwitter',
        // 'companyYoutube',
        // 'showCompanyLogo',
    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
