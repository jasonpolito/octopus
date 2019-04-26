<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateBoldtaskBlogPosts8 extends Migration
{
    public function up()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->string('read_time')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->dropColumn('read_time');
        });
    }
}
