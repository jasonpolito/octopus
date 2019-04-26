<?php namespace Boldtask\Blog\Controllers;

use Backend\Classes\Controller;
use Boldtask\Blog\Models\Post;
use Newride\InlineAssets\Plugin as Assets;
use BackendMenu;
use Cms\Classes\Theme;
use File;
use System\Classes\CombineAssets;
use Less_Parser;

class Blog extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Boldtask.Blog', 'blog', 'bt-posts');
    }

    public function create() {
        $this->bodyClass = 'compact-container';
        $this->addJs('/plugins/boldtask/blog/assets/js/post-form.js');
        $this->addCss('/plugins/boldtask/blog/assets/css/post-form.css');
        $this->addCss("themes/octopus/assets/css/backend.less");
        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null) {
        $this->bodyClass = 'compact-container';
        $this->addJs('/plugins/boldtask/blog/assets/js/post-form.js');
        $font_family = Theme::getActiveTheme()->getCustomData()['font_family'];
        $this->addCss("https://fonts.googleapis.com/css?family=$font_family");
        return $this->asExtension('FormController')->update($recordId);
    }

    public function onRefreshPreview()
    {
        $data = post('Post');
        $previewHtml = Post::formatHtml($data['content'], true);
        return [
            'preview' => $previewHtml
        ];
    }

}
