<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    //
//    use SyncsWithFirebase;

//    protected $appends = ['name'];

    public function getNameAttribute($value)
    {
//        /home/wqfquran/public_html/3mmem.com/ammem/assets/upload/151325431214.png
        return url('public/assets/upload/'.$value);

    }
    /*
        protected $appends = ['message'];

        public function Message()
        {
            return $this->belongsTo(Message::class, 'message_id');
        }

        public function getMessageAttribute()
        {
            return $this->Message()->first();
        }*/
}
