<?php namespace Boldtask\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateBoldtaskBlogPosts7 extends Migration
{
    public function up()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->string('main_image_color')->nullable();
            $table->text('main_image_palette')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('boldtask_blog_posts', function($table)
        {
            $table->dropColumn('main_image_color');
            $table->dropColumn('main_image_palette');
        });
    }
}
