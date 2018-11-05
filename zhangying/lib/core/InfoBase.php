<?php
/**
 * 动态数据基类
 * @author yunyi
 */
namespace core;

use tools\Func,ZPHP\Exception\GameException,ZPHP\Exception\Error;

class InfoBase
{
    protected $_info = null;					// 模块成员信息
    private $_infoFields = array();			// 模块成员字段
    private $_changedFields = array();		// 成员变更记录
    private $_infoForUpdate = false;		// 是否开启了数据锁
    private $_infoTransVersion = 0;			// 事务版本
    private $_classRef = null;				// 配置文件加载
    /**
     * @var \core\BaseStruct
     */
    protected $_baseStruct = 0;
    
    public function __construct($baseStruct, $struct, $module = '')
    {
    	$this->_baseStruct = $baseStruct;
    	if ($module == '') {
    		list($nameSpace1,$nameSpace2,$nameSpace3, $object) = explode('\\', get_class($this));
    	} else {
    		$object = $module;
    	}
    	$this->_classRef = \factory\RefFactory::getInstance($object);
    	$this->initInfo($struct);
    }
    
    public function getBaseStruct()
    {
    	return $this->_baseStruct;
    }
    
    // 是否更新开关
    public function setForUpdate($forUpdate)
    {
    	$this->_infoForUpdate = $forUpdate;
    	if($this->getForUpdate()) {
    		$this->_infoTransVersion = $this->_baseStruct->GameData->transVersion();
    	}
    }
    
    public function getForUpdate()
    {
    	return $this->_infoForUpdate;
    }
    
    public function reSetForUpdate()
    {
    	if (!$this->getForUpdate()) {
    		$this->setForUpdate(true);
    		$this->setInfo();
    	}
    }
    
    // 判断指定的属性是否属于Info
    public function isInfoField($field)
    {
    	return array_key_exists($field,(array)$this->_infoFields);
    }
    
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
    
    // 填充成员数据
    public function setInfo()
    {
    	$info = $this->_baseStruct->GameData->initData($this->_classRef, $this->getInfo(), $this->getForUpdate());
    	if (!empty($info)) {
    		$this->initInfo($info);
    	}
    }
    
    // insert update
    public function save()
    {
    	if(!$this->getForUpdate()) {
    		throw new GameException(Error::DB_NONE_FORUPDATE);
    	}
    	if(!$this->_infoTransVersion) {
    		throw new GameException(Error::DB_NEED_TRANSACTION);
    	}
    	$primary = $this->_classRef->getConstant('PRIMARY');
    	if ($this->$primary) {
    		if (!empty($this->getChangedFields())) {
    			$this->_baseStruct->GameData->update($this->_classRef, $this->getChangedFields(), $this->getInfo());
    		}
    	} else {
    		$this->$primary = $this->_baseStruct->GameData->add($this->_classRef, $this->getInfo());
    	}
    }
    
    // remove
    public function remove()
    {
    	return $this->_baseStruct->GameData->remove($this->_classRef, $this->getInfo());
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
    
    // 返回更改过的属性
    protected function getChangedFields()
    {
        return array_keys($this->_changedFields);
    }
    
    // 清除更改过的属性信息
    protected function clearChangedFields()
    {
        $this->_changedFields = array();
    }
    
    // 成员是否修改过
    public function isFieldChanged($field)
    {
    	return array_key_exists($field, $this->_changedFields);
    }
    
    // 返回成员历史信息
    public function getFieldHistoryValue($field)
    {
    	return array_key_exists($field, $this->_changedFields) ? $this->_changedFields[$field] : false;
    }
    
    // -- isset get set 魔术方法 -- //
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
            	if(call_user_func(array($this, $method),$value)) {
            		$this->_changedFields[$name] = $oldvalue;
            	}
        	}
        } else if (array_key_exists($name, $this->_infoFields)) {
        	if($this->_info->$name != $value) {
                $this->_changedFields[$name] = $this->_info->$name;
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