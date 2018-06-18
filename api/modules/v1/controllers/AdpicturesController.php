<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v1\controllers\BaseController;

/**
 *
 * @api {get} /adpictures   请求轮播图文件信息
 * @apiName  获取轮播图信息
 * @apiGroup 轮播图
 * @apiSuccess {String} item  轮播图信息列表详情
 * @apiDescription   获取轮播图信息
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 * {
 *   item{
 *       "picture_url": "视频缩略图",
 *       "picture_create_time": "视频下载地址"
 *       "picture_sort": "视频标题"
 *     }
 */
class AdpicturesController extends BaseController
{
    public $modelClass = 'common\models\PictureInfo';
}
