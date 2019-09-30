<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Group extends Model
{
    //
    use SoftDeletes;

    protected $appends = ['count_members', 'members', 'admin', 'status_group', 'created_date'];

    public function getCreatedDateAttribute()
    {   
        $carbon = Carbon::parse($this->created_at);
        return $carbon->diffForHumans();

    }
    public function getImageAttribute($value)
    {
        return url('public/assets/upload/' . $value);
    }

    public function getStatusGroupAttribute()
    {
        return ($this->status == 'closed') ? 'مغلقة' : 'مفتوحة';

    }

    public function getMembersAttribute()
    {
        return $this->Users()->get();
    }

    public function Users()
    {
        return $this->belongsToMany(User::class, 'user_groups', 'group_id', 'user_id');
    }

    public function getCountMembersAttribute()
    {
        return $this->Users()->count() + 1; // admin member + other members
    }

    public function getAdminAttribute()
    {
        return $this->Admin()->first();
    }

    public function Admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        static::creating(function ($model) {
            $model->slug = $model->id.str_random(30);
        });
    }
}
