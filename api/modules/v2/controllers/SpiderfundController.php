<?php

namespace api\modules\v2\controllers;

use common\models\AwsGuCategory;
use common\models\AwsGuGuanli;
use common\models\AwsGuNewGuanli;
use common\models\AwsGuNewInfo;
use common\models\AwsGuNewTuoguan;
use common\models\AwsGuTuoguan;
use common\models\AwsGuNewCategory;
use common\models\AwsGu;
use yii;
use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v2\controllers\BaseController;
use yii\data\ActiveDataProvider;
use common\models\AwsFundScore;
class SpiderfundController extends BaseController
{
    public $modelClass = '';
    public $TianTianFundUrl;
    public $danweijingzhi;
    /*
     * 爬天天基金详情数据
     * @param
     * return  单位净值  日涨跌  标签  净值走势   业绩表现
     *  require(__DIR__ . '/../../../../common/lib/phpquery.php');
        $pq = \phpQuery::newDocumentFile($this->TianTianFundUrl);
     */
    public function actionFunddetail(){
        $fundCode = Yii::$app->request->get('fundCode')?:161725;
        $this->TianTianFundUrl = 'https://fundmobapi.eastmoney.com/FundMApi/FundBaseTypeInformation.ashx?FCODE='.$fundCode.'&deviceid=Wap&plat=Wap&product=EFund&version=2.0.0&Uid=&_=1501648066525';
        $fundDataInfo = file_get_contents($this->TianTianFundUrl);
        echo $fundDataInfo;
    }

    /*
     *基金参数走势图
     *@param int 月份
     *@return json  走势数据
     */
    public function actionFundjingzhi(){
        $month = Yii::$app->request->get()?:1;
        $fundCode = Yii::$app->request->get()?:161725;
        $this->danweijingzhi = 'https://fundmobapi.eastmoney.com/FundMApi/FundNetDiagram.ashx?FCODE='.$fundCode.'&RANGE='.$month.'y&deviceid=Wap&plat=Wap&product=EFund&version=2.0.0&Uid=&_=1501648066539';
        $jingzhiInfo = file_get_contents($this->danweijingzhi);
        return $jingzhiInfo;
    }

    /*
     *阶段涨幅
     * https://fundmobapi.eastmoney.com/FundMApi/FundPeriodIncrease.ashx?FCODE=161725&deviceid=Wap&plat=Wap&product=EFund&version=2.0.0&Uid=&RANGE=&_=1501658906342
     * 季度涨幅
     * https://fundmobapi.eastmoney.com/FundMApi/FundPeriodIncrease.ashx?callback=jQuery31106401913124215992_1501658906337&FCODE=161725&deviceid=Wap&plat=Wap&product=EFund&version=2.0.0&Uid=&RANGE=3y&_=1501658906343
     */
    public function actionHistoryjingzhi(){
        $month = Yii::$app->request->get()?:1;
        $fundCode = Yii::$app->request->get()?:161725;
        $this->danweijingzhi = 'https://fundmobapi.eastmoney.com/FundMApi/FundPeriodIncrease.ashx?FCODE='.$fundCode.'&deviceid=Wap&plat=Wap&product=EFund&version=2.0.0&Uid=&RANGE=&_=1501658906342';
        $jingzhiInfo = file_get_contents($this->danweijingzhi);
        echo $jingzhiInfo;
    }

    /*
     * 基金评论 评分  问基金  分答系统
     * @param int score
     * return bool
     * true 评论成功
     * false 评论失败
     */
    public function actionTalkfund(){
        $store = Yii::$app->request->get('store')?:'';
        $fund_id = Yii::$app->request->get()?:'';
        if(empty($store) || empty($fund_id)){
            $info = [
                'code'=>101,
                'message'=>'异常错误'
            ];
            return $info;
        }
        $score = new AwsFundScore;
        $score->score = $store;
        $score->create_time = date('Y-m-d H:i:s',time());
        $score->fund_id = $fund_id;
        if($score->save()){
            $info = [
                'code'=>200,
                'message'=>'评论成功,请搜索公众号“韭菜说投资社区”查看他人@你评论哟'
            ];
        }else{
            $info = [
                'code'=>101,
                'message'=>'评论失败，请前去社区进行询问'
            ];
        }
        return $info;
    }

}
