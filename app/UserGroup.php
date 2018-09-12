<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Firebase\SyncsWithFirebase;

class UserGroup extends Model
{
    //
    use SoftDeletes;
}
