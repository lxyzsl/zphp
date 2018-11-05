<?php 
namespace tools;

use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;
use enum\EnumGameObject;

class Func
{
	/**
	 * JSON_DECODE
	 */
	public static function decodeGameData($data,$mode = false)
	{
		if (empty($data)) {
			if (!$mode) {
				$result = new \stdClass();
			} else {
				$result = array();
			}
		} else {
// 			if (is_array($data)) {
// 				throw new GameException(Error::GAME_MODULE_CLOSE);
// 			}
			$result = json_decode($data, $mode);
		}
		return $result;
	}
	
	/**
	 * JSON_ENCODE
	 */
	public static function encodeGameData($data, $mode = JSON_UNESCAPED_UNICODE)
	{
		return empty($data) ? '' : urldecode(json_encode($data, $mode));
	}
	
	private static $cacheClassFields = array();
	
	private static function filterNonEnumValue($item)
	{
		return is_string($item) || is_numeric($item);
	}
	/**
	 * 根据对象或对象名获得枚举列表
	 * @param $object
	 */
	private static $enumsCache = array();
	public static function getAsEnum($object)
	{
		$objName = is_object($object) ? get_class($object) : $object;
		if (!array_key_exists($objName, self::$enumsCache)) {
			$ref = null;
			if (is_object($object)) {
				$ref = new \ReflectionObject($object);
			} else if (is_string($object) && class_exists($object)) {
				$ref = new \ReflectionClass($object);
			}
	
			$enums = array();
			if (!is_null($ref)) {
				$enums = $ref->getConstants();
				$enums = array_filter($enums, array(__CLASS__, 'FilterNonEnumValue'));
			}
	
			self::$enumsCache[$objName] = array_values($enums);
		}
		return self::$enumsCache[$objName];
	}
	
	public static function getGameObjectList()
	{
		$ref = new \ReflectionObject(new EnumGameObject());
		$enums = $ref->getStaticProperties();
		foreach ($enums as $val) {
			if ($val['Type'] == 'SYS') {
				$result[] = $val['Name'];
			}
		}
		sort($result);
		return $result;
	}
	
	public static function getClassFields($class,$useCachedFields=true)
	{
		if ($class==false) {
			throw new GameException(Error::COMMON_PARAM_ERROR);
		}
		$className = get_class($class);
		$fields = array();
		
		if($useCachedFields && $className != 'stdClass' &&
		array_key_exists($className,self::$cacheClassFields)) {
			$fields = self::$cacheClassFields[$className];
		} else {
			$reflector = new \ReflectionObject($class);
		
			$properties = $reflector->getProperties(\ReflectionProperty::IS_PUBLIC);
			foreach ($properties as $fr) {
				$field_name = $fr->getName();
				if($field_name == "_explicitType" || $field_name == '_primaryKey') continue;
				if ($field_name == "One_Where" || $field_name == "List_Where") continue;
				$fields[$field_name] = $field_name;
			}
		
			if($useCachedFields) {
				self::$cacheClassFields[$className] = $fields;
			}
		}
		
		return $fields;
	}
	
	public static function arrayToClass($data)
	{
		if (empty($data)) return null;
		$class = new \stdClass();
		foreach($data as $key=>$field)
		{
			$class->$key = $field;
		}
		return $class;
	}
	
    public static function classToArray($class)
    {
        $arr = array();
        foreach($class as $key=>$field)
        {
            $arr[$key] = $field;
        }
        return $arr;
    }
	
    public static function classToClass($sourceClass, $targetClass)
    {
    	$fileds = self::getClassFields($sourceClass);
    	foreach ($fileds as $filed) {
    		if (isset($targetClass->$filed)) {
    			$sourceClass->$filed = $targetClass->$filed;
    		}
    	}
    	
    	return $sourceClass;
    }
    
	public static function createWhere($whereFields, $info)
	{
		if (\is_array($info)) {
			$info = \tools\Func::arrayToClass($info);
		}
		
		if (!\is_object($info)) {
			throw new \Exception('create where need object');
		}
		
		$where = '1';
		foreach ($whereFields as $fields) {
			$where .= ' and '.$fields.' = \''.$info->$fields.'\'';
		}
		return $where;
	}
	
	public static function createUpdateParams($changeInfo, $info)
	{
		$result = array();
		foreach ($changeInfo as $key) {
			$result[$key] = $info->$key;
		}
		return $result;
	}
	
	/**
	 * 读取结构中选中字段
	 */
	public static function getNewStructByFields($class, $selectedFields)
	{
		if (!is_array($selectedFields) || count($selectedFields) < 0)
			throw new GameException(Error::COMMON_PARAM_ERROR);
		$std = new \stdClass();
		foreach ($selectedFields as $fields) {
			$std->$fields = $class->$fields;
		}
		return $std;
	}
	
	/**
	 * 时间转换到时间戳
	 * @param unknown_type $time 如$time = "2359"11点59分 或$time = "092102" 9点21分2秒
	 */
	public static function timeTosysTime($time = 0){
		switch (strlen($time)){
			case 4:
				if ($time<0||$time>2359)$time = "";
				break;
			case 6:
				if ($time<0||$time>235959)$time = "";
				break;
			default:
				$time = "";
				break;
		}
		return strtotime(date("Ymd",time()).$time);
	}
	
	// The purpose is to determine the type of a certain method that exi
	public static function isClassMethod($type="public", $class, $method) {
		$refl = new \ReflectionClass($class);
		switch ($type) {
			case "static":
				if ($refl->hasMethod($method) && $refl->getMethod($method)->isStatic()) {
					return true;
				}
				break;
			case "public":
				if ($refl->hasMethod($method) && $refl->getMethod($method)->isPublic()) {
					return true;
				}
				break;
			case "private":
				if ($refl->hasMethod($method) && $refl->getMethod($method)->isPrivate()) {
					return true;
				}
				break;
			case "protected":
				if ($refl->hasMethod($method) && $refl->getMethod($method)->isProtected()) {
					return true;
				}
				break;
		}
		return false;
	}
	
	public static function initDbConfig($dbConfig)
	{
		list($ip,$username,$pass,$dbname) = explode(':', $dbConfig);
		$config = \ZPHP\Core\Config::get('pdo');
		$config['name'] = sprintf($config['name'], $dbname);
		$config['dns'] = sprintf($config['dns'], $ip);
		$config['user'] = sprintf($config['user'], $username);
		$config['pass'] = sprintf($config['pass'], $pass);
		$config['dbname'] = sprintf($config['dbname'], $dbname);
		return $config;
	}
	
	public static function getWeeksKey($time, $month = '')
	{
		if(!$month) $month = date('W');
		if (date('m') == 1 && $month > 50) {
			$y = date('Y') - 1;
		}else if($month == 0){
			$y = date('Y') - 1;
			$month = date('W', strtotime("-7 day", $time));
		}else {
			if($month == 0) $y = date('Y') - 1;
			$y = date('Y');
		}
		if ($month < 10) $month = '0'.intval($month);
		return sprintf('%s-%s', $y, $month);
	}
	
	public static function getNextRefreshTime($now, $time)
	{
		$time = Func::timeTosysTime($time);
		if ($now > $time) {
			$time = strtotime("+1 days", $time);
		}
		return $time;
	}
	
	public static function getNextRefreshTimeByList($now, array $timeList)
	{
		$j = 0;
		for($i = 1;$i<=count($timeList);$i++) {
			$time = strtotime("+$j days", Func::timeTosysTime($timeList[$i-1]));
			if ($now > $time) {
				if ($i==count($timeList)) {
					$i = 0;
					$j++;
				}
				continue;
			}
			$nextRefreshTime = $time;
			break;
		}
	
		return $nextRefreshTime;
	}
}
