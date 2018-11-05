<?php

namespace store;

use enum\EnumGameObject;
use ZPHP\Exception\GameException;
class GameData
{
	private $_module = '';		// 链接模式 Game游戏服，Cross跨服
	/**
	 * @var \store\Pdo
	 */
	private $_mysql;
	public function __construct()
	{
		$this->initStore();
	}
	
	public function __destruct()
	{
		unset($this->_mysql);
	}
	
	public function setTableName($tableName)
	{
		$this->_mysql->setTableName($tableName);
	}
	
	public function setClassRef($classRef)
	{
		$this->_mysql->setClassRef($classRef);
	}
	
	/** 
	 * 初始化动态数据加载
	 */
	public function initStore()
	{
		$ip = \ZPHP\Core\Config::get('db')['host'] ? \ZPHP\Core\Config::get('db')['host'] :getenv('MYSQL_SERVER');
		$username = \ZPHP\Core\Config::get('db')['DBUser'] ? \ZPHP\Core\Config::get('db')['DBUser'] :  getenv('MYSQL_USERNAME');
		$pass = \ZPHP\Core\Config::get('db')['DBPass'] ? \ZPHP\Core\Config::get('db')['DBPass'] : getenv('MYSQL_PASSWORD') ;
		$dbname = \ZPHP\Core\Config::get('db')['DBName'] ?  \ZPHP\Core\Config::get('db')['DBName'] : getenv('MYSQL_DB_NAME');
		$dbport = \ZPHP\Core\Config::get('db')['DBPort'] ? \ZPHP\Core\Config::get('db')['DBPort'] : getenv('MYSQL_DB_PORT');
		$config['name'] =  $dbname;
		$config['dns'] = 'mysql:host='.$ip.';port='.$dbport;
		$config['user'] = $username;
		$config['pass'] =  $pass;
		$config['dbname'] = $dbname;
		$config['charset'] = 'UTF8';
		$this->_mysql = new \store\Pdo($config);
	}
	
	// -- module control begin -- //
	
	/**
	 * init data
	 */
	public function initData($classRef, $info, $forUpdate = false)
	{
		$this->_mysql->setClassRef($classRef);
		$this->_mysql->setForUpdate($forUpdate);
		return $this->_mysql->getInfo($info);
	}
	
	/**
	 * update
	 */
	public function update($classRef, $changeInfo, $info)
	{
		$this->_mysql->setClassRef($classRef);
		return $this->_mysql->updateInfo($changeInfo, $info);
	}
	
	/**
	 * insert
	 */
	public function add($classRef, $info)
	{
		$this->_mysql->setClassRef($classRef);
		return $this->_mysql->addInfo($info);
	}
	
	/**
	 * delete
	 */
	public function remove($classRef, $info)
	{
		$this->_mysql->setClassRef($classRef);
		return $this->_mysql->removeInfo($info);
	}
	
	// -- module control end -- //
	
	public function getList($classRef, $params, $listWhere=null)
	{
		$this->_mysql->setClassRef($classRef);
		return $this->_mysql->getList($params, $listWhere);
	}
	
	public function getListBySql($classRef, $params, $where, $fields = '*', $orderby = null, $limit = null)
	{
		$this->_mysql->setClassRef($classRef);
		return $this->_mysql->getListBySql($params, $where, $fields, $orderby, $limit);
	}
	
	public function getStruct($dbName, $table)
	{
		$sql = "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, EXTRA, COLUMN_COMMENT, NUMERIC_PRECISION FROM information_schema.COLUMNS
			where TABLE_SCHEMA='%s' and TABLE_NAME='%s' ORDER BY ORDINAL_POSITION;";
		$sql = sprintf($sql, $dbName, $table);
		return $this->_mysql->fetchAllBySql($sql, []);
	}
	
	
	public function fetchAllBySql($query, $params)
	{
		return $this->_mysql->fetchAllBySql($query, $params);
	}
	
	public function getDBName()
	{
		return $this->_mysql->getDBName();
	}
	
	public function refreshSql()
	{
		return $this->_mysql->fetchBySql("select NOW()", 1);
	}
	
	public function execute($sql)
	{
		return $this->_mysql->execute($sql);
	}
	
	public function tableAllStruct($tableName,$dbIP)
	{
		return $this->_mysql->tableAllStruct($tableName,$dbIP);
	}
	
	public function close()
	{
		$this->_mysql->close();
	}
	// ------------------事务------------------ //
	public function transVersion() {
    	return $this->_mysql->transVersion();
    }
	
	public function beginTran()
	{
		$this->_mysql->beginTransaction();
	}
	public function commitTran()
	{
		$this->_mysql->commitTransaction();
		
	}
	public function rollTran()
	{
//		\game\module\backapi\Cache::clear(false);
//		\game\module\clientthrift\Cache::clear(false);
		$this->_mysql->rollbackTransaction();
	}
}