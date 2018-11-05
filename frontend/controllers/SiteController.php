<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;
    
    public function actionApitest(){
        $time = date('Y-m-d H:i:s');
        $encrypted = base64_encode('appid=isanjieeIhvDh&secret=oVBykAFg9kyL3peTy78e6LVEZsn2Dpz7&date='.$time);
        //$url = "http://139.224.67.75:7070/goods/getcategory";
        //$array = ['level'=>'5','token'=>'M9x3gw9t8Z3TU39cWb7sOwXUdOYxpBBd'];
        $url = "http://api.cc/web/site/gettoken";
        $array = ['encrypted'=>$encrypted];
        $ch = curl_init ();
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $array );
        $output = curl_exec ( $ch );
        curl_close ( $ch );
        return $output;
    }
    
    /** 
     * 获取TOKEN
     * appid , secret  date 用&符号拼接后进行base64加密
     * 
     */
    public function actionGettoken()
    {   
        $post = Yii::$app->request->post();
        if(empty($post['encrypted'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, 'POST参数缺失', '');die;
        }
        $params = base64_decode($post['encrypted']);
        parse_str($params);
        if(empty($appid)||empty($secret)||empty($date)){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失', '');die;
        }
        $project = (new UniversalDb('db'))->setTableName('system_project')->find()->where(['appid' => $appid])->one();
        if(empty($project)){
            MobileApi::RetEcho(MobileErr::DATA_NON, '项目不存在！', '');die;
        }
        if($project['secret']!=$secret){
            MobileApi::RetEcho(MobileErr::SECRET_WRONG, 'secret验证失败！', '');die;
        }else{
            $token = self::getRandChar('32');
            $date = date('Y-m-d H:i:s',strtotime($date . "+1 hours"));
            Yii::$app->redis->setex($token,'3600',$date);
            $data = ['token'=>$token,'date'=>$date];
            MobileApi::RetEcho(MobileErr::SUCCESS, 'Token获取成功！', $data);
        }
    }
    //生成随机字符串
    public function getRandChar($length){
       $str = null;
       $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
       $max = strlen($strPol)-1;
    
       for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
       }
    
       return $str;
   }
 
   //快递信息查询
   public function actionExpressfind(){
        Yii::info('快递信息查询;', 'info');
        $post = Yii::$app->request->post();
        $post_data = array();
        $post_data["customer"] = 'B5F55AA967A7D740DCEB78B2351D60B8';
        $key= 'xyEPvWlj2727' ;
      
        if(empty($post['com'])||empty($post['num'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $post_data["param"] = '{"com":"'.$post['com'].'","num":"'.$post['num'].'"}';
          
        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o=""; 
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = str_replace("\&quot;",'"',$result );
        $data = json_decode($data,true); 
        if(!empty($data)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '获取成功', $data);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '获取失败');
        }
        
   }

   //测试
   public function actionTest(){
    
        Yii::$app->redis->del('shop_category_level_0');
        Yii::$app->redis->del('shop_category_level_1');
        Yii::$app->redis->del('shop_category_level_19');
   }
}
