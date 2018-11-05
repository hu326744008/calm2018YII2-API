<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;

class ContentController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout=false;
    
    /**
     * 获取静态页面接口
     */
    public function actionGethtml()
    {
       $url = [
           'share'=>"http://139.224.67.75/appHtml/share.html",//分享
           'sanjielife'=>"http://139.224.67.75/appHtml/sanjielife.html",//三界生活
           'sanjiestone'=>"http://139.224.67.75/appHtml/sanjiestone.html",//三界石
           'sanjietreasure'=>"http://139.224.67.75/appHtml/sanjietreasure.html",//三界宝
           'services'=>"http://139.224.67.75/appHtml/services.html",//服务
           'online'=>"http://139.224.67.75/appHtml/online.html",//上线公告
           'agent'=>"http://139.224.67.75/appHtml/agent.html",//代理商
           'business'=>"http://139.224.67.75/appHtml/business.html",//创业会员
           'attract'=>"http://139.224.67.75/appHtml/attract.html",//供应商
           'realstore'=>"http://139.224.67.75/appHtml/realstore.html",//实体店铺
           'contribution'=>"http://139.224.67.75/appHtml/contribution.html",//贡献值
           'kefu'=>"http://139.224.67.75/appHtml/kefu.html",
           'aboutour'=>"http://139.224.67.75/appHtml/aboutour.html"
           
       ];
       if(!empty($url)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '获取成功', $url);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '获取失败！');
        }
    }
    /**
     * 商品详情页面
     */
    public function actionCont()
    {
        $get = Yii::$app->request->get();
        if($get['goods_type']=='life'){
            $db_name = "store_goods";
            $key = 'store_goods_id';
        }elseif($get['goods_type']=='shop'){
            $db_name = "shop_goods";
            $key = 'goods_id';
        }elseif($get['goods_type']=='miaosha'){
            $db_name = "activity_shop_goods";
            $key = 'goods_id';
        }elseif($get['goods_type']=='baokuan'){
            $db_name = "activity_store_goods";
            $key = 'store_goods_id';
        }
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        $data['goods'] = (new UniversalDb('db'))->setTableName($db_name)
                     ->find()
                     ->select('goods_desc')
                     ->where([$key => $get['goodsid']])
                     ->asArray()
                     ->one();
        
        return $this->render('shopdetails', $data);
    }
    /**
     * 商家详情页面
     */
    public function actionStore()
    {
        $get = Yii::$app->request->get();
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        $data['store'] = (new UniversalDb('db'))->setTableName('store_info')
                     ->find()
                     ->select('store_desc')
                     ->where(['store_id' => $get['store_id']])
                     ->asArray()
                     ->one();
        
        return $this->render('storedetails', $data);
    }
    /**
     * 分享
     */
    public function actionShare()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('share', $data);
    }
    /**
     * 三界生活
     */
    public function actionSanjielife()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('sanjieshneghuo', $data);
    }
    /**
     * 三界石
     */
    public function actionSanjiestone()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('sanjieshi', $data);
    }
    /**
     * 三界宝
     */
    public function actionSanjietreasure()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('sanjiebao', $data);
    }
    
    /**
     * 生活服务
     */
    public function actionServices()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('fuwu', $data);
    }
     /**
     * 上线公告
     */
    public function actionOnline()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('online', $data);
    }
     /**
     * 代理商
     */
    public function actionAgent()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('agent', $data);
    }
     /**
     * 创业会员
     */
    public function actionBusiness()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('business', $data);
    }
     /**
     * 供应商
     */
    public function actionAttract()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('attract', $data);
    }
     /**
     * 实体店铺
     */
    public function actionRealstore()
    {
        
        $data['pc_style'] = \Yii::$app->params['pc_style'];
        
        return $this->render('realstore', $data);
    }
}
