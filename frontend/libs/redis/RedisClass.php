<?php
namespace frontend\libs\redis;
use Yii;
header("Content-type: text/html; charset=utf-8");
/***
接口基类
***/
class RedisClass
{
    
    
    /**
     * Redis 键值生成
     * keyword 键
     * value 值
     * */ 
    static function redis_key($keyword,$value = ''){
       $value =  hash('crc32', $value);
       $redis_key = $keyword.'_'.$value;
       return $redis_key;
    }
    
    /**
     * 添加redis缓存：：set方法
     * key 键
     * value 值
     * */
   static function redis_set($key,$value){
	   if(empty($key)||empty($value)){
          return false;
	   }
       $set = Yii::$app->redis->set($key,$value);
       return $set;
	}
    /**
     * 查询redis
     * key 键
     * */
   static function redis_get($key){
	   if(empty($key)){
          return false;
	   }
       $set = Yii::$app->redis->get($key);
       return $set;
	}
}