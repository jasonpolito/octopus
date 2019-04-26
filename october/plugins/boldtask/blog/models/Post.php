<?php namespace Boldtask\Blog\Models;

use Boldtask\Blog\Classes\TagProcessor;
use Cms\Classes\CmsCompoundObject;
use Cms\Classes\Theme;
use ColorThief\ColorThief;
use Markdown;
use Model;
use ToughDeveloper\ImageResizer\Classes\Image;
use Twig;

/**
 * Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];
    protected $jsonable = ['schema', 'main_image_palette'];
    protected $appends = ['fullSlug', 'displayDate'];
    protected $fillable = ['title'];

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
        'content_images' => ['System\Models\File'],
    ];

    public $belongsToMany = [
        'categories' => [
            'Boldtask\Blog\Models\Category',
            'table' => 'boldtask_blog_cats_posts',
            'order' => 'title',
        ],
        'related_posts' => [
            'Boldtask\Blog\Models\Post',
            'table' => 'boldtask_blog_posts_posts',
            'order' => 'title',
            'key' => 'post_id',
            'otherKey' => 'related_id',
        ],
    ];

    public function scopeIsPublished($query)
    {
        return $query->where('published', 1)->orderBy('published_at', 'desc');
    }

    public function getRelatedIds($item)
    {
        return $item['id'];
    }

    public function relatedPosts()
    {
        if ($this->related_posts) {
            $next = $this->nextPost($this)->id;
            $ids = array_map(array($this, 'getRelatedIds'), $this->related_posts->toArray());
            $pos = array_search($next, $ids);
            if ($pos) {
                array_splice($ids, $pos, 1);
            }
            $posts = Post::orderBy('published_at', 'desc')->whereIn('id', $ids)->get();
            if (count($ids) < 3) {
                $additional = Post::orderBy('published_at', 'desc')->whereNotIn('id', array_merge($posts->pluck('id')->toArray(), [$next]))->limit(3 - $posts->count())->get();
                $all = $posts->merge($additional);
                return $all;
            } else {
                return $posts;
            }
        }

        // $catIds = $this->categories()->pluck('id');
        // return Post::where('id', '!=', $this->id)->with(['categories' => function ($query) use ($catIds) {
        //     $query->whereIn('id', $catIds);
        // }])->get();
    }

    public function fullSlug()
    {
        return $this->slug;
    }

    public function meta()
    {
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
            $partial_name = false;
            $parts = explode(' ', trim($matches[1][$i]));
            $partial_name = $parts[0];
            if (count($parts) > 1) {
            }
            if ($partial_name && $partial_name !== 'toc') {
                $partial = CmsCompoundObject::load($theme, "partials/$partial_name.htm")->getTwigContent();
                $markup = Twig::parse($partial, []);
                $content = str_replace($matches[0][$i], "$post_close$markup$post_open", $content);
            }
        }
        $content = self::generateToc($content);
        if (!$preview) {
            $content = self::lazyloadImages($content);
        }
        return $content;
    }

    public static function lazyloadImages($content)
    {
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

    public static function generateToc($content)
    {
        $lt = 'ul';
        $toc = "<div class='md:text-sm'><$lt class='list-toc'>";
        $max_level = 0;
        $last_level = false;
        preg_match_all('/\<p\>\[\[(.*?toc.*?)\]\]\<\/p\>/', $content, $toc_matches, PREG_PATTERN_ORDER);
        if (count($toc_matches)) {
            preg_match_all('/\<h(([2,3,4,5])\>(.*?))\<\/h[2,3,4,5]\>/', $content, $headers, PREG_PATTERN_ORDER);
            for ($i = 0; $i < count($headers[0]); $i++) {
                $title = $headers[3][$i];
                $id = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
                $id = strtolower(preg_replace('/\s/', '-', $id));
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
            $content = str_replace($toc_matches[0], "<div class='pt-8 text-sm md:text-xs tracking-wide uppercase mb-4'>Table of contents</div>$toc</$lt></div>", $content);
        }
        return $content;
    }

    public function parsedContent()
    {
        $content = self::formatHtml($this->content);
        return $content;
    }

    public function getFullSlugAttribute()
    {
        return $this->fullSlug();
    }

    public function getDisplayDateAttribute()
    {
        return $this->published_at;
    }

    public static function getImageColors($img)
    {
        if (!$img) {
            return;
        }

        $filename = base_path(\Config::get('cms.storage.media.path') . $img);
        $image = imagecreatefromstring(file_get_contents($filename));
        $main = ColorThief::getColor($image);
        $palette = ColorThief::getPalette($image, 8);
        imagedestroy($image);

        foreach ($palette as &$rgb) {
            $rgb = sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
        }

        $main = sprintf("#%02x%02x%02x", $main[0], $main[1], $main[2]);

        return [
            'main' => $main,
            'palette' => $palette,
        ];
    }

    public static function searchFor($value)
    {
        return self::where('title', 'like', '%' . $value . '%')->orWhere('content', 'like', '%' . $value . '%')->get();
    }

    public function nextPost($post)
    {
        $next = Post::orderBy('published_at', 'desc')->where('published_at', '<', $post->published_at)->first();
        if (!$next) {
            $next = Post::orderBy('published_at', 'desc')->first();
        }
        return $next;
    }

    public function beforeSave()
    {
        $colors = self::getImageColors($this->main_image);
        if ($colors) {
            $this->read_time = max(round(count(explode(' ', $this->content)) / 150), 1);
            $this->main_image_color = $colors['main'];
            $this->main_image_palette = $colors['palette'];
        }
    }

}
