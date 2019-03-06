<?php namespace Boldtask\Blog\FormWidgets;

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
        $this->vars['layouts'] = [
            'default' => 'Default',
            'alt' => 'Poop',
        ];
        $this->vars['id'] = $this->model->id;
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
    }

}