<?php
/***************************************
 * 控制成核心文件
 **************************************/
namespace core;

use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;
use tools\Func;
class CtrlBase
{
	/**
	 * @var \core\BaseStruct
	 */
	public $_baseStruct = null;
	
	public function __construct($baseStruct)
	{
		$this->_baseStruct = $baseStruct;
	}
	
	public function __destruct()
	{
		\factory\Factory::closeInstance();
	}
	
	public function getBaseStruct()
	{
		return $this->_baseStruct;
	}
	
	public function __get($name)
	{
		if (array_key_exists($name, $this->_baseStruct->Params)) {
			return $this->_baseStruct->Params->$name;
		} else {
			return '';
		}
	}
	
	/**
	 * 登入用户检查
	 */
	public function checkRole()
	{
		if (!array_key_exists('RoleID', $this->_baseStruct->Params) || $this->RoleID <= 0) {
			throw new GameException(Error::COMMON_ROLE_PARAM);
		}
		if ($this->RoleID != $this->_baseStruct->LoginID) {
			throw new GameException(Error::COMMON_ACCESS_OTHERS_DATA);
		}
	}
	
	/**
	 * 更改行为记录信息
	 * @param 主记录行为名 $actionName
	 * @param 执行次数 $times
	 * @param 行为信息描述 $activityParams
	 * @param 辅助行为记录列表 $assist
	 */
	public function changeActionMes($trigger='', $times = 1, $actionParams = array(), $assist = array(), $roleID = 0)
	{
		$this->_baseStruct->Func['MainAction'] = $trigger;
		$this->_baseStruct->Func['Times'] = $times;
		$this->_baseStruct->Func['ActionParams'] = $actionParams;
		if (count($assist) > 0) {
			$this->_baseStruct->Func['Assist'] = $assist;
		}
		if ($roleID > 0) {
			$this->_baseStruct->Func['RoleID'] = $roleID;
		}
	}

	public function actionLog()
	{
		\game\module\actionlog\Tools::record($this->_baseStruct);
	}
	
	// -- TODO 魔术方法__get代替 -- //
	public function getParam($name)
	{
		if (isset($this->_baseStruct->Params->$name)) {
			return $this->_baseStruct->Params->$name;
		}
	}
	
	public function getParams() {
		$result = array();
		$params = func_get_args();
		foreach ($params as $name) {
			if (isset($this->_baseStruct->Params->$name)) {
				$result[] = $this->_baseStruct->Params->$name;
			}
		}
		return $result;
	}
	// -- -- //
}