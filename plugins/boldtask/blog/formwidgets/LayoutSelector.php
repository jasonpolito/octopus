<?php namespace Boldtask\Blog\FormWidgets;

use Backend\Models\BrandSetting;
use Backend\Classes\FormWidgetBase;
use System\Classes\CombineAssets;
use Cms\Classes\Theme;
use Newride\InlineAssets\Plugin as Assets;
use Cms\Classes\Asset;
use Config;
use File;
use Less_Parser;

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
        // $assets = $test->inlineAssets(['assets/css/theme.less']);
        // $parser = new Less_Parser(['compress' => true]);
        // $parser->ModifyVars(Theme::getActiveTheme()->getCustomData()->getAssetVariables());

        // $parser->parse(File::get(Theme::getActiveTheme()->getPath() . '/assets/css/theme.less'));
        $this->vars['test'] = ''; //json_encode(Theme::getActiveTheme()->getCustomData()['font_family']);
        // $this->vars['test'] = $name;
        $this->vars['color'] = BrandSetting::get('secondary_color');
        $this->vars['layouts'] = [
            'default' => 'Default',
            'single_column' => 'Single Column',
            'no_image' => 'No Image',
        ];
        $this->vars['id'] = $this->model->id;
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue() ?  $this->getLoadValue() : 'default';
    }

    public function loadAssets() {
        $this->addCss('css/test.css');
    }

}