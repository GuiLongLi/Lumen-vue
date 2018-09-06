<?php

namespace App\Modules;

use Illuminate\Database\Eloquent\Model;

class PostTagPivot extends Model
{
    protected $table = 'post_tag_pivot';

    protected $fillable = [
        'post_id','tag_id'
    ];

}
