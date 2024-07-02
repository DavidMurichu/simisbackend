<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchFeeStructureVoteHead extends Model
{
    use HasFactory;
  
        protected $fillable = [
            'classid',
            'termid',
            'voteheadid',
            'amount',
            'status',
            'remarks',
            'createdby',
            'lasteditedby',
            'ipaddress',
            'is_active',
        ];

        public function voteHead(){
            return $this->belongsTo(SchVoteHead::class, 'voteheadid', 'name');
        }

        public function getForeign(){
            $voteheadId=$this->voteHead->id;
            return ['voteheads'=>[
                'id'=>$voteheadId
            ]];
        }
        public function get_fillable(){
            return $this->fillable;
          }
      

        public function actions(){
            
        }
    
}
