<?php namespace Boldtask\Blog;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerFormWidgets() {
        return [
            'Boldtask\Blog\FormWidgets\LayoutSelector' => [
                'label' => 'Layout selector',
                'code' => 'layout'
            ]
        ];
    }

    public function registerSettings()
    {
    }
}
