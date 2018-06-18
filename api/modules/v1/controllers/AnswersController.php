<?php

namespace api\modules\v1\controllers;

use common\models\AwsAttach;
use common\models\AwsQuestion;
use yii;
use common\models\AwsAnswer;
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
class AnswersController extends BaseController
{
    public $modelClass = 'common\models\AwsAnswer';

    public function actionNewindex(){
        $params = 'http://www.jiucaishuo.com/uploads/answer';
        $avater = 'http://www.jiucaishuo.com/uploads/avatar/';
        $model = new AwsAnswer();
        $attachs = [];
        $questionid = Yii::$app->request->get('question_id');
        $data = $model->find()->where(['question_id'=>$questionid])->with('questionName','userInfo')->orderBy( 'question_id DESC')->asArray()->one();
        $attachs = $this->parse_attachs($data['answer_content'],true);
        $data['answer_content'] = $this->parse_attachs($data['answer_content']);
        $data['answer_content'] = explode('--img--',$this->parse_attachs($data['answer_content']));
        foreach($data['answer_content'] as $key=>$vvv){
            $data['answer_contents'][($key*2+1)]['type'] = 'p';
            $data['answer_contents'][($key*2+1)]['value'] = $vvv;
        }
        unset($data['answer_content']);
        if($attachs){
            $conf = ['in','id',$attachs];
            $attachInfo = AwsAttach::find()->where($conf)->asArray()->all();
            foreach($attachInfo as $k=>$vv){
                $data['answer_contents'][($k*2+2)]['type'] = 'img';
                $data['add_time'] = date('Y-m-d H:i:s',$vv['add_time']);
                $data['answer_contents'][($k*2+2)]['value'] = $params.'/'.date('Ymd',$vv['add_time']).'/'.$vv['file_location'];
            }
        }
        $data['userInfo']['avatar_file'] = $avater.$data['userInfo']['avatar_file'];
        return $data;
    }

    public function actionQuestionlist(){
        $model = new AwsQuestion();
        $curPage = Yii:: $app->request->get( 'page',1);
        $pageSize = 10;
        $type = Yii:: $app-> request->get( 'type', '');
        $value = Yii:: $app-> request->get( 'value', '');
        $search = ($type&&$value)?[ 'like',$type,$value]: '';
        $query = $model->find()->with('userInfo')->orderBy( 'question_id DESC');
        $data = $model->getPages($query,$curPage,$pageSize,$search);
        foreach($data['data'] as &$v){
            $v['userInfo']['avatar_file'] = $this->get_avatar_url($v['userInfo']['uid']);
        }
        return $data;
    }


    public function get_avatar_url($uid, $size = 'mid',$params = 'http://www.jiucaishuo.com/uploads')
    {
        $uid = intval($uid);
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        return $params . '/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, - 2) . '_avatar_' . $size . '.jpg';

    }

    public function parse_attachs($str,$match = false)
    {
        if($match){
            preg_match_all('/\[attach\]([0-9]+)\[\/attach]/', $str, $matches);
            return array_unique($matches[1]);
        }else{
            $res = preg_replace('/\[attach\]([0-9]+)\[\/attach]/', '--img--',$str);
            return $res;
        }
    }
}
