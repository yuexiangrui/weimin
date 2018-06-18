<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v1\controllers\BaseController;
use yii\data\ActiveDataProvider;

/**
 * @api {get} /files
 * @apiName  获取所有文件信息
 * @apiGroup 图片列表
 * @apiSuccess {String} item  文件列表详情
 * @apiSuccess {String} _meta 当前页面数量
 * @apiSuccess {String} _links 分页信息
 * @apiDescription   获取文件详情
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 * {
 *   item{
 *       "file_url": "文件下载地址",
 *       "file_title": "文件标题"
 *       "file_view": "文件浏览次数"
 *       "file_create_time": "文件创建时间"
 *       "file_down_number": "文件下载数量"
 *     }
 * _links{
 *      "self":"当前页链接",
 *      "next":"下一页链接",
 *      "last":"最后一页链接",
 * }
 * _meta{
 *      "totalCount": 总共条数,
 *      "pageCount": 当前页面数量,
 *      "currentPage": 当前页,
 *      "perPage": 共多少页
 * }
 */
/**
 *
 * @api {get} /files/:id   请求单个文件文件信息
 * @apiName  请求单个文件
 * @apiGroup 图片列表
 * @apiSuccess {String} item  文件详情
 * @apiDescription   获取文件详情
 */
class FilesController extends BaseController
{
    public $modelClass = 'common\models\FileAttribute';
}
