<?php
/**
 * 动态数据模块工厂
 * 
 * @author yunyi
 */
namespace factory;

use ZPHP\Exception\Error,ZPHP\Exception\GameException;
class Factory
{
	private static $instances = array();
	
	public static function getInstance($baseStruct, $class, $params, $forUpdate = false)
	{
		if (!is_array($params)) $params = array($params);
		$classMes = \enum\EnumGameObject::getMesByName($class);
// 		if ($classMes['Type'] != 'dyn') throw new GameException(Error::OBJ_ACCESS_NULL); 
		if ($classMes['Type'] != 'DYNAMIC') throw new GameException(Error::OBJ_ACCESS_NULL);
		$keyName = $class.'_'.implode('_', $params);
// 		$keyName = $classMes['Name'].'_'.implode('_', $params);
		if (isset(self::$instances[$keyName]) && !empty(self::$instances[$keyName])) {
			if (self::$instances[$keyName]['ForUpdate'] == $forUpdate || $forUpdate == false) {
				return self::$instances[$keyName]['Class'];
			}
		}
		$className = $classMes['Route'];
		if (!\class_exists($className)) {
			throw new GameException(Error::OBJ_ACCESS_NULL);
		}
// 		$params['Class'] = $classMes['Name'];
		self::$instances[$keyName]['ForUpdate'] = $forUpdate;
		self::$instances[$keyName]['Class'] = new $className($baseStruct, $params, $forUpdate);
// 		self::$instances[$baseStruct->ServerID][$keyName]['Class'] = new $className($baseStruct, $params, $classMes['Name'], $forUpdate);
		return self::$instances[$keyName]['Class'];
	}
	
	public static function closeInstance()
	{
		if (!isset(self::$instances) || empty(self::$instances)) return;
		foreach (self::$instances as $keyName=>$object) {
			unset(self::$instances[$keyName]);
		}
	}
	
	public static function battleClose()
	{
		self::$instances = array();
	}
}