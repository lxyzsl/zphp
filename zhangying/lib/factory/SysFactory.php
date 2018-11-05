<?php
/**
 * 静态数据模块工厂
 * 
 * @author yunyi
 */
namespace factory;

use ZPHP\Exception\Error;
use ZPHP\Exception\GameException;
class SysFactory
{
	private static $instances = array();
	
	public static function getInstance($class, $params)
	{
		if (!is_array($params)) $params = array($params);
		$classMes = \enum\EnumGameObject::getMesByName($class);
		if ($classMes['Type'] != 'SYS') throw new GameException(Error::OBJ_ACCESS_NULL);
		$keyName = $class.'_'.implode('_', $params);
		if (isset(self::$instances[$keyName])) {
			return self::$instances[$keyName];
		}
		$className = $classMes['Route'];
		if (!\class_exists($className)) {
			throw new GameException(Error::OBJ_ACCESS_NULL);
		}
		self::$instances[$keyName] = new $className($params);
		return self::$instances[$keyName];
	}
}