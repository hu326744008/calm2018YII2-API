<?php
namespace frontend\controllers;
header("Content-type: text/html; charset=utf-8");
use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
use frontend\models\Redis;
use frontend\libs\redis\RedisClass;
/**
 * Site controller
 */
class NewsController extends Controller
{

    /**
     * Displays homepage.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $set = RedisClass::redis_get('hu');
        var_dump($set);
        die;
        //Yii::$app->redis->set('user','aaa');
       // Yii::$app->redis->del('user:1');die;
        $array = array(
            'name',
            '2222',
            '3333333');
        Yii::$app->redis->hmset('user:1', 'name', 'joe', 'solary', 2000);
        Yii::$app->redis->hmset('user:1', 'ssss', 'wwww', 'saasd', 2111000);
        Yii::$app->redis->hmset('user:1', 'ssss', 'wwww', 'saasd', 6666666);
        $arr = Yii::$app->redis->hgetall('user:1');
        var_dump($arr);
        die;

        //$arr= Yii::$app->redis->get('user');
        //var_dump($arr);
        //Yii::$app->redis->del('list');die;
        Yii::$app->redis->rpush('list', 'aa2a');
        Yii::$app->redis->rpush('list', 'bb3b');
        Yii::$app->redis->rpush('list', 'cc3c');
        Yii::$app->redis->rpush('list', 'cc3c');
        Yii::$app->redis->rpush('list', '3cc3c');

        $data = Yii::$app->redis->lrange('list', 0, 1000);
        print_r($data);
        //die;
        //die;
        //$redis = new Rediss();
        //$redis->connect('192.168.100.185', 6379);
        // 获取数据并输出

        $arList = Yii::$app->redis->keys("*");
        echo "Stored keys in redis:: ";
        print_r($arList);
        
        //获取条数
        Yii::$app->redis->llen('shop_category');
        //分页查询
        Yii::$app->redis->lrange('shop_category', 0, 30);
        //写入
        Yii::$app->redis->rpush('shop_category', json_encode($v));
        //设置有效时间
        Yii::$app->redis->setex($token,'3600',$date);
        //删除
        Yii::$app->redis->del('shop_category');
    }
    //
    public function actionList(){
        
    }


}
