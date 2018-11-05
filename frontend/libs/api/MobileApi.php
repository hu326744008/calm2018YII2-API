<?php
namespace Api;
use Yii;
header("Content-type: text/html; charset=utf-8");
/***
接口基类
***/
class MobileApi
{
	//参数验证
	/***
	$rightkey:正确的参数名数组(特别注意，参数名一定要按a-z字母顺序！！！)
	$actualvalue:实际POST过来的参数值数组
	***/
    public static function VerifyPOST($actualvalue,$rightkey='')
	{
		if( empty($actualvalue['time']) || empty($actualvalue['token']) ){
			self::RetEcho(MobileErr::POST_LOSE,'time或token参数缺失');
		}
		$str='key='.Yii::app()->params['mobileapi_secretkey'].'time='.$actualvalue['time'];
		if(!empty($rightkey)){
			foreach($rightkey as $eachkey){
				if(!isset($actualvalue[$eachkey])){
					self::RetEcho(MobileErr::POST_LOSE,'POST参数缺失');
				}
				else{
					$str.=$eachkey.'='.$actualvalue[$eachkey];
				}
			}
		}
		$actualmd5=strtoupper(md5($str));
		
		//token验证
		if($actualmd5!=$actualvalue['token']){
			self::RetEcho(MobileErr::POST_CHKFAIL,'token校验失败');
		}
		else{
			//token校验成功
		}
	}
    
    //接口安全验证
    public static function index(){
        
        
        echo 11;
    }
	
	
	//json格式返回 
    //$errno  状态
    //$errmsg 消息
    //$data   数组
	public static function RetEcho($errno,$errmsg,$data=null)
	{
	    //Yii::info('返回code：'.$errno.'返回提示：'.$errmsg.'返回数据：'.$data, 'info');
		$retarr=array('errno'=>$errno,'errmsg'=>$errmsg,'data'=>$data);
        Yii::info(json_encode($retarr), 'info');
		echo json_encode($retarr);
		exit(0);
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