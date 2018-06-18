<?php

namespace api\modules\v1\controllers;

use common\models\AwsGuCategory;
use common\models\AwsGuGuanli;
use common\models\AwsGuNewInfo;
use common\models\AwsGuTuoguan;
use common\models\AwsGu;
use yii;
use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v1\controllers\BaseController;
use yii\data\ActiveDataProvider;

class FundsController extends BaseController
{
    public $modelClass = 'common\models\AwsGuNewInfo';

    public function actionNewindex(){
        $model = new AwsGu();
        $conf = [];
        if($guali = Yii::$app->request->get('guanli')){
            $conf['gu_tou_category'] = $guali;
        }

        if($tuoguan = Yii::$app->request->get('tuoguan')){
            $conf['gu_tou_two_category'] = $tuoguan;
        }

        if($gu_is_fen = Yii::$app->request->get('gu_is_fen')){
            $conf['gu_is_fen'] = $gu_is_fen;
        }
        if($gu_three_category = Yii::$app->request->get('gu_three_category')){
            $conf['gu_three_category'] = $gu_three_category;
        }

        if($gu_category =  Yii::$app->request->get('gu_category')){
            $conf['gu_category'] = $gu_category;
        }

        if($gu_name =  Yii::$app->request->get('gu_name')){
            $conf = ['like', 'gu_name', $gu_name];
        }

        $curPage = Yii:: $app->request->get( 'page',1);
        $pageSize = 10;
        $type = Yii:: $app-> request->get( 'type', '');
        $value = Yii:: $app-> request->get( 'value', '');
        $search = ($type&&$value)?[ 'like',$type,$value]: '';
        $query = $model->find()->where($conf);
        if($gui_mo = Yii::$app->request->get('gui_mo')){
            $gui_mo = str_replace('亿','',$gui_mo);
            $arr = explode('-',$gui_mo);
            $confs = ['between', 'gu_guimo', $arr[0], $arr[1]];
            $query = $query->andWhere($confs);
        }
        $query = $query->orderBy( 'id DESC');
        $data = $model->getPages($query,$curPage,$pageSize,$search);
        return $data;
    }

    /*
     * 基金管理人
     */
    public function actionFundsguanli(){
        $new = '';
        $info = '';
        $fundguanli = new AwsGuGuanli();
        $guanli = $fundguanli::find()->asArray()->orderBy('gu_tuo_suo asc')->all();
        foreach($guanli as $v){
            $firstString = substr($v['gu_tuo_suo'],0,1);
            $new[$firstString][] = $v['aws_guanli_name'];
        }
        $i = 0;
        if($new){
            foreach($new as $key=>$vv){
                $info[$i]['name'] = $key;
                $info[$i]['data'] = $vv;
                $i++;
            }
        }
        return $info;
    }

    /*
     * 基金托管人
     */
    public function actionFundstuoguan(){
        $new = '';
        $info = '';
        $fundtuoguanli = new AwsGuTuoguan();
        $guanli = $fundtuoguanli::find()->asArray()->orderBy('gu_tuo_suo asc')->all();
        foreach($guanli as $v){
            $firstString = substr($v['gu_tuo_suo'],0,1);
            $new[$firstString][] = $v['gu_tuo_name'];
        }
        $i = 0;
        if($new){
            foreach($new as $key=>$vv){
                $info[$i]['name'] = $key;
                $info[$i]['data'] = $vv;
                $i++;
            }
        }
        return $info;
    }

    /*
     * 多级分类
     */
    public function actionAllcategory(){
        $new = '';
        $category_id = Yii::$app->request->get('category_id')?:'';
        $categoryInfo = AwsGuCategory::find();
        $allInfo = $categoryInfo->asArray()->all();
        if($category_id){
            $conf = ['parent_id'=>$category_id];
            $new = $categoryInfo->where($conf)->all();
        }
        $info = $this->subtree($allInfo,0,1);
        foreach($info as $v){
            if($v['parent_id'] == 0){
                  $new[$v['id']] = $v;
            }
        }
        foreach($info as $vv){
            foreach($new as $k=>$vvv){
                if($vv['parent_id'] == $vvv['id']){
                    $new[$k]['children'][] = $vv;
                }
            }
        } 
        $children = '';
        foreach($info as $y=>$vv){
            if($vv['lev'] == 1){
                $children = $vv['id'];
            }
            if($children){
                foreach($new[$children]['children'] as $kk=>$vvvv){
                    if($vv['parent_id'] == $vvvv['id']){
                        $new[$children]['children'][$kk]['children'][] = $vv;
                    }
                }
            }
        }

        foreach($new as $nv){
            $a[] = $nv;
        }
        return $a;
    }

    public function subtree($arr,$id,$lev=1){
    //根据id找子孙
        $subs=array();
        foreach($arr as $v){
            if($v['parent_id']==$id)
            {
                $v['lev']=$lev;
                $subs[]=$v;
                $subs=array_merge($subs,$this->subtree($arr,$v['id'],$lev+1));
            }
        }
       // print_r($subs);exit;
        return $subs;
    }

    /*
     * 基金详情信息
     */
    public function actionFundDetail(){
        $fundid = Yii::$app->request->get();
    }
}
