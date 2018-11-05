<?php
namespace store;

class initStore
{
	public static function createRedis()
	{
		$enumList = \tools\Func::getGameObjectList();
		$sysData = \factory\SysDataFactory::getInstance();
		$sysData->redisRefactoring($enumList);
		return true;
	}
	
	public static function createRedisByType($type)
	{
		$sysData = \factory\SysDataFactory::getInstance();
		return $sysData->redisRefactoringByOne($type);
	}
	
	public static function getSysList($classRef)
	{
		$sysData = \factory\SysDataFactory::getInstance();
		return $sysData->getSysList($classRef);
	}
}