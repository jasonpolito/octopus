<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateBoldtaskBlogPostsPosts extends Migration
{
    public function up()
    {
        Schema::create('boldtask_blog_posts_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('related_id')->unsigned();
            $table->primary(['post_id','related_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('boldtask_blog_posts_posts');
    }
}