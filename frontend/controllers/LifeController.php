<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use Api\MobileApi;
use Api\MobileErr;
use frontend\models\UniversalDb;
use frontend\models\UStoreGoodsCategory;
use frontend\models\UStoreGoods;
class LifeController extends Controller
{
    public $enableCsrfValidation = false;


    /**
     * 获取城市列表
     */
    public function actionGetcitys()
    {
        Yii::info('获取城市列表:', 'info');
        $citylist = [['cityid' => '130500', "city" => "邢台市"], ['cityid' => '361000',
            "city" => "抚州市"],['cityid' => '411400',
            "city" => "商丘市"],['cityid' => '341200',
            "city" => "阜阳市"]];
        if (!empty($citylist)) {
            MobileApi::RetEcho(MobileErr::SUCCESS, '城市获取成功', $citylist);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '城市获取失败！', '');
        }
    }
    /**
     * 获取商城首页推荐位
     * 请求参数：recommend  参数值为：LIFESY01:幻灯图推荐位; LIFESY02:导航推荐位; LIFESY03:自定义推荐位1; LIFESY04:自定义推荐位2; LIFESY05:自定义推荐位3;
     * 
     * 返回结果：
     * thumb ：图片 
     * recommend_type : 1 秒杀活动 2 爆款 3 单条分类 4 超链接 5 单条商品 6 商品列表 7 分类列表 8 订单列表 9 购物车 10 店铺
     * target_id : 对象ID
     * target_url : 对象地址
     * title : 商品标题
     * subtitle ：商品副标题
     * price : 单价
     */
    public function actionGetrecommend()
    {
        Yii::info('获取商城首页推荐位:', 'info');
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
        $redis_key = 'StoreIndex' . $post['appid'] . '_' . $post['recommend'].'_'.$post['cityid'];
        //查redis
        $redis_list = Yii::$app->redis->get($redis_key);
        //redis为空
        if (empty($redis_list)) {
            //获取项目ID
            $project = (new UniversalDb('db'))->setTableName('system_project')->find()->
                select('id')->where(['appid' => $post['appid']])->asArray()->one();
            //根据项目ID获取推荐位ID
            $recommend = (new UniversalDb('db'))->setTableName('system_recommend')->find()->
                select('id')->where(['code' => $post['recommend'], 'project_id' => $project['id']])->
                asArray()->one();
            //根据推荐位ID获取推荐位内容
            $list = (new UniversalDb('db'))->setTableName('system_recommend_rel')->find()->
                select('title,thumb,city_id,recommend_type,target_id,target_url')->where(['recommend_id' =>
                $recommend['id'], 'status' => '1', 'city_id' => $post['cityid']])->asArray()->orderBy('sort desc')->
                all();
            if (!empty($list)) {
                //等于5的时候查商品的分类ID
                foreach ($list as $k => $v) {
                    $list[$k]['starttime'] ='';
                    $list[$k]['endtime'] = '';
                    $list[$k]['city'] = '';
                    $list[$k]['cat_id'] = '';
                    if ($v['recommend_type'] == 5) {
                        $goodsinfo = (new UniversalDb('db'))->setTableName('store_goods')->find()->
                            select('store_goods_id,cat_id,goods_name')->where(['store_goods_id' => $v['target_id']])->
                            asArray()->one();
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
        } else {
            Yii::info('从redis获取数据' . $redis_key, 'info');
            $list = json_decode($redis_list);
        }
        if (!empty($list)) {

            MobileApi::RetEcho(MobileErr::SUCCESS, '获取成功', $list);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '推荐位不存在');
        }
    }
    /**
     * 获取店铺分类
     */
    public function actionGetcategory()
    {
        Yii::info('获取店铺分类:', 'info');
        $post = Yii::$app->request->post();
        if (!isset($post['cat_id']) || !is_numeric($post['cat_id'])) {
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
        $list = [];
        $list = Yii::$app->redis->lrange('store_category_catid_' . $post['cat_id'], 0,
            10);
        //var_dump($list);die;
        if (!empty($list)) {
            Yii::info('从redis获取数据:', 'info');
            foreach ($list as $k => $v) {
                $list[$k] = json_decode($v, true);
            }
        } else {
            $list = (new UniversalDb('db'))->setTableName('store_goods_category')->find()->
                select('*')->where(['parent_id' => $post['cat_id'], 'is_show' => '1'])->asArray()->
                all();
            foreach ($list as $k => $v) {

                Yii::$app->redis->rpush('store_category_catid_' . $post['cat_id'], json_encode($v));
            }
        }
        if (!empty($list)) {
            MobileApi::RetEcho(MobileErr::SUCCESS, '分类获取成功！', $list);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '分类获取失败！', $list);
        }
    }
    /**
     * 获取店铺商品列表
     */
    public function actionGetstoregoodslist()
    {
        Yii::info('获取店铺商品列表:', 'info');
        $post = Yii::$app->request->post();
        if (empty($post['page_num']) || empty($post['requests_num']) || empty($post['store_id'])) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失',[]);
            die;
        }
        $redis_key = 'Storegoods' . $post['page_num'] . '_' . $post['requests_num'].'_'.$post['store_id'];
        $like = "";
        if (!empty($post['keyword'])) {
            $like = ["like","goods_name",$post['keyword']];
        }
        //查redis
        if (empty($post['keyword'])) {
            $redis_list = Yii::$app->redis->lrange($redis_key, 0, $post['requests_num']);
        }
        //redis为空
        if (empty($redis_list)) {
            //$goodslist = (new UniversalDb('db'))->setTableName('store_goods')->find()->
//                select('store_goods_id,cat_id,goods_sn,goods_name,goods_img,goods_sales,shop_price,promote_price')->where(['store_id' =>
//                $post['store_id'], 'is_state' => '1', 'is_hot' => '0'])->andWhere($like)->asArray()->offset(($post['page_num'] -
//                1) * $post['requests_num'])->limit($post['requests_num'])->orderBy('store_goods_id DESC')->
//                all();
                
                // 查询店铺普通商品
                $query = new \yii\db\Query();
                $query->select('store_goods_id,cat_id,goods_sn,goods_name,goods_img,goods_sales,shop_price,promote_price,is_hot')->from('store_goods')->where(['store_id' =>
                $post['store_id'], 'is_state' => '1', 'is_hot' => '0','is_delete'=>'0'])->andWhere($like)->orderBy('store_goods_id DESC');
                // 查询店铺爆款商品
                $query2 = new \yii\db\Query();
                $query2->select('store_goods_id,cat_id,goods_sn,goods_name,goods_img,goods_sales,shop_price,promote_price,is_hot')->from('activity_store_goods')->where(['store_id' =>
                $post['store_id'], 'is_state' => '1', 'is_hot' => '1','is_delete'=>'0'])->andWhere($like)->orderBy('store_goods_id DESC');
                //使用union拼接数据
                $goodslist = (new \yii\db\Query())->from(['tmpA' => $query->union($query2,true)])->offset(($post['page_num'] -1) * $post['requests_num'])->limit($post['requests_num'])->all();
            foreach ($goodslist as $k => $v) {
                $goodslist[$k]['goods_img'] = \Yii::$app->params['pic_host'] . $v['goods_img'];
                if (empty($post['keyword'])) {
                    Yii::$app->redis->rpush($redis_key, json_encode($goodslist[$k]));
                }
            }
        }else {
            Yii::info('从redis获取数据' . $redis_key, 'info');
            foreach ($redis_list as $k => $v) {
                $goodslist[] = json_decode($v, true);
            }
        }
        if (!empty($goodslist)) {
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $goodslist);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品数据不存在！',[]);
        }

    } 
    /**
     * 获取店铺详情
     */
    public function actionGetstore()
    {
        Yii::info('获取店铺详情:', 'info');
        $post = Yii::$app->request->post();
        if (empty($post['store_id'])) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失',[]);
            die;
        }
        $redis_key = 'Storeinfo' .$post['store_id'];
        //查redis
        $redis_list = Yii::$app->redis->get($redis_key);
        //redis为空
        
        if (empty($redis_list)) {
            $store = (new UniversalDb('db'))->setTableName('store_info')->find()->
                select('store_id,store_name,store_img,store_tel,address')->where(['store_id' =>
                $post['store_id'], 'state' => '1'])->asArray()->one();
            $store['store_img'] = \Yii::$app->params['pic_host'] . $store['store_img'];
            $store['store_url'] = Yii::$app->request->hostInfo.\Yii::$app->urlManager->createUrl(['content/store']).'?store_id='.$store['store_id'];
            
            Yii::$app->redis->set($redis_key,json_encode($store));
        }else {
            Yii::info('从redis获取数据' . $redis_key, 'info');
            $store = json_decode($redis_list);
        }
        if (!empty($store)) {
            MobileApi::RetEcho(MobileErr::SUCCESS, '店铺获取成功', $store);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '店铺数据不存在！',[]);
        }
    }
    /**
     * 无敌商城商品列表
     **/
    public function actionStoregoodslist()
    {
        Yii::info('开始查询店铺商品列表;', 'info');
        $post = Yii::$app->request->post();
        if (empty($post['page_num']) || empty($post['requests_num']) || empty($post['city_id'])) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失',[]);
            die;
        }
        //不允许两种条件都为空的情况下去查询
        if (empty($post['cat_id']) && empty($post['keyword'])) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失',[]);
            die;
        }
        //分类ID条件
        $where = array();
        //城市条件
        $where['city'] = $post['city_id'];
        //状态为上架状态
        $where['is_state'] = 1;
        //商品状态为0的
        $where['is_hot'] = 0;
        if (!empty($post['cat_id'])) {
            $params_cate['cat_id'] = $post['cat_id'];
            //查询该分类ID信息
            $cateinfo = UStoreGoodsCategory::_get_info($params_cate);
            if ($cateinfo['parent_id'] == 0) {
                //找出分类ID条件
                $categoryid = $this->CateIdAll($cateinfo['cat_id']);
                $where['cat_id'] = $categoryid;
            } else {
                $where['cat_id'] = $post['cat_id'];
            }
        }

        //关键字搜索条件
        $like = '';
        if (!empty($post['keyword'])) {
            $like = $post['keyword'];
        }
        //分页条件
        $startpage = ($post['page_num'] - 1) * $post['requests_num']; //第几条开始
        $pagesize = $post['requests_num']; //页数

        //$like = ['like', 'username', 'test'];
        //筛选条件

        //排序条件

        //redis
        $redisgoodslist = [];

        //没有关键字的时候才去找redis
        if (empty($post['keyword'])) {
            $redis_key = 'storegoodslist' . $post['cat_id'] . '_' . $post['city_id'] . '_' .
            $post['page_num'] . '_' . $post['requests_num'];
            $redisgoodslist = Yii::$app->redis->lrange($redis_key, 0, $post['requests_num']);
        }
        //die;
        if (empty($redisgoodslist)) {

            //开始查询mysql
            $goodslist = UStoreGoods::_get_likedata($where, $like, $startpage, $pagesize);
            //var_dump($goodslist);die;
            if (!empty($goodslist)) {
                foreach ($goodslist as $k => $v) {
                    $shopgoodslist[$k] = $v->attributes;
                    $shopgoodslist[$k]['goods_img'] = \Yii::$app->params['pic_host'] . $v->
                        attributes['goods_img'];
                    if (empty($post['keyword'])) {
                        Yii::$app->redis->rpush($redis_key, json_encode($shopgoodslist[$k]));
                    }
                }
            }
        } else {
            Yii::info('从Redis取数据:' . $redis_key, 'info');
            foreach ($redisgoodslist as $k => $v) {
                $redisgoodslist[$k] = json_decode($v, true);
            }
            $shopgoodslist = $redisgoodslist;
        }

        if (!empty($shopgoodslist)) {
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $shopgoodslist);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品获取失败！',[]);
        }


    }
    /**
     * 当catid为初级分类情况下查找出下边所有的catId
     */
    private function CateIdAll($cat_id)
    {
        $params['parent_id'] = $cat_id;
        $cate = UStoreGoodsCategory::_get_data($params);
        if (!empty($cate)) {
            foreach ($cate as $k => $v) {
                $categoryid[$k] = $v->attributes['cat_id'];
            }
            //父级ID也丢进去
            array_push($categoryid, $cat_id);
            return $categoryid;
        } else {
            return $cat_id;
        }
    }
    /**
     * 获取商品详情
     */
    public function actionGetgoods()
    {
        Yii::info('获取商品详情', 'info');
        $post = Yii::$app->request->post();
        if (empty($post['store_goods_id'])) {
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');
            die;
        }
        $goods = (new UniversalDb('db'))->setTableName('store_goods')->find()->select('store_goods_id,cat_id,store_id,goods_sn,goods_name,goods_sales,promote_price,goods_img,is_real,start_time,effective_time,is_best,is_hot,is_new')->
            where(['store_goods_id' => $post['store_goods_id']])->asArray()->one();
        if (empty($goods)) {
            MobileApi::RetEcho(MobileErr::DATA_NON, '未找到该商品！');
        }
        $goods['goods_img'] = \Yii::$app->params['pic_host'] . $goods['goods_img'];
        $goods['piclist'][] = $goods['goods_img'];
        unset($goods['goods_img']);
        $goods['goods_cont_url'] = Yii::$app->request->hostInfo . \Yii::$app->
            urlManager->createUrl(['content/cont']) . '?goodsid=' . $goods['store_goods_id'] .
            '&goods_type=life';
        if (!empty($goods)) {
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $goods);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品获取失败！');
        }
    }

    /**
     * 获取城市列表
     */
    public function actionGetcitylist()
    {
        Yii::info('获取城市列表', 'info');
        $provinces = (new UniversalDb('db'))->setTableName('system_provinces')->find()->
            select('*')->asArray()->all();
        $newarr = [];
        foreach ($provinces as $k => $v) {
            $newarr[$k]['p'] = $v['province'];
            $city = (new UniversalDb('db'))->setTableName('system_cities')->find()->select('*')->
                where(['provinceid' => $v['provinceid']])->asArray()->all();
            foreach ($city as $ck => $c) {
                $newarr[$k]['c'][$ck]['n'] = $c['city'];
                $area = (new UniversalDb('db'))->setTableName('system_areas')->find()->select('*')->
                    where(['cityid' => $c['cityid']])->asArray()->all();
                foreach ($area as $ek => $e) {
                    $newarr[$k]['c'][$ck]['a'][$ek]['s'] = $e['area'];
                }
            }
        }
        echo json_encode($newarr);
    }

    /**
     * 获取城市
     */
    public function actionGetcitylistname()
    {
        Yii::info('获取城市', 'info');
        $provinces = (new UniversalDb('db'))->setTableName('system_provinces')->find()->
            select('*')->asArray()->all();
        $newarr = [];
        foreach ($provinces as $k => $v) {
            $newarr[$k]['name'] = $v['province'];
            $newarr[$k]['idcode'] = $v['provinceid'];
            $city = (new UniversalDb('db'))->setTableName('system_cities')->find()->select('*')->
                where(['provinceid' => $v['provinceid']])->asArray()->all();
            foreach ($city as $ck => $c) {
                $newarr[$k]['sub'][$ck]['name'] = $c['city'];
                $newarr[$k]['sub'][$ck]['idcode'] = $c['cityid'];
                $area = (new UniversalDb('db'))->setTableName('system_areas')->find()->select('*')->
                    where(['cityid' => $c['cityid']])->asArray()->all();
                foreach ($area as $ek => $e) {
                    $newarr[$k]['sub'][$ck]['sub'][$ek]['name'] = $e['area'];
                    $newarr[$k]['sub'][$ck]['sub'][$ek]['idcode'] = $e['areaid'];
                }
            }
        }
        echo json_encode($newarr);
    }
    
    /**
     * 获取活动商品详情
     */
    public function actionGet_activity_goods()
    {
        Yii::info('获取爆款活动商品详情:', 'info');
        $post = Yii::$app->request->post();
        if(empty($post['goods_id'])){
            MobileApi::RetEcho(MobileErr::POST_LOSE, '参数缺失');die;
        }
        $goods = (new UniversalDb('db'))->setTableName('activity_store_goods')->find()->select('store_goods_id,activity_id,cat_id,store_id,goods_sn,goods_name,goods_sales,promote_price,goods_img,is_real,start_time,effective_time,is_best,is_hot,is_new,is_promote')->
            where(['store_goods_id' => $post['goods_id']])->asArray()->one();
        if (empty($goods)) {
            MobileApi::RetEcho(MobileErr::DATA_NON, '未找到该商品！');
        }
        $goods['goods_img'] = \Yii::$app->params['pic_host'] . $goods['goods_img'];
        $goods['piclist'][] = $goods['goods_img'];
        unset($goods['goods_img']);
        $goods['goods_cont_url'] = Yii::$app->request->hostInfo . \Yii::$app->
            urlManager->createUrl(['content/cont']) . '?goodsid=' . $goods['store_goods_id'] .
            '&goods_type=baokuan';
        if (!empty($goods)) {
            MobileApi::RetEcho(MobileErr::SUCCESS, '商品获取成功', $goods);
        } else {
            MobileApi::RetEcho(MobileErr::DATA_NON, '商品获取失败！');
        }
    }
}
