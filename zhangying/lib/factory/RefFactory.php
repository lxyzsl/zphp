<?php
namespace factory;

use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;
class RefFactory
{
	private static $instances = array();
	
	/**
	 * @return \ReflectionClass
	 */
	public static function getInstance($class)
	{
		$classMes = \enum\EnumGameObject::getMesByName($class);
// 		$keyName = $classMes['Name'];
		$keyName = $class;
		if (isset(self::$instances[$keyName])) {
			return self::$instances[$keyName];
		}
		
		$constantName = $classMes['ConstRoute'];
		if (!\class_exists($constantName)) {
			throw new GameException(Error::COMMON_PARAM_ERROR);
		}
		
		self::$instances[$keyName] = new \ReflectionClass($constantName);
		return self::$instances[$keyName];
	}
}