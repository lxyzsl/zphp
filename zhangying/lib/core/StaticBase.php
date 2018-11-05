<?php
/**
 * 静态数据基类
 * @author yunyi
 */
namespace core;

use tools\Func;
use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;
use enum\EnumGameObject;
use ZPHP\Core\Config;
use common\Common;

class StaticBase
{
    private $_info = null;
    private $_infoFields = array();
    
    /**
     * @var \ReflectionClass
     */
    private $_classRef = null;
    
    public function __construct($struct)
    {
    	list($nameSpace2,$nameSpace3,$object) = explode('\\', get_class($this));
    	$this->_classRef = \factory\RefFactory::getInstance($object);
        $this->initInfo($struct);
    }
    
//    public function popLanguage($prop)
//    {
//    	$primary = $this->_classRef->getConstant('PRIMARY');
//    	return \sys\language\Tools::getLanguage('Sql', $this->_classRef->getConstant('MODULE'), $this->$primary, $prop, Common::$server->Language);
//    }
    
    public function primaryValue()
    {
    	$primary = $this->_classRef->getConstant('PRIMARY');
    	return $this->$primary;
    }
    
    // 判断指定的属性是否属于Info
    public function isInfoField($field)
    {
    	return array_key_exists($field,$this->_infoFields);
    }
    // 初始化成员
    protected function initInfo($info)
    {
        if(!empty($info)) {
            if(count($this->_infoFields) == 0) {
                $this->_infoFields = Func::getClassFields($info);
            }
            $this->_info = $info;
        }
    }
    // 填充成员数据
    public function setInfo()
    {
    	$info = \factory\SysDataFactory::getInstance()->initSysData($this->_classRef, $this->getInfo());
    	$moduleName = $this->_classRef->getConstant('MODULE');
    	if (!empty($info)) {
    		$this->initInfo($info);
    	} else if (!in_array($moduleName, array(EnumGameObject::MISC, 
    		EnumGameObject::AWARD_TIME_DROP, EnumGameObject::USER_AWARD_USER, EnumGameObject::CROSS, 
    		EnumGameObject::CROSS_SERVER,EnumGameObject::ACTION, EnumGameObject::GUIDE, EnumGameObject::LANGUAGE))) {
    		throw new GameException(Error::COMMON_PARAM_ERROR);
    	}
    }
    // 返回成员数据
    public function getInfo()
    {
    	return $this->_info;
    }
    // 判断属性信息是否存在
    public function existInfo()
    {
    	return !(is_null($this->_info));
    }
    // -- isset get魔术方法 -- //
    public function __isset($name)
    {
        if(array_key_exists($name,$this->_infoFields)) {
            return true;
        }
    }
    public function __SET($name,$value)
    {
    	$method = "set{$name}";
    	$getmethod = "get{$name}";
    	if (method_exists($this, $method)) {
    		$oldvalue = call_user_func(array($this,$getmethod));
    		if ($oldvalue!=$value) {
    			call_user_func(array($this, $method),$value);
    		}
    	} else if (array_key_exists($name, $this->_infoFields)) {
    		if($this->_info->$name != $value) {
    			$this->_info->$name = $value;
    		}
    	}
    }
    public function __GET($name)
    {
    	$method = "get{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        } else if (array_key_exists($name, $this->_infoFields)) {
            return $this->_info->$name;
        }
    }
}