<?php
namespace store;

class Pdo extends \ZPHP\Db\Pdo
{
	/**
	 * 单列获取
	 */
	public function getInfo($info, $fields = '*', $orderby = null)
	{
		$where = '1';
		$params = array();
		$oneWhere = $this->_classRef->getStaticPropertyValue('One_Where');
		foreach ($oneWhere as $val) {
			$where .= ' and `'.$val.'`= :'.$val;
			$params[$val] = $info->$val;
		}
		return $this->fetchEntity($where, $params, $fields, $orderby);
	}
	
	/**
	 * 单列修改
	 */
	public function updateInfo($changeInfo, $info)
	{
		$where = 1;
		$params = array();
		$oneWhere = $this->_classRef->getStaticPropertyValue('One_Where');
		foreach ($oneWhere as $val) {
			$where .= ' and `'.$val.'`= :'.$val;
			$params[$val] = $info->$val;
		}
		foreach ($changeInfo as $val) {
			$params[$val] = $info->$val;
		}
		return $this->update($changeInfo, $params, $where);
	}
	
	/**
	 * 单列添加
	 */
	public function addInfo($info)
	{
		$id = $this->add($info, \tools\Func::getClassFields($info));
		return $id;
	}
	
	/**
	 * 单列移除
	 */
	public function removeInfo($info)
	{
		$where = 1;
		$params = array();
		$oneWhere = $this->_classRef->getStaticPropertyValue('One_Where');
		foreach ($oneWhere as $val) {
// 			if (empty($info->$val)) return null;
			$where .= ' and `'.$val.'`= :'.$val;
			$params[$val] = $info->$val;
		}
		return $this->remove($where, $params);
	}
	
	// -- -- //
	
	public function getList($info, $listWhere = null)
	{
		$where = 1;
		$params = array();
		
		if (is_null($listWhere)) {
			$listWhere = $this->_classRef->getStaticPropertyValue('List_Where');
		}
		
		foreach ($listWhere as $val) {
			$where .= ' and `'.$val.'`= :'.$val;
			$params[':'.$val] = $info[$val];
		}
		return $this->fetchAll($where, $params);
	}
	
	public function getSysList($classRef)
	{
		$where = 1;
		$this->_classRef = $classRef;
		$fields = $this->_classRef->getStaticPropertyValue('fields');
		$fields = empty($fields) ? '*' : '`'.implode('`,`', $fields).'`';
		if ($this->_classRef->hasConstant('ENABLED_SHOW') && $this->_classRef->getConstant('ENABLED_SHOW') == 1) {
			$enbaled = $this->_classRef->getConstant('ENABLED_SHOW');
			$where .= ' and Enabled = '.$enbaled.'';
		}
		
		if ($this->_classRef->hasProperty('Where_Key')) {
			$whereKey = $this->_classRef->getStaticPropertyValue('Where_Key');
			foreach ($whereKey as $key=>$val) {
				$where .= " and $key = '$val'";
			}
		}
		
		return $this->fetchAll($where, null, $fields);
	}
	
	public function getListBySql($params, $where, $fields = '*', $orderby=null, $limit=null)
	{
		return $this->fetchAll($where, $params, $fields, $orderby, $limit);
	}
	// ---- 事务 ---- //
	/**
	 * 事件记数器
	 * 在开始事务时,值为1时才做实际的开始事务操作
	 * 在提交或回滚事务时,值为0时才做实际的操作
	 *
	 * @var mixed
	 */
	private $_pdoTransTally = 0;
	public function transTally()
	{
		return $this->_pdoTransTally;
	}
	/**
	 * 事务的版本号(开始事务的次数)
	 * 一些锁定的内容,可根据这个版本号判断是否需要重新锁定
	 * @var mixed
	 */
	private $_pdoTransVersion = 0;
	public function transVersion()
	{
		return $this->_pdoTransVersion;
	}
	public function inTransaction()
	{
		return $this->_pdoTransTally>0;
	}
	public function beginTransaction()
	{
		$this->_pdoTransTally ++;
		
		if($this->_pdoTransTally == 1) {
			$this->_pdoTransVersion++;
			$this->pdo->beginTransaction();
		}
	}
	public function commitTransaction()
	{
		$this->_pdoTransTally--;
		if($this->_pdoTransTally == 0) {
			if ($this->pdo->commit() === false) {
				$this->_pdoTransTally++;
				throw new \Exception(1, null, true);
			}
		}
	}
	public function rollbackTransaction()
	{
		$this->_pdoTransTally--;
		if($this->_pdoTransTally == 0) {
			if ($this->pdo->rollBack() === false) {
				$this->_pdoTransTally++;
				throw new \Exception(1, null, true);
			}
		}
	}
}