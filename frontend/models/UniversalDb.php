<?php

/**
 * 通用Model
 * Class UniversalDb
 * @author  Shaco
 * @date    2016-07-10  15:30
 * @update  修改主键自定义参数 2016-10-31 14:00
 */
 
namespace frontend\models;

use yii\db\ActiveRecord;

class UniversalDb extends ActiveRecord {
	public $db = null;
	public static $dbStr = '';
	public $model_cars = '';
	public $displacement = '';
	public $type = '';
	public $color = '';
	public $RawSql = '';
	public static $table = '';
	function __construct($db = 'db') {
		self::$dbStr = $db;
		$dbName = self::$dbStr;
		$this->db = \yii::$app->$dbName;
	}
	
	/**
	 *
	 * @return obj 返回该AR类关联的对象
	 */
	public static function setTableName($tableName) {
		self::$table = $tableName;
		$class = __class__;
		return new $class ( self::$dbStr );
	}
	
	/**
	 *
	 * @return string 返回该AR类关联的数据表名
	 */
	public static function tableName() {
		return self::$table;
	}
	public static function className() {
		return self::$dbStr;
	}
	public static function getDb() {
		$dbName = self::$dbStr;
		return \Yii::$app->$dbName;
	}
	/**
	 *  根据ID获取单条
	 * @params  
     *    $key = 字段名称
     *    $id = 字段值
	 */
	function getOneById($key,$id) {
		$this->RawSql = "SELECT * FROM " . self::tableName () . " WHERE $key='$id'";
		return $this->db->createCommand ( $this->RawSql )->queryOne ();
	}
	/**
	 *  根据条件获取数据
	 * @params  
     *    $where = 条件
     *    $order = 排序
     *    $type  =  查询方式，空值为多条查询
	 */
	function getFind($where = "", $order = "", $type = 0) {
		if (empty ( $where )) {
			$where = ' 1=1 ';
		}
		if (empty ( $order )) {
		    $command = $this->db->createCommand ( "SELECT * FROM " . self::tableName () . " where $where " );
		}else{
		    $command = $this->db->createCommand ( "SELECT * FROM " . self::tableName () . " where $where  ORDER BY $order " );
		}
		if (empty ( $type )) {
			return $lists = $command->queryAll ();
		} else {
			return $lists = $command->queryOne ();
		}
	}
	/**
	 *  根据条件获取数据
	 * @params  
     *    $key = 主键名称
     *    $id =  主键值，空值为添加，存在为编辑
     *    $params  =  参数数组 与数据库字段名称相同
	 */
	function addOrEdit($key,$id = 0 , $params) {
		foreach ( $params as $name => $value ) {
			if ($this->hasAttribute ( $name )) {
				$save_data_add [$name] = $value;
			}
		}
		$this->RawSql = "SELECT * FROM " . self::tableName () . " WHERE $key='$id'";
		$Obj = $this->db->createCommand ( $this->RawSql )->queryOne ();
		
		if (empty ( $Obj )) {
			$status = $this->db->createCommand ()->insert ( self::tableName (), $save_data_add )->execute ();
			if ($status) {
				$status = $this->db->getLastInsertID ();
			}
		} else {
			$status = $this->db->createCommand ()->update ( self::tableName (), $save_data_add, "$key='$id'" )->execute ();
			if ($status) {
				$status = $id;
			}
		}
		return $status;
	}
    /**
	 *  根据条件获取修改
	 * @params  
     *    $where = 条件
     *    $params  =  参数数组 与数据库字段名称相同
	 */
	function updateByWhere($where, $params) {
		return $this->db->createCommand ()->update ( self::tableName (), $params, $where )->execute ();
	}
    
    /**
	 *  根据参数删除单条
	 * @params  
     *    $key = 字段名称
     *    $id  =  字段值
	 */
	function del($key,$value) {
		return $this->db->createCommand ()->delete ( self::tableName (), "$key='$value'" )->execute ();
	}
    /**
	 *  根据条件删除多条
	 * @params  
     *    $where = 条件
	 */
	function delWhere($where) {
		return $this->db->createCommand ()->delete ( self::tableName (), $where )->execute ();
	}
    /**
	 *  根据条件统计
	 * @params  
     *    $where = 条件
	 */
	function count($where = "") {
		if (empty ( $where )) {
			$where = ' 1=1 ';
		}
		$command = $this->db->createCommand ( "SELECT count(*) FROM " . self::tableName () . " where $where " );
		if (empty ( $type )) {
			return $lists = $command->queryAll ();
		} else {
			return $lists = $command->queryOne ();
		}
	}
}
