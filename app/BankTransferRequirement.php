<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankTransferRequirement extends Model
{
    //

    public function Group(){
        return $this->belongsTo(Group::class,'group_id');
    }
}
