<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class SurveyResult extends Model
{
    //
//    use SyncsWithFirebase;

    public function Survey()
    {
        return $this->belongsTo(Survey::class,'choice_id');

    }
}
