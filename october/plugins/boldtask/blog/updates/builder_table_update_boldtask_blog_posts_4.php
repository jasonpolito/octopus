<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateBoldtaskBlogPosts4 extends Migration
{
    public function up()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->boolean('published')->nullable();
            $table->renameColumn('is_featured', 'featured');
            $table->renameColumn('updated_at_override', 'published_at');
        });
    }
    
    public function down()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->dropColumn('published');
            $table->renameColumn('featured', 'is_featured');
            $table->renameColumn('published_at', 'updated_at_override');
        });
    }
}
