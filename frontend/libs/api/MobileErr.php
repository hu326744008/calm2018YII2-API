<?php
namespace Api;
/***
错误类
***/
class MobileErr
{
	//请求成功
	const SUCCESS=2000;
	
	
	//POST参数缺失
	const POST_LOSE=1001;
	//POST参数校验失败
	const POST_CHKFAIL=1002;
 
	
	//系统配置列表缺失
	const SYSTEMCONF_LOSE=3001;
	//分期配置列表缺失
	const STAGE_LOSE=3002;
	//没有数据
	const DATA_NON=3003;
    //失败
    const ERROR = 3004;
    //token不匹配
    const TOKEN_WRONG = 3006;
    //secret不匹配
    const SECRET_WRONG = 3007;
    //token超时
    const TOKEN_TIMEOUT = 3008;
    //token写入缓存失败
    const TOKEN_WRITE_FALSE = 3009;
/**
 * 	***********
 * 	***********注册登陆开始	***********
 * 	***********
 */
    //用户数据插入/更新失败
	const USERUPDATE_FAIL=4001;
	//手机号码不存在
	const PHONE_NOTEXIST=4002;
	//登录密码错误
	const PASSWORD_WRONG=4003;
	//用户id不存在
	const USERID_LOSE=4004;
	//生成账号失败
	const USER_NOTCHECK=4005;
	//账号审核拒绝
	const USER_CHECKFAIL=4006;
	//账号已冻结
	const USER_FREEZE=4007;
	//注册失败
	const REGISTER_FAIL=4008;
	//支付密码错误
	const PAYPASSWORD_WRONG=4009;
    //手机号码已登录
	const TEL_EXIST=4010;
	//验证码发送失败
	const SENDMESSAGE_FAIL=4011;
	//手机号、身份证号不匹配
    const TELIDENTITY_NOTMATCH=4012;
	//用户未完善资料账号未通过审核
	const USERINFO_LOSE=4013;
	//邀请码手机号不存在
	const YAOQING_LOSE=4014;
	//验证码失效
	const INVALIDTIME=4015;
    //错误的验证码
	const ERRORCHECK=4016;
    //更新照片失败
	const ERRORPIC=4017;
	 //用户未登录
	const USERDOWN=4018;
	 //余额不足
	const BALANCE_NON=4019;
	// 注册失败
	const ENROLL_FAIL=4020;
	// 手机号已经注册过
	const PHONE_ALREADY=4021;
    //
    const TOKEN_FAIL = 4022;
/**
 * 	***********
 * 	***********群开始	***********
 * 	***********
 */
    //创建群失败
	const CREATE_GROUP=5001;
    //申请群失败
    const ADD_GROUP=5002;

/**
 * 	***********
 * 	***********商品开始	***********
 * 	***********
 */
	//没有相关商品
	const PROID_LOSE = 6001;
	// 下订单失败
	const ORDER_LOSE = 6002;
	// 加入购物车失败
	const SHOPCAR_LOSE =6003;
	//  缺少参数 不知道是曾还是减
	const TYPE_LOSE =6004;
	// 购物车ID缺失
	const SHOPCARID_LOSE =6005;
	// 商品数量以全部购买
	const SHOPCAR_OVER =6006;
	// 不存在揭晓信息
	const ANNOUNCE_LOSE =6007;
	// 
	const NUMBER_NON =6008;
	// 不存在晒单信息
	const BASKORDER_NON =6009;
	// 获取购物车信息失败
	const SHOPCAR_NON =6010;
	// 没有参与记录
	const PLAY_NON =6011;
	// 没有商品
	const PRODUCT_NON =6012;
	// 用户与商品不匹配
	const PRODUCT_WRONG =6013;
	// 订单不存在
	const ORDER_NON =6014;

/**
 * 	***********
 * 	***********地址信息开始	***********
 * 	***********
 */

	// 获取地址信息失败
	const ADDRESS_NON =7001;
	// 缺失单条地址信息ID
	const ADDRESSID_LOST =7002;
	// 修改地址信息失败
	const ADDRESSEDIT_LOST =7003;
	// 添加地址信息失败
	const ADDRESS_LOST =7004;
	// 删除地址信息失败
	const ADDRESSDLE_LOST =7005;
	

/**
 * 	***********
 * 	***********咨讯开始	***********
 * 	***********
 */
	// 获取咨讯信息失败
	const MESSAGE_NON =8001;
	// 缺失单条信息ID
	const MESSAGE_LOSE =8002;

/**
 * 	***********
 * 	***********活动开始	***********
 * 	***********
 */
 	// 缺失信息分类id
	const INFORM_NON =9001;
}