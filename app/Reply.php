<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class Reply extends Model
{
    //
    use SyncsWithFirebase;
    protected $appends = ['original_message', 'user','created_date'];


    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();

    }
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function getUserAttribute()
    {
        return $this->User()->first();
    }

    public function getOriginalMessageAttribute()
    {
        return $this->Message()->first();
    }
}
