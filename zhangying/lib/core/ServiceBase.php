<?php
/**
 * 服务接口基础
 * 
 * @author yunyi
 */
namespace core;

use tools\Func;
use enum\EnumGameObject;

class AServiceBase
{
	const CTRL_NAME = '';
	
	protected $_info = null;					// 模块成员信息
	protected $_infoFields = array();			// 模块成员字段
	
	// 初始化成员
	protected function initInfo($info)
	{
		if(!empty($info)) {
			if(count($this->_infoFields) == 0) {
				$this->_infoFields = Func::getClassFields($info);
			}
			$this->_info = $info;
			$this->_changedFields = array();
		}
	}
	
	/**
	 * @return \service\Struct
	 */
	public function getInfo()
	{
		return $this->_info;
	}
	
	/**
	 * @param $name
	 * @param $value
	 */
	public function __SET($name,$value)
	{
		if (array_key_exists($name, $this->_infoFields)) {
			if($this->_info->$name !== $value) {
				$this->_info->$name = $value;
			}
		}
	}
	
	/**
	 * @param $name
	 */
	public function __GET($name)
	{
		if (array_key_exists($name, $this->_infoFields)) {
			return $this->_info->$name;
		}
	}
}

class ServiceBase extends AServiceBase
{
	/**
	 * @var \core\BaseStruct
	 */
	protected $_baseStruct = null;
	/**
	 * @var \core\CtrlBase
	 */
	protected $_ctrl = '';
	/**
	 * @var \service\Struct
	 */
	
	public function __construct($stream)
	{
		$this->initInfo(new \service\Struct());
//		\common\Common::init();
		$this->initBaseStruct($stream);
		$this->initCtrl();
	}
	
	public function __destruct()
	{
		if ($this->isSucce && !empty($this->_ctrl)) {
			$this->_ctrl->actionLog();
		}
		unset($this->_ctrl);
	}
	
	public function initBaseStruct($stream)
	{
		$this->_baseStruct = new \core\BaseStruct($stream);
	}
	
	public function initCtrl()
	{
		if (static::CTRL_NAME != '') {
			$ctrlName = '\api\ctrl\\'.static::CTRL_NAME;
			$this->_ctrl = new $ctrlName($this->_baseStruct);
		}
	}
	
	/**
	 * @param 错误信息写入
	 */
	public function pushError($mes)
	{
		$this->error = $mes;
		$this->isSucce = 0;
	}
	
	// -- TODO RECYLE -- //
	/**
	 * @return \core\BaseStruct
	 */
	public function getBaseStruct()
	{
		return $this->_baseStruct;
	}
}