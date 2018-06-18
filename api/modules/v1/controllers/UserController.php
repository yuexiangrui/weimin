<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v1\controllers\BaseController;




class UserController extends BaseController
{
    public $modelClass = 'common\models\Admin';
    public function actions(){
        return array_merge(parent::actions(),
            [
                'version' => [
                    'class' => 'api\modules\Index2Action',
                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess']
                ],
            ]
        );
    }
}
