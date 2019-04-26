<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateBoldtaskBlogPosts5 extends Migration
{
    public function up()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->text('schema')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->dropColumn('schema');
        });
    }
}
