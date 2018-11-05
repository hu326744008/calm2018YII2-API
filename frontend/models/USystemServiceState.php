<?php
namespace frontend\models;
use \yii;
use \Exception;
use yii\helpers\Tools;
use yii\base\ErrorException;
use app\models\base\SystemServiceState;
class USystemServiceState extends SystemServiceState
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
            $dbSource->orderBy('id DESC');
             Yii::info('从mysql取数据:'.$dbSource->createCommand()->getRawSql(), 'info');
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
     */
    public static function _get_data($condition,$startpage,$pagesize)
    {
        try {
            $dbSource = parent::find();
            $dbSource->where($condition);
            $dbSource->offset($startpage);
            $dbSource->limit($pagesize);
           // echo $dbSource->createCommand()->getRawSql();
//            die;
            return $dbSource->all();
         
        } catch (Exception $e) {
            return false;
            
        }
    }
}
