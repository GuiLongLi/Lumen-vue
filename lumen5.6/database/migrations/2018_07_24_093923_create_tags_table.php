<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('tag',100)->unique();
            $table->string('title',100);
            $table->string('subtitle',100);
            $table->string('page_image')->comment('标签图片');
            $table->string('meta_description')->comment('标签介绍');
            $table->boolean('reverse_direction')->default(0)->comment('在文章列表按时间升序排序，默认是降序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
