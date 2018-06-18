<?php

namespace api\modules\v2\controllers;

use common\models\AwsGuCurlData;
use yii;
use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v2\controllers\BaseController;
use yii\data\ActiveDataProvider;
use common\models\AwsFundScore;


class GuzhiController extends BaseController

{
    /**
     * 估值所有颜色对比
     */
    public function actionNewindex(){
        $param = Yii::$app->request->post();
        $model = AwsGuCurlData::find();
        $new = [];
        foreach($model as $k=>$v){
            $info = $this->getGuPosition($v['gu_pe_current_perent']);
            $new[][$k]['date'] = time().'000';
            $new[$k]['group'] = $info['color'];
            $new[$k]['indexName'] = $v['gu_name'];
            $new[$k]['indexCode'] = $v['gu_code'];
            $new[$k]['pb'] = $v['gu_pb'];
            $new[$k]['pbHigh'] ='';
            $new[$k]['pbLow'] = '';
            $new[$k]['pbPercentile'] = $v['gu_pb_current_perent'];
            $new[$k]['pe'] = $v['gu_pe'];
            $new[$k]['peHigh'] = '';
            $new[$k]['peLow'] = '';
            $new[$k]['pePercentile'] = $v['gu_pe_current_perent'];
            $new[$k]['roe'] = $v['gu_xilv'];
            $new[$k]['scoreBy'] = '';
            $new[$k]['source'] = 0;
        }
        $info = [];
        $order_low = [];
        $order_middele= [];
        $order_hight = [];

        foreach($new as $k=>$v){
            if($v['group'] == 'Low'){
                $info['Low'][] = $v;
                $order_low[] =$v['gu_pe_current_perent'];
            }else if($v['group'] == 'High'){
                $info['High'][] = $v;
                $order_middele[] =$v['gu_pb_current_perent'];
            }else if($v['group'] == 'Middle'){
                $info['Middle'][] = $v;
                $order_hight[] =$v[''];
            }
        }

        array_multisort($order_low,SORT_ASC,$info['Low']);

        array_multisort($order_middele,SORT_ASC,$info['Middle']);

        array_multisort($order_hight,SORT_ASC,$info['High']);

        $one = array_slice($info['Low'],10);
        $two = array_slice($info['Low'],10);
        $three = array_slice($info['Low'],10);
        $result = array_merge($one,$two,$three);
        return $result;
    }

    public function getGuPosition($perent){
        $info = [];
        if($perent < 0.2){
            $info['color'] = 'Low';//低于10
            $info['gu_value'] = $perent;
            return $info;
        }
        else if($perent > 0.8 && $perent < 1){
            $info['color'] = 'High';//高
            $info['gu_value'] = $perent;
            return $info;
        }
        else{
            $info['color'] = 'Middle';//适中
            $info['gu_value'] = $perent;
            return $info;
        }
    }
}
