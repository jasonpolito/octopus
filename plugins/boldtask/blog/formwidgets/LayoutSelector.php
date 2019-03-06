<?php namespace Boldtask\Blog\FormWidgets;

use Backend\Models\BrandSetting;
use Backend\Classes\FormWidgetBase;
use Config;

class LayoutSelector extends FormWidgetBase {
    public function widgetDetails() {
        return [
            'name' => 'LayoutSelector',
            'description' => 'test'
        ];
    }

    public function render() {
        $this->prepareVars();
        return $this->makePartial('layouts');
    }

    public function prepareVars() {
        $this->vars['color'] = BrandSetting::get('secondary_color');
        $this->vars['layouts'] = [
            'default' => 'Default',
            'single_column' => 'Single Column',
            'no_image' => 'No Image',
        ];
        $this->vars['id'] = $this->model->id;
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
    }

    public function loadAssets() {
        $this->addCss('css/test.css');
    }

}