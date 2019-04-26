<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateBoldtaskBlogCatsPosts extends Migration
{
    public function up()
    {
        Schema::create('boldtask_blog_cats_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('category_id');
            $table->integer('post_id');
            $table->primary(['category_id','post_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('boldtask_blog_cats_posts');
    }
}