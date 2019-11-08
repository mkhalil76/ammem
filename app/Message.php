<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Firebase\SyncsWithFirebase;
use Carbon\Carbon;

class Message extends Model
{
    //
    use SoftDeletes;
    use SyncsWithFirebase;

    protected $appends = ['result_survey_count','groups', 'members', 'media', 'surveys', 'user', 'created_date','message_pin_count', 'Replies']; //

    protected $with = ['seen', 'Replies'];

    public function seen()
    {
        return $this->hasMany(UserMessageSeen::class, 'message_id');
    }
    public function getCreatedDateAttribute()
    {
        $carbon = Carbon::parse($this->created_at);
        return $carbon->diffForHumans();
    }

    public function Sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Groups()
    {
        return $this->belongsToMany(Group::class, 'message_groups', 'message_id', 'group_id');
    }

    public function Members()
    {
        return $this->hasMany(UserMessage::class, 'message_id', 'id');
    }

    public function Media()
    {
        return $this->hasMany(Media::class, 'message_id', 'id');
    }


    public function Survey()
    {
        return $this->hasMany(Survey::class, 'message_id', 'id');
    }

    public function Replies()
    {
        return $this->hasMany(Reply::class, 'message_id', 'id')->orderByDesc('id');
    }

    public function getGroupsAttribute()
    {
        return $this->Groups()->first();
    }

    public function getMembersAttribute()
    {
        return $this->Members()->get();
    }

    public function getMediaAttribute()
    {
        return $this->Media()->get();
    }

    public function getSurveysAttribute()
    {
        return $this->Survey()->get();
    }

    public function getResultSurveyCountAttribute()
    {

        $surveys = $this->Survey()->get();
        $percentage_result = 0.0;
        foreach ($surveys as $survey)
            $percentage_result += $survey->percentage_result;

        return $percentage_result;
    }

    public function getUserAttribute()
    {
        return $this->Sender()->first();
    }

    public function getMessagePinCountAttribute()
    {
        return  $this->where('user_id', auth()->user()->id)->where('pin', 1)->count();

    }

   public function getRepliesAttribute()
    {
        return $this->Replies()->get();
    }
}
