<?php namespace Boldtask\Blog\FormWidgets;

use Backend\Models\BrandSetting;
use Cms\Classes\Theme;
use Config;
use File;
use Less_Parser;
use Input;
use Request;
use Response;
use Validator;
use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Backend\Controllers\Files as FilesController;
use October\Rain\Filesystem\Definitions as FileDefinitions;
use ApplicationException;
use ValidationException;
use Exception;

class ImageCropper extends FormWidgetBase {
    public function widgetDetails() {
        return [
            'name' => 'ImageCropper',
            'description' => 'test'
        ];
    }

    public function render() {
        $this->prepareVars();
        return $this->makePartial('imagecropper');
    }

    public function prepareVars() {
        $this->vars['id'] = $this->model->id;
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue() ?  $this->getLoadValue() : 'default';
    }

    public function loadAssets() {
        $this->addCss('css/cropper.css');
        $this->addJs('js/cropper.js');
        $this->addJs('js/ImageCropper.js');
    }

    protected function checkUploadPostback()
    {
        if (!($uniqueId = Request::header('X-OCTOBER-FILEUPLOAD')) || $uniqueId != $this->getId()) {
            return;
        }
        try {
            if (!Input::hasFile('file_data')) {
                throw new ApplicationException('File missing from request');
            }
            $fileModel = $this->getRelationModel();
            $uploadedFile = Input::file('file_data');
            $validationRules = ['max:'.$fileModel::getMaxFilesize()];
            if ($fileTypes = $this->getAcceptedFileTypes()) {
                $validationRules[] = 'extensions:'.$fileTypes;
            }
            if ($this->mimeTypes) {
                $validationRules[] = 'mimes:'.$this->mimeTypes;
            }
            $validation = Validator::make(
                ['file_data' => $uploadedFile],
                ['file_data' => $validationRules]
            );
            if ($validation->fails()) {
                throw new ValidationException($validation);
            }
            if (!$uploadedFile->isValid()) {
                throw new ApplicationException('File is not valid');
            }
            $fileRelation = $this->getRelationObject();
            $file = $fileModel;
            $file->data = $uploadedFile;
            $file->is_public = $fileRelation->isPublic();
            $file->save();
            /**
             * Attach directly to the parent model if it exists and attachOnUpload has been set to true
             * else attach via deferred binding
             */
            $parent = $fileRelation->getParent();
            if ($this->attachOnUpload && $parent && $parent->exists) {
                $fileRelation->add($file);
            } else {
                $fileRelation->add($file, $this->sessionKey);
            }
            $file = $this->decorateFileAttributes($file);
            $result = [
                'id' => $file->id,
                'thumb' => $file->thumbUrl,
                'path' => $file->pathUrl
            ];
            Response::json($result, 200)->send();
        }
        catch (Exception $ex) {
            Response::json($ex->getMessage(), 400)->send();
        }
        exit;
    }

}