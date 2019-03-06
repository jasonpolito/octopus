<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateBoldtaskBlogCategories extends Migration
{
    public function up()
    {
        Schema::create('boldtask_blog_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('boldtask_blog_categories');
    }
}
