<?php
namespace store;
class Apc
{
	private $apc;
	
	public function __construct($config)
	{
		if (empty($this->apc)) {
			$this->apc = \ZPHP\Manager\Apc::getInstance();
		}
	}
	
	public function enable()
	{
		return $this->apc->enable();
	}
	
	public function selectDb($db)
	{
		return $this->apc->selectDb($db);
	}
	
	public function add($key, $value, $timeOut = 0)
	{
		return $this->apc->add($key, $value, $timeOut);
	}
	
	public function set($key, $value, $timeOut = 0)
	{
		return $this->apc->set($key, $value, $timeOut);
	}
	
	public function get($key)
	{
		return $this->apc->get($key);
	}
	
	public function delete($key)
	{
		return $this->apc->delete($key);
	}
	
	public function increment($key, $step = 1)
	{
		return $this->apc->increment($key, $step);
	}
	
	public function decrement($key, $step = 1)
	{
		return $this->apc->decrement($key, $step);
	}
	
	public function clear()
	{
		return $this->apc->clear();
	}
}