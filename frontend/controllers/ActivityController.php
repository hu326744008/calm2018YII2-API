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
     * 获取活动列表
     */
    public function actionGetlist()
    {
        Yii::info('获取活动列表;', 'info');
        $post = Yii::$app->request->post();
        if (empty($post['appid']) || empty($post['recommend']) || empty($post['cityid'])) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');
            die;
        }
        //验证TOKEN
        /**$token_endTime = Yii::$app->redis->get($post['token']);
        
        if(empty($token_endTime)){
            MobileApi::RetEcho(MobileErr::TOKEN_WRONG, 'token不匹配', '');die;
        }elseif($token_endTime<date('Y-m-d H:i:s')){
            MobileApi::RetEcho(MobileErr::TOKEN_TIMEOUT, 'token超时', '');die;
        }**/
        //获取项目ID
        $project = (new UniversalDb('db'))->setTableName('system_project')->find()->select('id')->where(['appid' => $post['appid']])->asArray()->one();
        //根据项目ID获取推荐位ID
        $recommend = (new UniversalDb('db'))->setTableName('system_recommend')->find()->select('id')->where(['code' => $post['recommend'],'project_id'=>$project['id']])->asArray()->one();
        //根据推荐位ID获取推荐位内容
        $list = (new UniversalDb('db'))->setTableName('system_recommend_rel')
                 ->find()
                 ->select('title,thumb,city_id,recommend_type,target_id,target_url')
                 ->where(['recommend_id' => $recommend['id'],'status'=>'1', 'city_id' => $post['cityid']])
                 ->asArray()
                 ->all();
       
        if(!empty($list)){
                //等于5的时候查商品的分类ID
            foreach($list as $k=>$v){
                $list[$k]['starttime'] ='';
                $list[$k]['endtime'] = '';
                $list[$k]['city'] = '';
                $list[$k]['cat_id'] = '';
                if($v['recommend_type'] == 5){
                    $goodsinfo = (new UniversalDb('db'))->setTableName('shop_goods')
                     ->find()
                     ->select('goods_id,cat_id,goods_name')
                     ->where(['goods_id' => $v['target_id']])
                     ->asArray()
                     ->one();
                    $list[$k]['cat_id'] = $goodsinfo['cat_id'];
                }elseif($v['recommend_type'] == 1||$v['recommend_type'] == 2){
                    $activity = (new UniversalDb('db'))->setTableName('activity_info')
                     ->find()
                     ->select('start_time,end_time,city')
                     ->where(['id' => $v['target_id']])
                     ->asArray()
                     ->one();
                     if(empty($activity)){
                        MobileApi::RetEcho(MobileErr::DATA_NON, '推荐活动有误',[]);die;
                     }
                     $list[$k]['starttime'] = $activity['start_time'];
                     $list[$k]['endtime'] = $activity['end_time'];
                     $list[$k]['city'] = $activity['city'];
                }
            }
            MobileApi::RetEcho(MobileErr::SUCCESS, '获取成功', $list);
        }else{
           
            MobileApi::RetEcho(MobileErr::DATA_NON, '推荐位不存在',[]);
        }
        die;
        $activity[]  = [
          'starttime' => '2016-12-20 16:26:19',
          'cate' =>'2',
          'pic' =>'http://futureshop.oss-cn-qingdao.aliyuncs.com/activity/banner_02.jpg',
          'url'=>'',
          'city'=>130500
          
        ];  
      
        if(!empty($activity)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '数据获取成功', $activity);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '数据获取失败');
        }
    }
    
}
