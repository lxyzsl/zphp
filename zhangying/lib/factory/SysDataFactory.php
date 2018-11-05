<?php
namespace factory;

class SysDataFactory
{
	private static $instances = '';
	
	/**
	 * @return \store\SysData
	*/
	public static function getInstance()
	{
		if (!empty(self::$instances)) {
			return self::$instances;
		}
		
		self::$instances = \store\Factory::getDataInstantce('SysData', 0);
		return self::$instances;
	}
}