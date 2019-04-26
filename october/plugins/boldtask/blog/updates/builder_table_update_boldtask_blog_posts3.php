<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateBoldtaskBlogPosts3 extends Migration
{
    public function up()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->dateTime('updated_at_override')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->dropColumn('updated_at_override');
        });
    }
}