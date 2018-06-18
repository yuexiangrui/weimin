<?php

namespace api\modules\v2\controllers;

use common\models\AwsGuFundCompanyInfo;
use common\models\AwsPersonAttribute;
use common\models\AwsQuestion;
use common\models\AwsQuestionRightLogs;
use common\models\AwsUserScoreLogs;
use yii;
use yii\rest\ActiveController;
use yii\web\Response;
use api\modules\v2\controllers\BaseController;
use yii\data\ActiveDataProvider;
use common\models\AwsFundScore;
use common\models\QuestionList;
use common\lib\weixin;

class QuestionController extends BaseController
{
    public $modelClass = 'common\models\QuestionList';
    /**
     * 问题列表
     */
    public function actionNewindex()
    {
        $model = new QuestionList();
        $question = $model->find()->asarray()->all();
        $result = [];
        foreach($question as $k=>$v){
            if($v['question_category_one'] == '基础'){
                $result[$v['question_category_one']][] = $v;
            }else if($v['question_category_one'] == '进阶'){
                $result[$v['question_category_one']][] = $v;
            }else{
                $result[$v['question_category_one']][] = $v;
            }
        }
        $one = array_rand($result['基础'],6);

        $one_data = [];
        foreach($one as $v){
            $one_data[] = $result['基础'][$v];
        }
        $two = array_rand($result['进阶'],3);
        $two_data = [];
        foreach($two as $v){
            $two_data[] = $result['进阶'][$v];
        }
        $three = array_rand($result['附加'],1);
        $three_data = [];
        $three_data[] = $result['附加'][$three];
//        $data = $three_data;//array_merge($one_data,$two_data,$three_data);
        $data = array_merge($one_data,$two_data,$three_data);
        $new = [];
        foreach($data as $k=>$v){
            $new[$k]['q'] = $v['question_name'];
            $new[$k]['a'][] = $v['answer_a'];
            $new[$k]['a'][] = $v['answer_b'];
            $new[$k]['a'][] = $v['answer_c'];
            $new[$k]['a'][] = $v['answer_d'];
            $new[$k]['r'] = $v['question_right'];
            $new[$k]['score'] = $v['question_score'];
            $new[$k]['question_id'] = $v['id'];
        }
        $result = [
            'status'=>true,
            'data'=>$new
        ];
        return $result;
    }

    /**
     * 下发js 配置签名
     */
    public function actionGetsign(){
        $param = Yii::$app->request->get();
        $noncestr = mt_rand(1000000000, 9999999999);
        $weixin = new weixin();
        $acctoken = $weixin->get_access_token();
        $get_jsapi_ticket = $weixin->get_jsapi_ticket($acctoken);
        if(isset($param['url']) && !empty($param['url'])){
            $url = urldecode($param['url']);
        }else{
            $url = 'http://person.jiucaishuo.com/';
        }
        //'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $times = time();
        $weixin_signature = $weixin->generate_jsapi_ticket_signature($get_jsapi_ticket,$noncestr,$times,$url);
        $icon_url = 'https://api.jiucaishuo.com/icon_url.jpg';
        $data = [
            'appId'=>'wxde771d88b98c8b41',
            'noncestr'=>$noncestr,
            'signature'=>$weixin_signature,
            'timestamp'=>$times,
            'url'=>$url,
            'jsapi_ticket'=>$get_jsapi_ticket,
            'title'=>'测一测，你的职业会被人工智能代替吗？',
            'desc'=>(string)'14个行业，163个职位，AI时代胜算几何',
            'icon_url'=>$icon_url,
        ];
        return $data;
    }

    /**
     * 获取最终结果图
     */
    public function actionGetfinallyimage(){
        $config = [
            '0'=>'生活从哪里开始不重要，重要的是开始生活。学习基金投资也是一样。',
            '1'=>'自负盈亏的人生，需要认真对待投资这件事，不如先把基金玩透吧。',
            '2'=>'投资是你是我，疲惫生活中的英雄梦想，就用基金实现它吧。',
            '3'=>'投资路上没有白走的路，每一步走算数，每一支基都hold住。',
            '4'=>'作为一代基神，何不把自己的知识更多的分享给他人，毕竟超有才华，说话又好听！',
        ];
        $absoute_url = dirname(dirname(dirname(dirname(__FILE__)))).'/web';
        if(strstr($absoute_url,'home')){
            $absoute_url = '/home/wwwroot/weimin/api/web/';
        }else{
            $absoute_url = './';
        }

        $param = Yii::$app->request->get();
        $score = $param['score'] = isset($param['score']) && !empty($param['score']) && $param > 0 ?$param['score']:'22';
        $param['answer'] = isset($param['answer']) && !empty($param['answer'])?$param['answer']:'{"1":"A","2":"B"}';
        $param['open_id'] = isset($param['open_id']) && !empty($param['open_id'])?$param['open_id']:'oZVNEs1ALKohRTML0VetovFf00V0';
        $param['idenity'] = isset($param['idenity']) && !empty($param['idenity'])?$param['idenity']:'未知';
        $scoreLogs = new AwsUserScoreLogs;
        $score_lists = $scoreLogs->find()->asArray()->all();
        $new_score = [];
        foreach($score_lists as $v){
            $new_score[]= $v['score'];
        }
        $new_score[] = $score;
        sort($new_score);
        $key = array_search($score,$new_score);
        $number = $key +1;//排名
        $person_numbers = count($new_score) + 1;
        $sheng = $key +1;
        $aws_conf = ['open_id'=>$param['open_id']];
        if($info = AwsUserScoreLogs::find()->where($aws_conf)->one()){
            $info->score = $score;
            $info->open_id = $param['open_id'];
            $info->idenity = $param['idenity'];
            $info->create_time = date('Y-m-d H:i:s',time());
            $info->save();
        }else{
            $scoreLogs->score = $score;
            $scoreLogs->open_id = $param['open_id'];
            $scoreLogs->idenity = $param['idenity'];
            $scoreLogs->create_time = date('Y-m-d H:i:s',time());
            $scoreLogs->save();
        }
        $data = json_decode($param['answer'],true);
        $info_jiucai = '';
        if($param['score'] > 0 && $param['score'] <= 30){
            $info_jiucai = $config[0];
        }else if($param['score'] <= 50 && $param['score'] > 30){
            $info_jiucai = $config[1];
        }else if($param['score'] <= 70 && $param['score'] > 50){
            $info_jiucai = $config[2];
        }else if($param['score'] <= 90 && $param['score'] > 70){
            $info_jiucai = $config[3];
        }else if($param['score'] <= 100 && $param['score'] > 90){
            $info_jiucai = $config[4];
        }
        if(mb_strlen($info_jiucai,'utf-8') > 18 ){
            $info_one = mb_substr($info_jiucai,0,18,'utf-8');
            $info_two = mb_substr($info_jiucai,18,mb_strlen($info_jiucai,'utf-8'),'utf-8');;
        }else{
            $info_one = mb_substr($info_jiucai,0,18,'utf-8');
            $info_two = '';
        }
        AwsQuestionRightLogs::deleteAll(['open_id'=>$param['open_id']]);
        $weixin = new weixin();
        $info = $weixin->get_user_info_by_openid_from_weixin($param['open_id']);
        $poster_path = 'score.jpg';
        $new_path =  'demo.'.$param['open_id'].'.jpg';
        //创建图片的实例，接收参数为图片
        $perent_size = 110;
        $nick_name_size = 23;
        $jiucai_name_size = 32;
        $dst_qr = @imagecreatefromstring(file_get_contents($poster_path));
        $width = imagesx ( $dst_qr );
        $height = imagesy ( $dst_qr );
        $red = imagecolorallocate($dst_qr, 245, 52, 71);//字体颜色]
        $nick_name = imagecolorallocate($dst_qr, 0, 0, 0);//字体颜色]
        $idenity_name = imagecolorallocate($dst_qr, 0, 0, 0);//字体颜色]
        $jiu_name = imagecolorallocate($dst_qr, 245, 52, 71);//字体颜色]
        $fontfile_perent = $absoute_url.'li.ttf';
        $fontfile_name = $absoute_url.'SourceHanSansCN-Normal.otf';
        $scoreBox = imagettfbbox($perent_size, 0, $fontfile_perent, $score.'%');//文字水平居中实质
        $nickNameBox = imagettfbbox($nick_name_size, 0, $fontfile_name, $info['nickname']);//文字水平居中实质
        $idenityNameBox = imagettfbbox($nick_name_size, 0, $fontfile_name, $info['nickname']);//文字水平居中实质
        $jiucaiOneNameBox = imagettfbbox($jiucai_name_size, 0, $fontfile_name, $info_one);//文字水平居中实质
        if($info_two){
            $fontBox_two = imagettfbbox($jiucai_name_size, 0, $fontfile_name, $info_two);//文字水平居中实质
            imagefttext($dst_qr, $jiucai_name_size, 0, ($width - $fontBox_two[2]) / 2, 1050, $jiu_name, $fontfile_name,$info_two);
        }
        imagefttext($dst_qr, $perent_size, 0, ($width - $scoreBox[2]) / 2 - 40, 740, $red, $fontfile_perent,round($number/$person_numbers,1).'%');
        imagefttext($dst_qr, $nick_name_size, 0, ($width - $nickNameBox[2]) / 2 +5, 440, $nick_name, $fontfile_name,$info['nickname']);
        imagefttext($dst_qr, $nick_name_size, 0, ($width - $idenityNameBox[2]) / 2 +5, 503, $nick_name, $fontfile_name,$param['idenity']);
        imagefttext($dst_qr, $jiucai_name_size, 0, ($width - $jiucaiOneNameBox[2]) / 2 , 983, $jiu_name, $fontfile_name,$info_one);
        $next_font_size = 32;
        $sheng_x = 315;
        $number_x = 465;
        $person_number_x = 664;
//        if($sheng > 100 ){
//            $sheng_x = 300;
//        }
//        if($number >=100 ){
//            $number_x = 400;
//        }
//        if($number >=1000 ){
//            $number_x = 380;
//        }
//        if($sheng >= 1000){
//            $sheng_x = 360;
//        }
//        if($person_numbers >=10000){
//            $person_number_x = 630;
//        }
//        if($sheng >=10000){
//            $sheng_x = 340;
//        }
        $person_numbers = sprintf('%05s', $person_numbers);
        $sheng = sprintf('%05s', $sheng-1);
        imagefttext($dst_qr, $next_font_size, 0, $number_x , 1233, $jiu_name, $fontfile_name,$person_numbers);//共有多少人参加
        imagefttext($dst_qr, $next_font_size, 0, $sheng_x , 1293, $jiu_name, $fontfile_name,$score);//分数
        imagefttext($dst_qr, $next_font_size, 0, $person_number_x, 1293, $jiu_name, $fontfile_name,$sheng);//分数总排名
        $img_header_size = 100;
        $thumb = imagecreatetruecolor($img_header_size, $img_header_size);//创建一个300x300图片，返回生成的资源句柄
        $qr_code_url = $info['headimgurl'];//'http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $qr_code_url);
        ob_start();
        curl_exec($ch);
        $qr_content = ob_get_contents();
        ob_end_clean();
        $source =  @imagecreatefromstring($qr_content);
        $img_path = './head_img'.$param['open_id'].'.jpg';
        imagejpeg($source, $img_path, 100);
        imagedestroy($source);
        $head_imag_score = $this->yuan_img($img_path);
        imagejpeg($head_imag_score, $img_path, 100);
        $source =  @imagecreatefromstring(file_get_contents($img_path));
        //将源文件剪切全部域并缩小放到目标图片上，前两个为资源句柄
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $img_header_size, $img_header_size, 132, 128);
        //加水印
        imagecopy($dst_qr, $thumb, 240, 405, 0, 0, $img_header_size, $img_header_size);
        imagejpeg($dst_qr, $new_path, 50);
        imagedestroy($dst_qr);
        foreach($data as $kk=>$vv){
            $model = new AwsQuestionRightLogs();
            $model->question_id = $kk+1;
            $model->open_id = $param['open_id'];
            $model->nickname  = $info['nickname'];
            $model->user_choose  = $vv;
            $model->create_time  =  date('Y-m-d H:i:s',time());
            $model->save();
        }
        $result['img_url'] = 'https://api.jiucaishuo.com/'.$new_path.'?time='.time();
        return $result;
    }
    public function yuan_img($imgpath = './tx.jpg') {
        $ext     = pathinfo($imgpath);
        $src_img = null;
        switch ($ext['extension']) {
            case 'jpg':
                $src_img = imagecreatefromjpeg($imgpath);
                break;
            case 'png':
                $src_img = imagecreatefrompng($imgpath);
                break;
        }
        $wh  = getimagesize($imgpath);
        $w   = $wh[0];
        $h   = $wh[1];
        $w   = min($w, $h);
        $h   = $w;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 246, 246, 246, 127);
        imagefill($img, 0, 0, $bg);
        $r   = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        return $img;
    }
    /**
     * 下发所有公司信息
     */
    public function actionGetallcompany(){
        $model = new AwsGuFundCompanyInfo();
        $fund = $model->find()->asarray()->all();
        $data = [];
//        foreach($fund as $k=>$v){
//            $data[$k]['name'] = $v['gu_company'];
//        }
        $data[]['name'] = '';
        $data[]['name'] = '';
        $data[]['name'] = '';
        $data[]['name'] = '';
        return $data;
    }

    /**
     * 下发大分类  小分类
     */
    public function actionGetcategory(){
        $model = new AwsPersonAttribute();
        $fund = $model->find()->asarray()->all();
        $data = [];
        foreach($fund as $k=>$v){
            $data[$k]['name'] = $v['one_category'];
        }
        return $data;
    }

    /**
     * 根据大分类  刷选小分类
     */
    public function actionGettwocategory(){
        $param = Yii::$app->request->get();
        $model = new AwsPersonAttribute();
        $fund = $model->find()->asarray()->all();
        $data = [];
        foreach($fund as $k=>$v){
            $data[$v['one_category']][] = $v['two_category'];
        }
        return $data;
    }

    /**
     *
     */
    public function actionGeturl(){
        $weixin = new weixin();
        $url = $weixin->get_wei_xin_code();
        $data= ['url'=>$url];
        return $data;
    }
    /**
     * 通过code 获取openid
     */
    public function actionGetopenidbycode(){
        $param = Yii::$app->request->get();
        $weixin = new weixin();
        $weixin->get_wei_xin_code();
//        $param['code'] = '001R3oPA1CyfPf0UWiPA1k3FPA1R3oPN';
        if(empty($param['code'])){
            $data = [
                'code'=>-1,
                'message'=>'code不能为空'
            ];
            return $data;
        }
        $result = $weixin->getOpenid($param['code']);
        return $result;
    }

    /*
     *人工智能生成图片
     */
    public function actionGetpersonimg(){
        $absoute_url = dirname(dirname(dirname(dirname(__FILE__)))).'/web';
        if(strstr($absoute_url,'home')){
            $absoute_url = '/home/wwwroot/weimin/api/web/';
        }else{
            $absoute_url = './';
        }
        $param = Yii::$app->request->get();
        $answer = isset($param['answer']) && !empty($param['answer'])?$param['answer']:'';
        $one_category = isset($param['category_detail']) && !empty($param['category_detail'])?$param['category']:'学术研究';
        $two_category = isset($param['category_detail']) && !empty($param['category_detail'])?$param['category_detail']:'历史学家';
        $conf = ['one_category'=>$one_category,'two_category'=>$two_category];
        $model = new AwsPersonAttribute();
        $info = $model->find()->asarray()->where($conf)->one();
        $perent =  isset($info['perent']) && !empty($info['perent'])? round($info['perent']*100) :'98';
        $info =  isset($info['info']) && !empty($info['info'])?$info['info']:'如果找不到一种在睡觉时也能帮你赚钱的方法那就准备好工作至死吧。';
        if(mb_strlen($info,'utf-8') > 18 ){
            $info_one = mb_substr($info,0,18,'utf-8');
            $info_two = mb_substr($info,18,mb_strlen($info,'utf-8'),'utf-8');;
        }else{
            $info_one = mb_substr($info,0,18,'utf-8');
            $info_two = '';
        }
        $poster_path = 'finally.jpg';
        $time = time();
        $new_path = 'person'.$time.rand(000,999).'.png';
        putenv('GDFONTPATH=' . realpath('.'));
        //创建图片的实例，接收参数为图片
        $dst_qr = @imagecreatefromstring(file_get_contents($poster_path));
        $width = imagesx ( $dst_qr );
        $height = imagesy ( $dst_qr );
        $black = imagecolorallocate($dst_qr, 0, 0, 0);//字体颜色]
        $black_one = imagecolorallocate($dst_qr, 0, 0, 0);//字体颜色]
        $black_two = imagecolorallocate($dst_qr, 241, 181, 63);//字体颜色]
        $fontfile_perent = $absoute_url.'ARIBLK.TTF';//f5b537
        $fontfile_info = $absoute_url.'SourceHanSansCN-Normal.otf';
        $fontfile_category = $absoute_url.'SourceHanSansCN-Light.otf';
        $fontBox = imagettfbbox(24, 0, $fontfile_info, $info_one);//文字水平居中实质
        $category_size = 23;
        $perent_size = 185;
        $info = 27;
        if($info_two){
            $fontBox_two = imagettfbbox($info, 0, $fontfile_info, $info_two);//文字水平居中实质
            imagefttext($dst_qr, $info, 0, ($width - $fontBox_two[2]) / 2, 1030, $black_one, $fontfile_info,$info_two);
        }
        $perentBox = imagettfbbox($perent_size, 0, $fontfile_perent, $perent.'%');//文字水平居中实质

        $categoryBox = imagettfbbox($category_size, 0, $fontfile_category, $one_category);//文字水平居中实质

        $categoryTwoBox = imagettfbbox($category_size, 0, $fontfile_category, $two_category);//文字水平居中实质
        $hengTwoBox = imagettfbbox($category_size, 0, $fontfile_category, '/');//文字水平居中实质
        $x = 75;
        imagefttext($dst_qr, $perent_size, 0, ($width - $perentBox[2]) / 2, 780, $black_two, $fontfile_perent,$perent.'%');
        imagefttext($dst_qr, $info, 0, ($width - $fontBox[2]) / 2 - 30, 950, $black_one, $fontfile_info,$info_one);
        imagefttext($dst_qr, $category_size, 0, ($width - $categoryBox[2]) / 2 - $x , 1150, $black_one, $fontfile_category,$one_category);
        imagefttext($dst_qr, $category_size, 0, ($width - $categoryTwoBox[2]) / 2 + $x, 1150, $black_one, $fontfile_category,$two_category);
        imagefttext($dst_qr, $category_size, 0, ($width - $hengTwoBox[2]) / 2, 1150, $black_one, $fontfile_category,'/');
        imagejpeg($dst_qr, $new_path, 30);
        imagedestroy($dst_qr);
        $url = 'https://api.jiucaishuo.com/'.$new_path;
        $img_ratio = number_format($width/$height,1);
//        unlink($new_path);
        //生成图片文字
        $data=['img_url'=>$url,'fund_url'=>'https://mp.weixin.qq.com/s/PRA_JDR9L0qrWlu6_7vXaw','img_ratio'=>$img_ratio];
        return $data;

    }

    /**
     * 下发分享配置
     */
    public function actionGetshareconfig(){
        $param = Yii::$app->request->get();
        $noncestr = mt_rand(1000000000, 9999999999);
        $weixin = new weixin();
        $acctoken = $weixin->get_access_token();
        $get_jsapi_ticket = $weixin->get_jsapi_ticket($acctoken);
        if(isset($param['url']) && !empty($param['url'])){
            $url = urldecode($param['url']);
        }else{
            $url = 'http://active.jiucaishuo.com/';
        }
        //'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $times = time();
        $weixin_signature = $weixin->generate_jsapi_ticket_signature($get_jsapi_ticket,$noncestr,$times,$url);
        $icon_url = 'https://file.jiucaishuo.com/fund/share.jpg';
        $data = [
            'appId'=>'wxde771d88b98c8b41',
            'noncestr'=>$noncestr,
            'signature'=>$weixin_signature,
            'timestamp'=>$times,
            'url'=>$url,
            'jsapi_ticket'=>$get_jsapi_ticket,
            'title'=>'基金从业20年野生知识小测验',
            'desc'=>(string)'别跟我说你懂基金，野生知识可否一战',
            'icon_url'=>$icon_url,
        ];
        return $data;
    }


    /**
     * 下发错题本
     */
    public function actionCuotilist(){
        $param = Yii::$app->request->get();
        $model = new AwsQuestionRightLogs();
        $open_id = isset($param['open_id']) && !empty($param['open_id'])?$param['open_id']:'1111111111';
        $conf = ['open_id'=>$open_id];
        $question_list_logs = $model->find()->where($conf)->asarray()->all();
        $question_list = new QuestionList();
        $data = [];
        foreach($question_list_logs as $k=>$v){
            $conf = ['id'=>$v['question_id']];
            $question_list_logs[$k]['question_info'] = $question_list->find()->where($conf)->asArray()->one();
        }

        foreach($question_list_logs as $k=>$vv){
            $data[$k]['q'] = $vv['question_info']['question_name'];
            $data[$k]['a'][] = $vv['question_info']['answer_a'];
            $data[$k]['a'][] = $vv['question_info']['answer_b'];
            $data[$k]['a'][] = $vv['question_info']['answer_c'];
            $data[$k]['a'][] = $vv['question_info']['answer_d'];
            $data[$k]['r'] = $vv['question_info']['question_right'];
            $data[$k]['score'] = $vv['question_info']['question_score'];
            $data[$k]['choose'] = $vv['user_choose'];
            $data[$k]['explain'] = '问题解析：'.$vv['question_info']['right_info'];
        }
        $logs = new AwsUserScoreLogs();
        $info_row = $logs->find()->asArray()->where(['open_id'=>$open_id])->one();
        $result['identity'] = $info_row['idenity'];
        $result['data'] = $data;
        return $result;
    }
}
