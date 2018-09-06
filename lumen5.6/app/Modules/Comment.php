<?php

namespace App\Modules;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments_article';

    protected $fillable = [
        'parent_id','body','created_at',
        'post_id',
        'updated_at'
    ];

}
