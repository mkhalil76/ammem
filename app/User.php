<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    protected $appends = ['activity', 'organization', 'interest', 'job', 'photo'];//,'','',''

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function Activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function Photo()
    {
        return $this->belongsTo(Media::class, 'photo_id');
    }

    public function Organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function Interest()
    {
        return $this->belongsTo(Interest::class, 'interest_id');
    }

    public function Job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function Groups()
    {
        return $this->belongsToMany(Group::class, 'user_groups', 'user_id', 'group_id');
    }

    public function getActivityAttribute()
    {
        return $this->Activity()->first();
    }

    public function getOrganizationAttribute()
    {
        return $this->Organization()->first();
    }

    public function getPhotoAttribute()
    {
        return $this->Photo()->first();
    }

    public function getJobAttribute()
    {
        return $this->Job()->first();
    }

    public function getInterestAttribute()
    {
        return $this->Interest()->first();
    }

    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->orWhere('mobile', $identifier)->first();
    }
    public function getGroupsAttribute()
    {
        return $this->Groups()->get();
    }
}