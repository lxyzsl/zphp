<?php
/**
 * redis
 */
namespace store;

use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;
class Redis
{
	private $redis;
	
	public function __construct($config)
	{
		if (empty($this->redis) || $this->redis->ping() != 'PONG') {
			$this->redis = new \ZPHP\Cache\Adapter\Redis($config);
		}
	}
	
	public function selectDb($db)
	{
		try {
			$this->redis->selectDb($db);
		} catch (GameException $e) {
			throw new GameException(Error::REDIS_SERVER_WENT_AWAY);
		} 
	}
	
	public function keys($key)
	{
		return $this->redis->keys($key);
	}
	
	public function add($key, $data)
	{
		return $this->redis->set($key, $data);
	}
	
	public function get($key)
	{
		return $this->redis->get($key);
	}
	
	public function set($key, $value, $expiration = 0)
	{
		return $this->redis->set($key, $value, $expiration);
	}
	
	public function exists($key)
	{
		return $this->redis->exists($key);
	}
	
	public function hSet($key, $field, $value)
	{
		return $this->redis->hSet($key, $field, $value);
	}
	
	public function hGet($key, $field)
	{
		return $this->redis->hGet($key, $field);
	}
	
	public function hmSet($key, $value)
	{
		$value = is_array($value) ? $value : \tools\Func::classToArray($value);
		$this->redis->hmSet($key, $value);
	}
	
	public function hmGet($key, $field)
	{
		$field = is_array($field) ? $field : array($field);
		return $this->redis->hmGet($key, $field);
	}
	
	public function hGetAll($key)
	{
		return $this->redis->hGetAll($key);
	}
	
	public function beginTrans()
	{
		return $this->redis->multi();
	}
	
	public function commitTrans()
	{
		return $this->redis->exec();
	}
	
	public function rollTrans()
	{
		return $this->redis->discard();
	}
	
	public function watch()
	{
		return $this->redis->watch();
	}
	
	public function unWatch()
	{
		return $this->redis->unwatch();
	}
	
	public function expire($key, $time)
	{
		return $this->redis->expire($key, $time);
	}
	
	public function ttl($key)
	{
		return $this->redis->ttl($key);
	}
	
	public function del($key)
	{
		return $this->redis->del($key);
	}
	
	public function flushall()
	{
		return $this->redis->flushall();
	}
	
	public function close()
	{
		return $this->redis->close();
	}
	
	public function ping()
	{
		$this->redis->ping();
	}
}