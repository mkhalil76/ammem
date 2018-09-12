<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class MessageGroup extends Model
{
    //
    use SyncsWithFirebase;
}
