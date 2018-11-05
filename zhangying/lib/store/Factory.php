<?php
/**
 * 数据工厂
 * 
 * @author yunyi
 */
namespace store;

class Factory
{
	public static $_instance = array();
	
	public static function getInstanc($type, $config)
	{
		if (empty(self::$_instance[$config['name']][$type])) {
			$name = 'store\\'.$type;
			self::$_instance[$config['name']][$type] = new $name($config);
		}
		
		return self::$_instance[$config['name']][$type];
	}
	
	public static $_dataInstance = array('SysData'=>array());
	
	public static function getDataInstantce($type, $servID = 0)
	{
		if (empty(self::$_dataInstance[$type][$servID])) {
			$name = 'store\\'.$type;
			self::$_dataInstance[$type][$servID] = new $name($servID);
		}
		
		return self::$_dataInstance[$type][$servID];
	}
}