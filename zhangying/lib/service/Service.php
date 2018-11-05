<?php
/**
 * 接口控制类
 * @author yunyi
 */
namespace service;

use tools\Func;
use ZPHP\Core\Config;
use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;

class Service
{
	// -- properties -- //
	private $_serverNamespace = '';						// 服务接口命名空间
	private $_serverName = '';							// 服务名
	private $_serverMethod = '';						// 接口名
	/**
	 * @var \core\ServiceBase
	 */
	private $_server = '';								// 服务实例
	
	public function __construct($stream)
	{
		$this->_serverNamespace = Config::get('server_path');
		$this->init($stream);
	}
	
	/**
	 * 接口信息初始化
	 */
	public function init($stream)
	{
		$stream = Func::decodeGameData($stream);
		
		if (!\is_object($stream)) {
			throw new GameException(Error::COMMON_PARAM_ERROR);
		}
		// not selected serv
		if (intval($stream->servID) <= 0 && intval($stream->crossID) <= 0) {
			throw new GameException(Error::COMMON_PARAM_ERROR);
		}
		list($this->_serverName,$this->_serverMethod) = explode('.', $stream->client->func);
		// not exist server
		if (!\class_exists($this->_serverNamespace.$this->_serverName)) {
			throw new GameException(Error::COMMON_PARAM_ERROR);
		}
		// not exist func
		$className = $this->_serverNamespace.$this->_serverName;
		$this->_server = new $className($stream);
		if (!\method_exists($this->_server, $this->_serverMethod)) {
			throw new GameException(Error::COMMON_PARAM_ERROR);
		}
	}
	
	/**
	 * 执行操作
	 */
	public function opr()
	{

		$this->_server->{$this->_serverMethod}();
		$result = $this->_server->getInfo();
		return $result;
	}
}
?>