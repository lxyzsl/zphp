<?php
/**
 * 静态数据加载---数据层模块
 * @author yunyi
 */
namespace store;

use module\RefFactory;
use tools;
use tools\Func;
use enum\EnumStageDb;
use ZPHP\Core\Config;
use enum\EnumGameObject;
use exception\GameException;
use exception\Error;
use common\Common;

abstract class ASysData
{
	/**
	 * @var \ReflectionClass
	 */
	protected $_classRef;
	/**
	 * @var \store\Pdo
	 */
	protected $_mysql;
	protected $_mysqlStage;
	/**
	 * @var \ZPHP\Cache\Adapter\Redis
	 */
	protected $_redis;
	
	/**
	 * @var \ZPHP\Cache\Adapter\Apc
	 */
	protected $_apc;
	
	/**
	 * redis 集合
	 * @var unknown
	 */
	protected $_zList = [];
	
	public static function popStageMysql()
	{
		$serverInfo = \sys\server\Tools::getOneFromIni();
		$sysStage = sprintf(\sys\server\Constant::DB_FORMAT, $serverInfo['DBStageDNSName'], $serverInfo['StageDBUser'], $serverInfo['StageDBPass'], $serverInfo['DBPrefix'].$serverInfo['DBStage']);
		return new \store\Pdo(Func::initDbConfig($sysStage));
	}
	
	public function __construct()
	{}
	
	public function __destruct()
	{
		$this->closeMysql();
		$this->closeRedis();
	}
	/**
	 * @param $info
	 * @param 模糊数据列表 $fuzzy
	 * @return Ambigous <string, mixed>
	 */
	public function getKey($info,array $fuzzy = array())
	{
		$key = '';
	    $redisKeys = $this->initRedisKey();
	    
	    foreach ($redisKeys as $field) {
	        $key .= $info->$field.':';
	    }
	    return substr($key, 0, -1);
	}
	
	public function initRedisKey()
	{
		$redisKey = $this->_classRef->getStaticPropertyValue('Redis_Key');
		$redisKey = is_array($redisKey) ? $redisKey : array($redisKey);
		return $redisKey;
	}
	
	public function initRedis($lan = '')
	{
		$redisDb = 0;
		if (Config::get('CheckLanDb')) {
			$ref = \factory\RefFactory::getInstance(EnumGameObject::LAN_DB);
			$lanList = self::popStageMysql()->getSysList($ref);
			foreach ($lanList as $lanInfo) {
				if ($lanInfo->Lan == $lan) {
					$redisDb = $lanInfo->RedisDb;
					break;
				}
			}
		}
		$this->_redis = new \ZPHP\Cache\Adapter\Redis(\ZPHP\Core\Config::get('redis'));
		$this->_redis->selectDb($redisDb);
	}
	
	public function initApc()
	{
		if (is_null($this->_apc)) {
			$this->_apc = \ZPHP\Manager\Apc::getInstance();
		}
	}
	
	public function closeRedis()
	{
		$this->_redis = null;
	}
	
	public function clearAll()
	{
		if ($this->_redis) {
			$this->_redis->flushall();
		}
		if ($this->_apc) {
			$this->_apc->clear();
		}
	}
	
	private $_lanDb = '';
	public function startMysql($landb = '')
	{
		if($landb != $this->_lanDb) {
			$this->_lanDb = $landb;
			$this->_mysql = null;
			$this->_mysqlStage = null;
		}
		$serverInfo = \sys\server\Tools::getOneFromIni();
		
		if ($landb) {
			$dbName = $serverInfo['DBPrefix'].$serverInfo['DBSys'].'_'.$landb;
		} else {
			$dbName = $serverInfo['DBPrefix'].$serverInfo['DBSys'];
		}
		
		$sysDb = sprintf(\sys\server\Constant::DB_FORMAT, $serverInfo['DBSysDNSName'], $serverInfo['DBUser'], $serverInfo['DBPass'], $dbName);
		$sysStage = sprintf(\sys\server\Constant::DB_FORMAT, $serverInfo['DBStageDNSName'], $serverInfo['StageDBUser'], $serverInfo['StageDBPass'], $serverInfo['DBPrefix'].$serverInfo['DBStage']);
		$this->_mysql = new \store\Pdo(Func::initDbConfig($sysDb));
		$this->_mysqlStage = new \store\Pdo(Func::initDbConfig($sysStage));
	}
	
	public function closeMysql()
	{
		if (!is_null($this->_mysql)) {
			$this->_mysql->close();
			unset($this->_mysql);
		}
		if (!is_null($this->_mysqlStage)) {
			$this->_mysqlStage->close();
			unset($this->_mysqlStage);
		}
	}
	
	public function init($classRef)
	{
		$this->_classRef = $classRef;
	}
}

class SysData extends ASYsData
{
	/**
	 * 静态模块数据初始化
	 * 单条数据
	 */
	public function initSysData($classRef, $info)
	{
		$this->init($classRef);
		$module = $this->_classRef->getConstant('MODULE');
		$redisKeys = $this->initRedisKey();
		$key = $module;
		foreach ($redisKeys as $field) {
			$key = $key. ':'.$info->$field;
		}
		if (Config::get('apc_open', 1)) {
			$this->initApc();
			$newKey = 'One_'.$key;
			$result = $this->_apc->get($newKey);
			if (!$result) {
				$this->initRedis($this->initLanByModule($module));
				$this->init($classRef);
				$result = $this->_redis->hGetAll($key);
				$this->_apc->add($newKey, $result);
			}
		} else {
			$this->initRedis($this->initLanByModule($module));
			$this->init($classRef);
			$result = $this->_redis->hGetAll($key);
		}
		return $result;
	}
	
	public function initLanByModule($module)
	{
		$db = '';
		if (Config::get('CheckLanDb')) {
			if (!in_array($module, ['Cross','Server','LanDb','Language'])) {
				$lanDb = \factory\SysFactory::getInstance(EnumGameObject::LAN_DB, Common::$server->getInfo()->Language);
				$db = $lanDb->Lan;
			}
		}
		return $db;
	}
	
	/**
	 * 获取静态数据列表
	 * 多条数据
	 */
	public function getSysList($classRef, $indexKey = '', $indexValue = 0)
	{
		$this->init($classRef);
		$moduleName = $this->_classRef->getConstant('MODULE');
		$key = '';
		if($indexKey)
		{
			$key = $moduleName.'-'.$indexKey.':'.$indexValue;
		}else{
			$key = $moduleName.'-List';
		}
		$result = [];
		if(Config::get('apc_open', 1))
		{
			$this->initApc();
			$newKey = 'List_'.$key;
			$result = $this->_apc->get($newKey);
			if(!$result)
			{
				$this->initRedis($this->initLanByModule($moduleName));
				$this->init($classRef);
				$list = $this->_redis->sMembers($key);
				$primary = $this->_classRef->getConstant('PRIMARY');
				foreach ($list as $key) {
					$info = $this->_redis->hGetAll($moduleName.':'.$key);
					$result[$info->{$primary}] = $info;
				}
				$this->_apc->add($newKey, $result);
			}
		} else {
			$this->initRedis($this->initLanByModule($moduleName));
			$this->init($classRef);
			$list = $this->_redis->sMembers($key);
			$primary = $this->_classRef->getConstant('PRIMARY');
			foreach ($list as $key) {
				$info = $this->_redis->hGetAll($moduleName.':'.$key);
				$result[$info->{$primary}] = $info;
			}
		}
		return $result;
	}
	
	// -- 缓存生成 Begin -- // 
	public function redisRefactoring($lan = '', $enum = '')
	{
		$this->initRedis($lan);
		$list = \tools\Func::getGameObjectList();
		if ($enum == '') {
		    $this->_redis->clear();
		    $del = false;
		    $enumList = $list;
		} else {
		    $del = true;
		    if (!in_array($enum, $list)) {
		        return 'no exist '.$enum;
		    }
		    $enumList = [$enum];
		}
		foreach ($enumList as $enum) {
			$result[$enum] = $this->initData($lan, $enum, $del);
		}
		return $result;
	}
	
	public function initData($lan, $enum, $del = false)
	{
	    $this->startMysql($lan);
		$classMes = \enum\EnumGameObject::getMesByName($enum);
		$classRef = \factory\RefFactory::getInstance($enum);
		$this->init($classRef);
		if ($del) $this->delOneModule($enum);
		$list = $classMes['ConstRoute']::redisInit($this->_mysql, $this->_mysqlStage, $classRef);
		$module = $this->_classRef->getConstant('MODULE');

		$indexList = $this->_classRef->getStaticPropertyValue('Index_List');
		$this->_zList[$module] = [];
		foreach ($list as $one) {
    		$oneKey = $this->getKey($one);
    		$this->_zList[$module]['List'][] = $oneKey;
    		if (count($indexList) > 0) {
    		    foreach ($indexList as $index) {
    		        $this->_zList[$module][$index.':'.$one->$index][] = $oneKey;
    		    }
    		}
    		$one = Func::classToArray($one);
    		$this->_redis->hmSet($module.':'.$oneKey, $one);
		}
		if (isset($this->_zList[$module])) {
    		foreach ($this->_zList[$module] as $key=>$iList) {
                $this->_redis->sadd($module.'-'.$key,$iList);
    		}
		}
		return $list;
	}
	
	// -- 缓存生成 End -- //
	/**
	 * 重建单个模块缓存，需要先清理
	 * @param unknown $enum
	 */
	public function delOneModule($enum)
	{
		$key = $enum.'-List';
		$list = $this->_redis->sMembers($key);
		$this->_redis->delete($key);
		foreach ($list as $one) {
			$this->_redis->delete($enum.':'.$one);
		}
	}
	
	/**
	 * 获取数据表所有字段
	 */
	public function tableAllStruct($tableName,$dbIP)
	{
		$this->startMysql();
		$serverInfo = \sys\server\Tools::getOneFromIni();
		$connect = mysql_connect($dbIP, $serverInfo['DBUser'], $serverInfo['DBPass']);
		$fields = mysql_list_fields($serverInfo['DBPrefix'].$serverInfo['DBSys'], $tableName, $connect);
		$columns = mysql_num_fields($fields);
		$fieldList = [];
		for ($i = 0; $i < $columns; $i++) {
			$fieldList[]= array("Name"=>mysql_field_name($fields, $i),"Type"=>mysql_field_type($fields,$i));//,"Desc"=>mysql_field_comment($fields,$i)
		}
		return $fieldList;
	}
	
	/**
	 * 初始化server.ini
	 */
	public function initConfig()
	{ 
		$configInfo = \sys\server\Tools::getConfigFromIni();
		$tmpTemplete = "'%s' => '%s',\n";
		$tmp = '';
		foreach ($configInfo as $key=>$val) {
			$tmp .= sprintf($tmpTemplete, $key, $val);
		}
		foreach (['game','cross','control','backapi'] as $app) {
			$structPath = ROOT_PATH.'/config/'.$app.'/init.php';
			$file = fopen($structPath, 'w+');
			$structText = "<?php return array(\n".$tmp.");";
			fwrite($file, $structText);
			fclose($file);
		}
	}
}