<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
use frontend\libs\WeixinPay;
/**
 * Site controller
 */
class PayapiController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * 微信生成预支付订单
     */
    public function actionWechatpaytest()
    {
        Yii::info('微信生成预支付订单', 'info');
        $post = Yii::$app->request->post();
        if(empty($post['pay_no'])||empty($post['attach'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失', '');die;
        }
        if(empty($post['goods_name'])){
            $post['goods_name'] = "三界生活大礼包";
        }
        //获取订单
        $get_order = (new UniversalDb('db'))->setTableName('pay_order')->getFind('pay_no='.$post['pay_no'],'','1');
        if(empty($get_order)){
            MobileApi::RetEcho(MobileErr::ORDER_NON, '订单不存在', '');die;
        }   
        //微信支付地址获取
        $wechat_url = (new UniversalDb('db'))->setTableName('system_general_code')->getFind('code_name="WXPAY_NOTIFY_URL"','','1');
        //拼接数据
        $money = $get_order['txamnt']*100;
        $WxPayHelper = new WeixinPay('wx96e9ad2511b39b42','1412054202','AqutmZoTbEHJTqXV0eLYj4QRNBkKGadr',$wechat_url['code_value'].'wxpay/wxpayNotify');
        $response = $WxPayHelper->getPrePayOrder($post['goods_name'], $get_order['pay_no'], $money,$post['attach']);
        if($response['return_code']!='SUCCESS'){
           MobileApi::RetEcho(MobileErr::ORDER_LOSE, $response['return_msg'], $response);die;
        }
     
        $x = $WxPayHelper->getOrder($response['prepay_id']);
        if(!empty($x)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '预支付订单生成成功！', $x);
        }else{
            MobileApi::RetEcho(MobileErr::ORDER_LOSE, '预支付订单生成失败！', '');
        }
    }

   
    //微信支付生成预付款订单
    public function actionWechatpay()
    {
        $post = Yii::$app->request->post();
        //$post['store_order_sn'] = "161128160210334259111057";
        if(empty($post['store_order_sn'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失', '');die;
        }
        //获取订单
        $get_order = (new UniversalDb('db'))->setTableName('store_order_info')->getFind('store_order_sn='.$post['store_order_sn'],'','1');
        if(empty($get_order)){
            MobileApi::RetEcho(MobileErr::ORDER_NON, '订单不存在', '');die;
        }
        //获取订单商品
        $get_order_goods = (new UniversalDb('db'))->setTableName('store_goods')->getFind('store_goods_id='.$get_order['store_goods_id'],'','1');
        if(empty($get_order_goods)){
            MobileApi::RetEcho(MobileErr::PROID_LOSE, '商品不存在', '');die;
         }
        
        //微信支付地址获取
        $wechat_url = (new UniversalDb('db'))->setTableName('system_general_code')->getFind('code_name="WXPAY_NOTIFY_URL"','','1');
        $wechat_url['code_value'] = '1111';
        //拼接数据
        $money = $get_order['order_amount']*100;
        $WxPayHelper = new WeixinPay('wx96e9ad2511b39b42','1412054202','AqutmZoTbEHJTqXV0eLYj4QRNBkKGadr',$wechat_url['code_value'].'wxpay/wxpayNotify');
        $response = $WxPayHelper->getPrePayOrder($get_order_goods['goods_name'], $get_order['store_order_sn'], $money);
        if($response['return_code']!='SUCCESS'){
           MobileApi::RetEcho(MobileErr::SUCCESS, '生成预支付交易单失败', $response);die;
        }
        $x = $WxPayHelper->getOrder($response['']);
        if(!empty($x)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '预支付订单生成成功！', $x);
        }else{
            MobileApi::RetEcho(MobileErr::ORDER_LOSE, '预支付订单生成失败！', '');
        }
    }
        //微信扫码支付
    public function actionWechatsmpay()
    {
        $post = Yii::$app->request->post();
        $post['store_order_sn'] = "1111111111";
        if(empty($post['store_order_sn'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失', '');die;
        }
        //获取订单
       // $get_order = (new UniversalDb('db'))->setTableName('store_order_info')->getFind('store_order_sn='.$post['store_order_sn'],'','1');
       // if(empty($get_order)){
       //     MobileApi::RetEcho(MobileErr::ORDER_WRONG, '订单不存在', '');die;
      //  }
        //获取订单商品
       // $get_order_goods = (new UniversalDb('db'))->setTableName('store_goods')->getFind('store_goods_id='.$get_order['store_goods_id'],'','1');
      // if(empty($get_order_goods)){
       //     MobileApi::RetEcho(MobileErr::PROID_LOSE, '商品不存在', '');die;
      //  }
        
        //微信支付地址获取
        $wechat_url = (new UniversalDb('db'))->setTableName('system_general_code')->getFind('code_name="WXPAY_NOTIFY_URL"','','1');
        //$wechat_url['code_value'] = '1111';
        //拼接数据
        $money = 1;
        $WxPayHelper = new WeixinPay('wx96e9ad2511b39b42','1412054202','AqutmZoTbEHJTqXV0eLYj4QRNBkKGadr',$wechat_url['code_value'].'wxpay/wxpayNotify');
        $response = $WxPayHelper->getPrePayOrder('测试', $post['store_order_sn'], $money);
        if($response['return_code']!='SUCCESS'){
           MobileApi::RetEcho(MobileErr::SUCCESS, '生成预支付交易单失败', $response);die;
        }
        $x = $WxPayHelper->getOrder($response['prepay_id']);
        if(!empty($x)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '预支付订单生成成功！', $x);
        }else{
            MobileApi::RetEcho(MobileErr::ORDER_LOSE, '预支付订单生成失败！', '');
        }
    }

}
