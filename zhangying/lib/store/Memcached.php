<?php
namespace store;

use ZPHP\Db\Pdo;
use ZPHP\Core\Config;

class Memcached
{
	/**
	 * @var \memcached
	 */
	private $_cache;
	private $_className;
	
	public function __construct($config, $className)
	{
		if (empty($this->_cache)) {
			$this->_cache = new \ZPHP\Cache\Adapter\Memcached($config);
		}
		$this->_className = $className;
	}
	
	public function initData($key)
	{
		$classRef = new \ReflectionClass($this->_className);
		$setting = $classRef->getStaticPropertyValue ('memcache')[$key];
		$pdo = new Pdo(Config::getField('pdo', 'master'), $this->_className);
		$list = $pdo->fetchAll();
		if (isset($setting['IndexField']) && !empty($setting['IndexField'])) {
			$result = array();
			foreach ($list as $one) {
				$result[$one->$setting['IndexField']] = $one;
			}
			$list = $result;
		}
		return $list;
	}
	
	public function get($key)
	{
		$list = $this->_cache->get($key);
		if (empty($list)) {
			$list = $this->initData($key);
			$this->_cache->set($key,$list);
		}
		return $list;
	}
	
	public function getAt($key, $index) 
	{
		$list = $this->get($key);
		return $list[$index];
	}
	/**
	 * 初始化参数
	 * @param unknown $args
	 * @return NULL|unknown
	 */
	private function initParams($args)
	{
		if (empty($args)) {
			return null;
		} else {
			return is_array($args[0]) ? $args[0] : $args;
		}
	}
}