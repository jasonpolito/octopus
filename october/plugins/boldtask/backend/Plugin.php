<?php namespace Boldtask\Backend;

use Cms\Classes\Theme;
use Cms\Models\ThemeData;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerFormWidgets()
    {
    }

    public function boot()
    {

        ThemeData::extend(function ($model) {
            $model->addDynamicMethod('listPartials', function ($query) {
                $theme = Theme::getActiveTheme();
                $path = $theme->getPath() . '/partials/menu/partials/';
                $files = self::getFiles($path);
                return $files;
            });
        });
    }
    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
    }

    public function registerSettings()
    {
    }

    public static function getFiles($path)
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $files = array();
        foreach ($rii as $file) {
            if (!$file->isDir()) {
                $key = $file->getFilename();
                $files[$key] = explode('.', $key)[0];
            }
        }
        return $files;
    }

}
