<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateBoldtaskBlogCategories2 extends Migration
{
    public function up()
    {
        Schema::table('boldtask_blog_categories', function($table)
        {
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('boldtask_blog_categories', function($table)
        {
            $table->dropColumn('title');
            $table->dropColumn('slug');
        });
    }
}
