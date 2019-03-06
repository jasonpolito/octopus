<?php namespace Boldtask\Blog\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

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
    }

    public function update($recordId = null) {
        $this->bodyClass = 'compact-container';
        $this->addJs('/plugins/boldtask/blog/assets/js/post-form.js');
        $this->addCss('/plugins/boldtask/blog/assets/css/post-form.css');
        $this->addCss("themes/octopus/assets/css/backend.less");
        return $this->asExtension('FormController')->update($recordId);
    }
}
