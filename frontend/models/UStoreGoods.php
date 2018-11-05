<?php
namespace frontend\models;
use \yii;
use \Exception;
use yii\helpers\Tools;
use yii\base\ErrorException;
use app\models\base\StoreGoods;
class UStoreGoods extends StoreGoods
{
    /**
     * 查询单条条数据
     * @param $condition
     * @return array|yii\db\ActiveRecord[]
     * @throws ErrorException
     */
    public static function _get_info($condition)
    {
        try {
            $dbSource = parent::find();
            $dbSource->where($condition);
            return $dbSource->one();
        }
        catch (exception $e) {
            return false;
            // var_dump($e);die;
            //            Tools::LogN('查询业主账户信息失败', $e);
            //            throw new ErrorException('查询业主账户信息失败', 500);
        }
    }

    /**
     * 查询多条数据
     * @param $condition
     * @return array|yii\db\ActiveRecord[]
     * @throws ErrorException
     * 
     */
    public static function _get_data($condition, $search)
    {
        try {
            $dbSource = parent::find();

            $dbSource->where($condition);
            if (!empty($search)) {
                $dbSource->andWhere(['like', 'goods_name', $search]);
            }
            //echo $dbSource->createCommand()->getRawSql();
            return $dbSource->all();
        }
        catch (exception $e) {
            return false;

        }
    }

    /**
     * 搜索查询多条数据
     * @param $condition
     * @search goods_name 关键字
     * @return array|yii\db\ActiveRecord[]
     * @throws ErrorException
     * 
     */
    public static function _get_likedata($condition, $search, $startpage, $pagesize)
    {
        try {
            $dbSource = parent::find();
            $dbSource->select(['store_goods_id','goods_sn','store_id', 'cat_id', 'goods_name',
                'goods_sales', 'brand_id', 'brand_detail_id', 'goods_stock', 'promote_price',
                'goods_img', 'is_state','is_delete','is_hot','is_real','is_promote','city','start_time','effective_time']);
            $dbSource->where($condition);
            if (!empty($search)) {
                $dbSource->andWhere(['like', 'goods_name', $search]);
            }
            $dbSource->offset($startpage);
            $dbSource->limit($pagesize);
            Yii::info('从mysql取数据:' . $dbSource->createCommand()->getRawSql(), 'info');
            //echo $dbSource->createCommand()->getRawSql();
            return $dbSource->all();
        }
        catch (exception $e) {
            return false;

        }
    }
}
