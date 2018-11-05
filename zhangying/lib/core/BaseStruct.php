<?php
/************************
 * 功能基础结构
 ************************/
namespace core;

use common\Common;
use ZPHP\Core\Config;
use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;
use tools\Func;
class BaseStruct
{
	public $Module = '';						// 
	public $LoginID = 0;						// login user roleid
	public $Systime = 0;						// system time
	/**
	 * @var \store\GameData
	 */
	public $GameData = null;					// dyn db handle
	/**
	 * @var \store\Redis
	 */
	public $Params = array();					// interface params
	public $Func = array();						// array(server name，server func)
	
	public function __construct($stream, $gameData = null, $dynRedis = null, $module = 'Api')
	{
		if(!empty($stream))
		{
			$this->Module = $module;
			$this->LoginID = $stream->loginID;
			$this->Params = $stream->client->params;
			$this->Systime = time();
			list($this->Func['Server'],$this->Func['Func']) = explode('.', $stream->client->func);
			$this->GameData = new \store\GameData();
		}
	}


	public function DynRedis()
	{
		return \factory\DynRedisFactory::getInstance(0);
	}
}
