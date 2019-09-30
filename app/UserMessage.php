<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class UserMessage extends Model
{
    //
    use SyncsWithFirebase;
}
