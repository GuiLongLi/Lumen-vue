<?php

namespace App\Modules;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'slug','title','subtitle','content_html','page_image','meta_description',
        'created_at','updated_at','published_at'
    ];

}
