<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v1\controllers\BaseController;

/**
 *
 * @api {get} /tags   文件标签
 * @apiName  获取文件标签
 * @apiGroup 标签
 * @apiSuccess {String} item  文件标签
 * @apiDescription   文件标签
 */
class TagsController extends BaseController
{
    public $modelClass = 'common\models\VideoTag';
}
