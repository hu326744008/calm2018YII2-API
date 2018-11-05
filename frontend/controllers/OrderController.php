<?php
namespace frontend\controllers;
header("Content-Type: text/html; charset=UTF-8");
use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
use frontend\models\UShopOrderGoods;
use frontend\models\UShopOrderInfo;
use frontend\models\UShopOrderLogistics;
use frontend\models\UStoreOrderInfo;
use frontend\models\UStoreOrderCode;


class OrderController extends Controller
{
    public $enableCsrfValidation = false;
    
    
    
     /**
     * 获取商城订单数量接口
     * user_id 用户id
     */
    public function actionShopordernum(){
        $post = Yii::$app->request->post();
        if(empty($post['user_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        //总数
        $where['user_id'] = $post['user_id'];
        $shopordernum['total'] = UShopOrderInfo::_count_data($where);
        //待付款数量
        $wait_pay['user_id'] = $post['user_id'];
        $wait_pay['order_status'] = 1;
        $shopordernum['wait_pay'] = UShopOrderInfo::_count_data($wait_pay);
         //待收货数量
        $wait_receipt['user_id'] = $post['user_id'];
        $wait_receipt['order_status'] = 2;
        $shopordernum['wait_receipt'] = UShopOrderInfo::_count_data($wait_receipt);
        //已完成
        $complete['user_id'] = $post['user_id'];
        $complete['order_status'] = 3;
        $shopordernum['complete'] = UShopOrderInfo::_count_data($complete);
        
        if(!empty($shopordernum)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '数据获取成功', $shopordernum);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '数据获取失败');
        }
        
    }
    /**
     * 获取商城订单接口
     * page_num  页数
     * requests_num 请求条数
     * user_id 用户id
     * order_status 订单状态
     */
    public function actionGetshoporderall()
    {
       $post = Yii::$app->request->post();
        if(empty($post['page_num'])||empty($post['requests_num'])||empty($post['user_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $where['user_id'] = $post['user_id'];
        if(!empty($post['order_status'])){
           $where['order_status'] = $post['order_status'];
        }
        //查询订单
        $orderlist = UShopOrderInfo::_get_pagedata($where,($post['page_num']-1)*$post['requests_num'],$post['requests_num']);
        //查询订单商品
        foreach($orderlist as $k=>$v){
            $wheregoods['shop_order_no'] = $v->order_no;
            
            $goodslist = UShopOrderGoods::_get_data($wheregoods);
           
            
            if(!empty($goodslist)){
              foreach($goodslist as $g){
                $goodslists[] = $g->attributes;
              }  
            }
            
            $orderlists[$k] = $v->attributes;
            $orderlists[$k]['goodslist'] = $goodslists;
        }
       
        if(!empty($orderlists)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '订单数据获取成功', $orderlists);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '订单数据获取失败');
        }
    }
     /**
     * 获取店铺订单数量接口
     * user_id 用户id
     */
    public function actionStoreordernum(){
        $post = Yii::$app->request->post();
        if(empty($post['user_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        //总数
        $where['user_id'] = $post['user_id'];
        $shopordernum['total'] = UStoreOrderInfo::_count_data($where);
        //待付款数量
        $wait_pay['user_id'] = $post['user_id'];
        $wait_pay['order_status'] = 0;
        $shopordernum['wait_pay'] = UStoreOrderInfo::_count_data($wait_pay);
         //待收货数量
        $wait_receipt['user_id'] = $post['user_id'];
        $wait_receipt['order_status'] = 1;
        $shopordernum['wait_receipt'] = UStoreOrderInfo::_count_data($wait_receipt);
        //已完成
        $complete['user_id'] = $post['user_id'];
        $complete['order_status'] = 2;
        $shopordernum['complete'] = UStoreOrderInfo::_count_data($complete);
        
        if(!empty($shopordernum)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '数据获取成功', $shopordernum);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '数据获取失败');
        }
        
    }
    /**
     * 获取店铺订单接口
     * page_num  页数
     * requests_num 请求条数
     * user_id 用户id
     * order_status 订单状态
     */
    public function actionGetstoreorderall()
    {
       $post = Yii::$app->request->post();
        if(empty($post['page_num'])||empty($post['requests_num'])||empty($post['user_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $where['user_id'] = $post['user_id'];
        if(!empty($post['order_status'])){
           $where['order_status'] = $post['order_status'];
        }
        //查询订单
        $orderlist = UShopOrderInfo::_get_pagedata($where,($post['page_num']-1)*$post['requests_num'],$post['requests_num']);
        //查询订单商品
        foreach($orderlist as $k=>$v){
            $wheregoods['shop_order_no'] = $v->order_no;
            
            $goodslist = UShopOrderGoods::_get_data($wheregoods);
           
            
            if(!empty($goodslist)){
              foreach($goodslist as $g){
                $goodslists[] = $g->attributes;
              }  
            }
            
            $orderlists[$k] = $v->attributes;
            $orderlists[$k]['goodslist'] = $goodslists;
        }
       
        if(!empty($orderlists)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '订单数据获取成功', $orderlists);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '订单数据获取失败');
        }
    }
    
}
