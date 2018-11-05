<?php
/**
 * 功能基础
 * 
 * 主用于触发处理
 * @author yunyi
 */
namespace core;

class MethodBase
{
	public $_result = null;
	
	public function getResult()
	{
		return $this->_result;
	}
	
	public function addResult($key, $value)
	{
		$this->_result[$key] = $value;
	}
}