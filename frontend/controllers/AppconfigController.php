<?php
namespace frontend\controllers;
header("Content-Type: text/html; charset=UTF-8");
use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
use frontend\models\USystemAppVersion;
use frontend\models\USystemServiceState;


class AppconfigController extends Controller
{
    public $enableCsrfValidation = false;
    
    
    /**
     * 获取APP版本号
     * page_num  页数
     * requests_num 请求条数
     * user_id 用户id
     * order_status 订单状态
     */
    public function actionVersion()
    {
       Yii::info('APP版本号查询：', 'info');
       $post = Yii::$app->request->post();
        if(empty($post['category'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $where['category'] = $post['category'];
        $redis_key = "appVersion".$post['category'];
        $info = Yii::$app->redis->get($redis_key);
        if(empty($info)){
        //查询APP版本号
        $info = USystemAppVersion::_get_info($where);
        $info = $info->attributes;
        Yii::$app->redis->set($redis_key,json_encode($info));
        }else{
             Yii::info('从redis里查询数据', 'info'); 
             $info = json_decode($info);
        }
        if(!empty($info)){

            MobileApi::RetEcho(MobileErr::SUCCESS, '数据获取成功', $info);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '数据获取失败');
        }
    }
    public function actionAppstate(){
        Yii::info('APP状态查询：', 'info');
        //Yii::$app->redis->del('appstate');die;
        //$post = Yii::$app->request->post();
        $redis_key = "appstate";
        $info = Yii::$app->redis->get($redis_key);
        if(empty($info)){
          
            //查询APP当前状态
            $where = '1=1';
            $info = USystemServiceState::_get_info($where);
            $info = $info->attributes;
            Yii::$app->redis->set($redis_key,json_encode($info));
        }else{
             Yii::info('从redis里查询数据', 'info'); 
             $info = json_decode($info);
        }
        if(!empty($info)){
          
            MobileApi::RetEcho(MobileErr::SUCCESS, '数据获取成功', $info);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '数据获取失败');
        }
    }
    
}
