<?php
/**
 * 控制核心
 */

namespace control\core;

use tools\Func;
class Control
{
	private $_server = null;
	
	// -- -- //
	private $_params = null;
	private $_action = 'main';
	private $_serverID = 0;
	private $_method = 'index';
	private $_file = '';
	
	public function __construct($server)
	{
		$this->_server = $server;
		$this->init($this->_server->getParams());
	}
	
	public function init($params)
	{
		$this->_action = isset($params['a']) && !empty($params['a']) ? $params['a'] : 'main';
		$this->_method = isset($params['m']) && !empty($params['m']) ? $params['m'] : 'index';
		$this->_serverID = isset($params['s']) && !empty($params['s']) ? $params['s'] : 0;
		$this->_params = isset($params['p']) && !empty($params['p']) ? Func::decodeGameData($params['p'], true) : array();
		$this->_file = isset($params['f']) && !empty($params['f']) ? explode(',', $params['f']) : array();
	}
	
	public function run()
	{
		$this->_server->setTplModule($this->_action);
		$this->_server->setTplFile($this->_method.'.php');
    	$this->_server->display(array("params"=>$this->_params,"s"=>$this->_serverID,"f"=>$this->_file));
	}
}