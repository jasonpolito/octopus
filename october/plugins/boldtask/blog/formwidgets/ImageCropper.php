<?php namespace Boldtask\Blog\FormWidgets;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use System\Classes\MediaLibrary;

/**
 * Media Finder
 * Renders a record finder field.
 *
 *    image:
 *        label: Some image
 *        type: media
 *        prompt: Click the %s button to find a user
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ImageCropper extends FormWidgetBase
{
    //
    // Configurable properties
    //

    /**
     * @var string Prompt to display if no record is selected.
     */
    public $prompt = 'backend::lang.mediafinder.default_prompt';

    /**
     * @var string Display mode for the selection. Values: file, image.
     */
    public $mode = 'file';

    /**
     * @var int Preview image width
     */
    public $imageWidth;

    /**
     * @var int Preview image height
     */
    public $imageHeight;

    //
    // Object properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'media';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'mode',
            'prompt',
            'imageWidth',
            'imageHeight',
        ]);

        if ($this->formField->disabled) {
            $this->previewMode = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('imagecropper');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $value = $this->getLoadValue();
        $this->vars['model'] = $this->model;
        $this->vars['focus'] = $this->model->main_image_focus;
        $this->vars['value'] = $value;
        $this->vars['imageUrl'] = $value ? MediaLibrary::url($value) : '';
        $this->vars['field'] = $this->formField;
        $this->vars['prompt'] = str_replace('%s', '<i class="icon-folder"></i>', trans($this->prompt));
        $this->vars['mode'] = $this->mode;
        $this->vars['imageWidth'] = $this->imageWidth;
        $this->vars['imageHeight'] = $this->imageHeight;
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        $this->model->main_image_focus = post('main_image_focus');
        $this->model->save();

        if ($this->formField->disabled || $this->formField->hidden) {
            return FormField::NO_SAVE_DATA;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addJs('js/fileupload.js', 'core');
        $this->addCss('css/fileupload.css', 'core');
    }
}
