<?php
namespace frontend\libs;
/**
 * 生成订单编号 
 */
class CreateOrderNum
{
    //生成订单编号  参数：用户推荐码
    public function Create($usercode)
    {
        $num = date('YmdHis').rand('100000','999999').$usercode;
        $get_order = (new UniversalDb('db'))->setTableName('store_order_info')->getFind('store_order_sn='.$num);
        if(!empty($get_order)){
            return self::Create($usercode);
        }else{
            return $num;
        }
    }
}
?>