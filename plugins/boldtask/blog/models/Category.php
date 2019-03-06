<?php namespace Boldtask\Blog\Models;

use Model;

/**
 * Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'boldtask_blog_categories';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsToMany = [
        'posts' => [
            'Boldtask\Blog\Models\Post',
            'table' => 'boldtask_blog_cats_posts',
            'order' => 'title'
        ]
    ];
}
