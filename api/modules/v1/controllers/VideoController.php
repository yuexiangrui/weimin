<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v1\controllers\BaseController;




class VideoController extends BaseController
{
    public $modelClass = 'common\models\VideoAttribute';
}
