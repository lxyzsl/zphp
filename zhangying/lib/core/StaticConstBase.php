<?php
namespace core;

use tools\Func;
use enum\EnumStageDb;
class StaticConstBase
{
	// 模块名
	const MODULE = 'Static';
	
	// -- db -- //
	// 表名
	const TABLE_NAME = 'Table';
	// 是否判定enabled
	const ENABLED_SHOW = 0;
	// 设置主键
	const PRIMARY = 'PrimaryID';
	// 设置查询字段
	public static $fields = array();
	
	// -- redis -- //
	// redis keys 包含的字段
	public static $Redis_Key = array();

	public static $Info_Key = array();

	public static $Where_Key = array();
	
	//根据key获取数据列表
	public static $Index_List = array();
	
	/**
	 * 在这里把游戏服数据库和stage库中的数据合并
	 * @param unknown $mysql
	 * @param unknown $mysqlStage
	 * @param unknown $classRef
	 */
	public static function redisInit($mysql, $mysqlStage, $classRef)
	{
		$result = $mysql->getSysList($classRef);
//		$stageDbList = Func::getAsEnum(new EnumStageDb());
//
//		$loadFromStage = false;
//		$moduleName = $classRef->getConstant('MODULE');
//		if (in_array($moduleName, $stageDbList)) $loadFromStage = true;
//
//		if($loadFromStage)
//		{
//			$resultStage = $mysqlStage->getSysList($classRef);
//			$result = array_merge($result,$resultStage);
//		}
		return $result;
	}
}