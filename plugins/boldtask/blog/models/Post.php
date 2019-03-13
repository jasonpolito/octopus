<?php namespace Boldtask\Blog\Models;

use Model;
use Cms\Classes\CmsCompoundObject;
use Boldtask\Blog\Classes\TagProcessor;
use Cms\Classes\Theme;
use ToughDeveloper\ImageResizer\Classes\Image;
use Markdown;
use Twig;

/**
 * Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];
    protected $jsonable = ['schema'];
    protected $appends = ['fullSlug', 'displayDate'];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'boldtask_blog_posts';


    public $attachMany = [
        'content_images' => ['System\Models\File']
    ];

    public $belongsToMany = [
        'categories' => [
            'Boldtask\Blog\Models\Category',
            'table' => 'boldtask_blog_cats_posts',
            'order' => 'title'
        ],
        'related_posts' => [
            'Boldtask\Blog\Models\Post',
            'table' => 'boldtask_blog_posts_posts',
            'order' => 'title',
            'key' => 'post_id',
            'otherKey' => 'related_id',
        ]
    ];

    public function scopeIsPublished($query) {
        return $query->where('published', 1);
    }

    public function relatedPosts() {
        if ($this->related_posts) return $this->related_posts;
        $catIds = $this->categories()->pluck('id');
        return Post::where('id', '!=', $this->id)->with(['categories' => function($query) use ($catIds) {
            $query->whereIn('id', $catIds);
        }])->get();
    }

    public function fullSlug() {
        return $this->slug;
    }

    public function meta() {
        return [
            'title' => $this->meta_title ? $this->meta_title : $this->title,
            'description' => $this->meta_description ? $this->meta_description : $this->excerpt,
        ];
    }

    public static function formatHtml($input, $preview = false)
    {
        $content = Markdown::parse(trim($input));
        if ($preview) {
            $content = str_replace('<pre>', '<pre class="prettyprint">', $content);
        }
        $content = TagProcessor::instance()->processTags($content, $preview);
        $theme = Theme::getActiveTheme();
        $post_open = CmsCompoundObject::load($theme, "partials/post/layouts/partials/post_open.htm")->getTwigContent();
        $post_close = CmsCompoundObject::load($theme, "partials/post/layouts/partials/post_close.htm")->getTwigContent();
        preg_match_all('/\[\[(.*?)\]\]/', $content, $matches, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $partial_name = trim($matches[1][$i]);
            if ($partial_name !== 'toc') {
                $partial = CmsCompoundObject::load($theme, "partials/$partial_name.htm")->getTwigContent();
                $markup = Twig::parse($partial, []);
                $content = str_replace($matches[0][$i], "$post_close$markup$post_open", $content);
            }
        }
        $content = self::generateToc($content);
        $content = self::lazyloadImages($content);
        return $content;
    }

    public static function lazyloadImages($content) {
        preg_match_all('/(\<img src="(.*?)".*?alt="(.*?)" \/>)/', $content, $matches, PREG_PATTERN_ORDER);
        $theme = Theme::getActiveTheme();
         for ($i = 0; $i < count($matches[0]); $i++) {
            $src = $matches[2][$i];
            $alt = $matches[3][$i];
            $path = '/storage' . explode('/storage', $src)[1];
            $image = new Image(base_path($path));
            $resized = $image->resize(20);
            $partial = CmsCompoundObject::load($theme, "partials/blog_picture.htm")->getTwigContent();
            $markup = Twig::parse($partial, [
                'src' => $src,
                'alt' => $alt,
                'resized' => $resized,
            ]);
            $content = str_replace($matches[0][$i], $markup, $content);
        }
        return $content;
    }

    public static function generateToc($content) {
        $lt = 'ul';
        $toc = "<$lt class='md:text-sm mb-8'>";
        $max_level = 0;
        $last_level = false;
        preg_match_all('/\<p\>\[\[(.*?toc.*?)\]\]\<\/p\>/', $content, $toc_matches, PREG_PATTERN_ORDER);
        if (count($toc_matches)) {
            preg_match_all('/\<h(([2,3,4,5])\>(.*?))\<\/h[2,3,4,5]\>/', $content, $headers, PREG_PATTERN_ORDER);
            for ($i=0; $i < count($headers[0]); $i++) { 
                $title = $headers[3][$i];
                $id = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
                $id = strtolower(preg_replace('/\s/', '_', $id));
                $level = (int) $headers[2][$i];
                $link = "<div><a smooth-scroll href='#$id'>$title</a></div>";
                $toc .= "<li>$link</li>";
                $id_added = str_replace(">", " id='$id'>", $headers[0][$i]);
                $content = str_replace($headers[0][$i], $id_added, $content);
                $last_level = $level;
                $max_level = max($level, $max_level);
            }
            while ($max_level > $last_level) {
                $toc .= "</$lt>";
                $max_level--;
            }
            $content = str_replace($toc_matches[0], "<div class='text-sm md:text-xs tracking-wide uppercase mb-4'>Table of contents</div>$toc</$lt>", $content);
        }
        return $content;
    }

    public function parsedContent() {
        $content = self::formatHtml($this->content);
        return $content;
    }

    public function getFullSlugAttribute() {
        return $this->fullSlug();
    }

    public function getDisplayDateAttribute() {
        return $this->updated_at_override ? $this->updated_at_override : $this->updated_at;
    }


    public static function searchFor($value) {
        return self::where('title', 'like', '%'. $value . '%')->orWhere('content', 'like', '%'. $value . '%')->get();
    }

}
