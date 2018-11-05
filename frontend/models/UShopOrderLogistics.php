<?php
namespace frontend\models;
use \yii;
use \Exception;
use yii\helpers\Tools;
use yii\base\ErrorException;
use app\models\base\ShopOrderLogistics;
class UShopOrderLogistics extends ShopOrderLogistics
{
    /**
     * ��ѯ����������
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
//            Tools::LogN('��ѯҵ���˻���Ϣʧ��', $e);
//            throw new ErrorException('��ѯҵ���˻���Ϣʧ��', 500);
        }
    }
      /**
     * ��ѯ��������
     * @param $condition
     * @return array|yii\db\ActiveRecord[]
     * @throws ErrorException
     */
    public static function _get_data($condition)
    {
        
        try {
            $dbSource = parent::find();
            $dbSource->where($condition);
            return $dbSource->all();
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * ���ݷ�ҳ��ѯ��������
     * @param $condition
     * @return array|yii\db\ActiveRecord[]
     * @throws ErrorException
     */
    public static function _get_pagedata($condition,$startpage,$pagesize)
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