<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
use frontend\models\UShopGoodsCategory;
use frontend\models\UShopGoods;
use frontend\libs\components\CheckInput;

class GoodsController extends Controller
{
    public $enableCsrfValidation = false;
    
    /** 
     * 获取商城首页推荐位
     * 请求参数：recommend  参数值为：SHOPSY01:幻灯图推荐位; SHOPSY02:导航推荐位; SHOPSY03:加盟推荐位; SHOPSY04:自定义推荐位1; SHOPSY05:自定义推荐位2; SHOPSY06:自定义推荐位3; SHOPSY07:自定义推荐位4;
     * 
     * 返回结果：
     * thumb ：图片 
     * recommend_type : 1 秒杀活动 2 爆款 3 单条分类 4 超链接 5 单条商品 6 商品列表 7 分类列表 8 订单列表 9 购物车
     * target_id : 对象ID
     * target_url :
     */
    public function actionGetrecommend()
    {       
        Yii::info('获取商城首页推荐位;', 'info');
        $post = Yii::$app->request->post();
        if(empty($post['appid'])||empty($post['recommend'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        //验证TOKEN
        /**$token_endTime = Yii::$app->redis->get($post['token']);
        
        if(empty($token_endTime)){
            MobileApi::RetEcho(MobileErr::TOKEN_WRONG, 'token不匹配', '');die;
        }elseif($token_endTime<date('Y-m-d H:i:s')){
            MobileApi::RetEcho(MobileErr::TOKEN_TIMEOUT, 'token超时', '');die;
        }**/
        $redis_key = 'ShopIndex'.$post['appid'].'_'.$post['recommend'];
        //查redis
        $redis_list = Yii::$app->redis->get($redis_key);
        //redis为空
        if(empty($redis_list)){
        //获取项目ID
        $project = (new UniversalDb('db'))->setTableName('system_project')->find()->select('id')->where(['appid' => $post['appid']])->asArray()->one();
        //根据项目ID获取推荐位ID
        $recommend = (new UniversalDb('db'))->setTableName('system_recommend')->find()->select('id')->where(['code' => $post['recommend'],'project_id'=>$project['id']])->asArray();
       
        Yii::info('获取推荐位sql:' . $recommend->createCommand()->getRawSql(), 'info');
        $recommend = $recommend->one();
      
        //根据推荐位ID获取推荐位内容
        $list = (new UniversalDb('db'))->setTableName('system_recommend_rel')
                 ->find()
                 ->select('title,thumb,city_id,recommend_type,target_id,target_url')
                 ->where(['recommend_id' => $recommend['id'],'status'=>'1'])
                 ->asArray();
        Yii::info('根据推荐位ID获取推荐位内容sql:' . $list->createCommand()->getRawSql(), 'info');
        $list = $list->all();
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
        }
        
        Yii::$app->redis->set($redis_key,json_encode($list));
        }else{
             Yii::info('从redis获取数据'.$redis_key, 'info');
            $list = json_decode($redis_list);
        }
        if(!empty($list)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '获取成功', $list);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '推荐位不存在');
        }
    }
    /**
     * 获取分类
     */
    public function actionGetcategory()
    {
        Yii::info('获取商城分类;', 'info');
        $post = Yii::$app->request->post();
        if(!isset($post['cat_id'])&&is_numeric($post['cat_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
       
        //验证TOKEN
        /**$token_endTime = Yii::$app->redis->get($post['token']);
        
        if(empty($token_endTime)){
            MobileApi::RetEcho(MobileErr::TOKEN_WRONG, 'token不匹配', '');die;
        }elseif($token_endTime<date('Y-m-d H:i:s')){
            MobileApi::RetEcho(MobileErr::TOKEN_TIMEOUT, 'token超时', '');die;
        }**/
        $list = [];
        $list = Yii::$app->redis->lrange('shop_category_catid_'.$post['cat_id'], 0, 10);
        if(!empty($list)){
             foreach($list as $k=>$v){
                $list[$k] = json_decode($v,true);
             }
        }else{
            $list = (new UniversalDb('db'))->setTableName('shop_goods_category')->find()->select('*')->where(['parent_id' => $post['cat_id'],'is_show'=>'1'])->asArray()->all();
            foreach($list as $k=>$v){
                
                Yii::$app->redis->rpush('shop_category_catid_'.$post['cat_id'], json_encode($v));
            }
        }
        if(!empty($list)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '分类获取成功！', $list);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '分类获取失败！');
        }
    }
    /**
     * 获取商品列表
     */
    public function actionGetgoodslist()
    {
        $post = Yii::$app->request->post();
        if(empty($post['page_num'])||empty($post['requests_num'])||empty($post['cat_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $goodslist = (new UniversalDb('db'))->setTableName('shop_goods')
                     ->find()
                     ->select('goods_id,cat_id,goods_sn,goods_name,goods_stock,goods_price,goods_img,is_best,is_new,is_hot,region_name')
                     ->where(['cat_id' => $post['cat_id'],'is_state' => '1','is_promote'=>'0'])
                     ->asArray()
                     ->offset(($post['page_num']-1)*$post['requests_num'])
                     ->limit($post['requests_num'])
                     ->orderBy('goods_id DESC')
                     ->all();
        foreach($goodslist as $k=>$v){
            $goodslist[$k]['goods_img'] = \Yii::$app->params['pic_host'].$v['goods_img'];
        }
        if(!empty($goodslist)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $goodslist);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品数据不存在！');
        }
    }
    /**
     * 无敌商城商品列表
     **/
    public function actionShopgoodslist(){
        Yii::info('开始查询商品列表;', 'info');
        $post = Yii::$app->request->post();
        CheckInput::PositiveIntegerEmpty(@$post['page_num']);
        CheckInput::PositiveIntegerEmpty(@$post['requests_num']);
        //CheckInput::PositiveIntegerEmpty(@$post['cat_id']);

        //不允许两种条件都为空的情况下去查询
        if(empty($post['cat_id'])&&empty($post['keyword'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        //分类ID条件
        $where = array();
        //状态为上架状态
        $where['is_state'] = 1;
        //商品未删除状态
        $where['is_delete'] = 0;
        //商品状态为0的
        $where['is_promote'] = 0;
        if(!empty($post['cat_id'])){
            $params_cate['cat_id'] = $post['cat_id'];
            //查询该分类ID信息
            $cateinfo =  UShopGoodsCategory::_get_info($params_cate);
            if($cateinfo['parent_id']==0){
                //找出分类ID条件
                $categoryid = $this->CateIdAll($cateinfo['cat_id']);
                $where['cat_id'] = $categoryid;
            }else{
                $where['cat_id'] = $post['cat_id'];
            }
        }

        //关键字搜索条件
        $like= '';
        if(!empty($post['keyword'])){
           $like  = $post['keyword'];
        }
        //分页条件
        $startpage = ($post['page_num']-1)*$post['requests_num'];//第几条开始
        $pagesize  =  $post['requests_num'];//页数

       //$like = ['like', 'username', 'test'];
        //筛选条件
        //排序条件
        $redisgoodslist = [];
       
         //没有关键字的时候才去找redis
        if(empty($post['keyword'])){
          $redis_key = 'shopgoodslist'.$post['cat_id'].'_'.$post['page_num'].'_'.$post['requests_num'];
          $redisgoodslist = Yii::$app->redis->lrange($redis_key, 0, $post['requests_num']);
        }
        //die;
        if(empty($redisgoodslist)){
            //Yii::info('从数据库取数据;', 'info');
            //开始查询mysql
            $goodslist = UShopGoods::_get_likedata($where,$like,$startpage,$pagesize);
          
            if(!empty($goodslist)){
                foreach($goodslist as $k=>$v){
                    $shopgoodslist[] = $v->attributes;
                    $shopgoodslist[$k]['goods_img'] = \Yii::$app->params['pic_host'].$v->attributes['goods_img'];
                    if(empty($post['keyword'])){
                    Yii::$app->redis->rpush($redis_key, json_encode($shopgoodslist[$k]));
                    }
                }
            }
       }else{
            Yii::info('从Redis取数据:'.$redis_key, 'info');
            foreach($redisgoodslist as $k=>$v){
                $redisgoodslist[$k] = json_decode($v,true);
             }
            $shopgoodslist = $redisgoodslist;
        }
        // Yii::info('商品列表return:'.@json_encode($shopgoodslist), 'info');
        if(!empty($shopgoodslist)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $shopgoodslist);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品获取失败！');
        }
       
       
       
    }
    /**
     * 当catid为初级分类情况下查找出下边所有的catId
     */
    public function CateIdAll($cat_id){
        $params['parent_id'] = $cat_id;
        $cate = UShopGoodsCategory::_get_data($params);
        if(!empty($cate)){
            foreach($cate as $k=>$v){
                 $categoryid[$k] = $v->attributes['cat_id'];
            }
            //父级ID也丢进去
            array_push($categoryid,$cat_id);
            return $categoryid; 
        }else{
            return $cat_id;
        }
    }
    /**
     * 获取商品详情
     */
    public function actionGetgoods()
    {
        Yii::info('获取商品详情:', 'info');
        $post = Yii::$app->request->post();
        if(empty($post['goods_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $goods = (new UniversalDb('db'))->setTableName('shop_goods')
                     ->find()
                     ->select('goods_id,cat_id,goods_sn,goods_name,goods_stock,goods_price,goods_img,is_best,is_new,is_hot,is_promote,region_name')
                     ->where(['goods_id' => $post['goods_id']])
                     ->asArray()
                     ->one();
       
        if(empty($goods)){
             MobileApi::RetEcho(MobileErr::DATA_NON, '商品不存在');die;
        }
        $goods['goods_img'] = \Yii::$app->params['pic_host'].$goods['goods_img'];
        $goods['piclist'][] = $goods['goods_img'];
        unset($goods['goods_img']);
        $goods['goods_cont_url'] = Yii::$app->request->hostInfo.\Yii::$app->urlManager->createUrl(['content/cont']).'?goodsid='.$goods['goods_id'].'&goods_type=shop';
         //商品属性
        $goodsId = $post['goods_id'];
        $query = new \yii\db\Query();
        $data['attrlist'] = $query->select("*")
            ->from('shop_goods_mutil_prise sgmp')
            ->where("sgmp.gmp_goods_id = $goodsId")
            ->all();
      
        if(!empty($data['attrlist'])){
            //价格20161228 胡天培
          //  $prics = array_column($data['attrlist'],'gmp_price');
//          
//            rsort($prics);
//            $data['max'] = $prics[0];
//            sort($prics);
//            $data['little'] = $prics[0];
            //属性
            $group = array_column($data['attrlist'],'gmp_json_attr');
            $str = implode(",",$group);
            $str = str_replace("|",",",$str);
            $str = explode(",",$str);
            $str = array_unique($str);
            foreach($str as $item){
                $a[] = explode("_",$item);
            }
            //var_dump($a);die;

            foreach ($a as $item2){
                //属性名
                $e[] = $item2[0];
                $f[] = $item2[1];
            }
            $string = array_unique($e);
            //$string2 = array_unique($f);
            //var_dump($string);die;
            $arr = [];
            foreach($string as $k=>$v){
                foreach($a as $c){
                    if($v==$c[0]){
                        $arr[$k][] = $c[1];
                    }
                }
                $tag[] = $query->select("ga.attr_name")->from('goods_attribute ga')->where("ga.attr_id = $v")->one();
                $arrid[] = $v;
            }
            //$data['tag'] = $tag;
            //$data['num'] = count($data['tag']);
            //$data['arrid'] = $arrid;
            foreach($arr as $k=>$v){
                $new[$k] = $tag[$k];
                $new[$k]['arrid'] = $arrid[$k];
                $new[$k]['arr'] = $v;
            }
            $data['new'] = $new;        
            $goods['attr'] = $data;
        }
        
      
        if(!empty($goods)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $goods);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品获取失败！');
        }
    }
    
    /**
     * 获取活动商品详情
     */
    public function actionGet_activity_goods()
    {
        Yii::info('获取秒杀活动商品详情:', 'info');
        $post = Yii::$app->request->post();
        if(empty($post['goods_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $goods = (new UniversalDb('db'))->setTableName('activity_shop_goods')
                     ->find()
                     ->select('goods_id,cat_id,activity_id,goods_sn,goods_name,goods_stock,goods_price,goods_img,is_best,is_new,is_hot,is_promote,region_name')
                     ->where(['goods_id' => $post['goods_id']])
                     ->asArray()
                     ->one();
       
        if(empty($goods)){
             MobileApi::RetEcho(MobileErr::DATA_NON, '商品不存在');die;
        }
        $goods['goods_img'] = \Yii::$app->params['pic_host'].$goods['goods_img'];
        $goods['piclist'][] = $goods['goods_img'];
        unset($goods['goods_img']);
        $goods['goods_cont_url'] = Yii::$app->request->hostInfo.\Yii::$app->urlManager->createUrl(['content/cont']).'?goodsid='.$goods['goods_id'].'&goods_type=miaosha';
        
        if(!empty($goods)){
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $goods);
        }else{
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品获取失败！');
        }
    }
}
