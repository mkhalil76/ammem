<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    //
//    use SyncsWithFirebase;

    protected $appends = ['percentage_result'];

    public function SurveyResults()
    {
        return $this->hasMany(SurveyResult::class, 'choice_id', 'id');
    }

    public function Message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function getPercentageResultAttribute()
    {
        return $this->SurveyResults()->count();
    }
}
