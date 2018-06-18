<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v1\controllers\BaseController;

/**
 *
 * @api {get} /videos   请求视频（图片）文件信息
 * @apiName  获取视频（图片）文件信息
 * @apiGroup 图文消息
 * @apiSuccess {String} item  视频（图片）列表详情
 * @apiDescription   获取视频（图片）详情
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 * {
 *   item{
 *       "video_av": "视频缩略图",
 *       "video_url": "视频下载地址"
 *       "video_title": "视频标题"
 *       "video_indro": "视频详情"
 *       "video_create_time": "视频创建时间"
 *       "video_view": "视频观看次数"
 *       "video_down_url": "视频下载链接"
 *       "video_text": "文字描述"
 *       "video_desc": "描述"
 *     }
 * _meta{
 *      "totalCount": 总共条数,
 *      "pageCount": 当前页面数量,
 *      "currentPage": 当前页,
 *      "perPage": 共多少页
 * }
 */
/**
 *
 * @api {get} /videos/:id   请求单个视频（图片）文件信息
 * @apiName  获取单个视频（图片）图文消息
 * @apiGroup 图文消息
 * @apiSuccess {String} item  单个视频（图片）列表详情
 * @apiDescription   单个获取视频（图片）详情
 */

class VideosController extends BaseController
{
    public $modelClass = 'common\models\VideoAttribute';

    public function videolist(){

    }
}
