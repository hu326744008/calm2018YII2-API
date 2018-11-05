<?php
namespace frontend\models;
use \yii;
use \Exception;
use yii\helpers\Tools;
use yii\base\ErrorException;
use app\models\base\ShopGoods;
class UShopGoods extends ShopGoods
{
    /**
     * 查询单条条数据
     * @param $condition
     * @return array|yii\db\ActiveRecord[]
     * @throws ErrorException
     */
    public static function _get_info( $condition )
    {
        try {
            $dbSource = parent::find();
            $dbSource->where($condition);
            return $dbSource->one();
        } catch (Exception $e) {
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
    public static function _get_data($condition,$search)
    {
        try {
            $dbSource = parent::find();
          
            $dbSource->where($condition);
            if (!empty($search)) {
                $dbSource->andWhere(['like', 'goods_name', $search]);
            }
           //echo $dbSource->createCommand()->getRawSql();
            return $dbSource->all();
        } catch (Exception $e) {
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
    public static function _get_likedata($condition,$search,$startpage,$pagesize)
    {
        try {
            $dbSource = parent::find();
            $dbSource->select(['goods_id','cat_id','goods_sn','goods_name','goods_stock','goods_price','goods_img','is_best','is_state','is_delete','is_new','is_hot','goods_sales','is_promote','region_name']);
            $dbSource->where($condition);
            if (!empty($search)) {
                $dbSource->andWhere(['like', 'goods_name', $search]);
            }
            $dbSource->offset($startpage);
            $dbSource->limit($pagesize);
            Yii::info('从mysql取数据:'.$dbSource->createCommand()->getRawSql(), 'info');
           //echo $dbSource->createCommand()->getRawSql();
            return $dbSource->all();
        } catch (Exception $e) {
            Yii::info('从mysql取数据异常:'.$e, 'info');
            return false;
           
        }
    }
}
