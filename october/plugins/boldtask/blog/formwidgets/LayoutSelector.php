<?php namespace Boldtask\Blog\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Backend\Models\BrandSetting;

class LayoutSelector extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name' => 'LayoutSelector',
            'description' => 'test',
        ];
    }

    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('layouts');
    }

    public function prepareVars()
    {
        $this->vars['color'] = BrandSetting::get('secondary_color');
        $this->vars['layouts'] = [
            'default' => 'Default',
            'single_column' => 'Single Column',
            'no_image' => 'No Image',
            'fancy' => 'Fancy',
        ];
        $this->vars['id'] = $this->model->id;
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue() ? $this->getLoadValue() : 'default';
    }

    public function loadAssets()
    {
        $this->addCss('css/layoutselector.css');
    }

}
