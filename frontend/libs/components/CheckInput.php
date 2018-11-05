<?php
namespace frontend\libs\components;
use Yii;
use Api\MobileApi;
use Api\MobileErr;
/***
小方法
***/
class CheckInput
{
    /**
     * 验证参数是否为int类型
     * int 校验的参数
     **/
    public static function IntEmpty($int)
    {
        if (empty($int) || !is_int($int)) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '错误的参数类型');
        }
        return  true;
    }
    /**
     * 验证参数是否为数字类型
     * string 校验的参数
     **/
    public static function NumericEmpty($string)
    {
        if (empty($string) || !is_Numeric($string)) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '错误的参数类型');
        }
        return  true;
    }
     /**
     * 验证参数是否为数字类型且为正整数
     * string 校验的参数
     **/
    public static function PositiveIntegerEmpty($string)
    {
        
        if (empty($string) || !is_Numeric($string)||preg_match("/^[0-9]*[1-9][0-9]*$/",$string)<=0) {  
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数错误');
        }
        return  true;
    }
    
    

    public static function IpCity($getIp)
    {
        
        $content = file_get_contents("http://api.map.baidu.com/location/ip?ak=7IZ6fgGEGohCrRKUE9Rj4TSQ&ip={$getIp}&coor=bd09ll");
        $json = json_decode($content);
        if(empty($json->{'content'}->{'address'})){
           return ""; 
        }else{
        return $json->{'content'}->{'address'}; //按层级关系提取address数据
        }
    }
    /*public static function array_remove($arr,$arr2)
    {
    $a = count($arr2);
    for($i=1;$i<$a;$i++){
    $arr = unset($arr[$arr2[$i]]);
    return $arr;
    }
    
    }*/
}
