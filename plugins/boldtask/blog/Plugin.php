<?php namespace Boldtask\Blog;

use System\Classes\PluginBase;
use Boldtask\Blog\Classes\TagProcessor;

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
            ],
            'Boldtask\Blog\FormWidgets\ImageCropper' => [
                'label' => 'Image Cropper',
                'code' => 'imagecropper'
            ]
        ];
    }


    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        /*
         * Register the image tag processing callback
         */
        TagProcessor::instance()->registerCallback(function($input, $preview) {
            if (!$preview) {
                return $input;
            }
            return preg_replace('|\<img src="image" alt="([0-9]+)"([^>]*)\/>|m',
                '<span class="image-placeholder" data-index="$1">
                    <span class="upload-dropzone">
                        <span class="label">Click or drop an image...</span>
                        <span class="indicator"></span>
                    </span>
                </span>',
            $input);
        });

    }

    public function registerSettings()
    {
    }
}
