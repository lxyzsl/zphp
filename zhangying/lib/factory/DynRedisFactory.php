<?php
namespace factory;

class DynRedisFactory
{
	private static $instances = array();
	
	/**
	 * @return \store\Redis
	 */
	public static function getInstance($serverID)
	{
		$config = \ZPHP\Core\Config::get('redisdyn');
		$config['name'] .= '_'.$serverID;
		
		if (isset(self::$instances[$config['name']])) {
			return self::$instances[$config['name']];
		}
		self::$instances[$config['name']] = new \ZPHP\Cache\Adapter\Redis($config);
		self::$instances[$config['name']]->selectDb($serverID);
		return self::$instances[$config['name']];
	}
}