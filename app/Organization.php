<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Firebase\SyncsWithFirebase;

class Organization extends Model
{
    //
    use SoftDeletes;
}
