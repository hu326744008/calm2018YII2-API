<?php
namespace frontend\controllers;
header("Content-Type: text/html; charset=UTF-8");
use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
use frontend\models\USystemAppVersion;


class ActivityController extends Controller
{
    public $enableCsrfValidation = false;
    
    
    /**
     * 获取活动列表ss
     * 
     * 是是是
     */
    public function actionGetlist()
    {
        $redis = Yii::$app->redis->set();
       
        if(!empty($activity)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '数据获取成功', $activity);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '数据获取失败');
        }
    }
    
}
