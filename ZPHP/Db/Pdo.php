<?php
namespace ZPHP\Db;

use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;
use tools\Func;
class Pdo
{
    /**
     * @var \ReflectionClass
     */
    public $_classRef = null;

    protected $pdo;
    private $_config = null;
    private $dbName;
    private $tableName;
    private $forUpdate = false;

    public function __construct($config)
    {
        $this->_config = $config;
        $this->link();
    }

    public function link()
    {
        if(empty($this->_config)) throw new GameException(Error::COMMON_LACK_PARAM);
        $this->pdo = new \PDO($this->_config['dns'], $this->_config['user'], $this->_config['pass'], array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->_config['charset']}';",
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ));

        $this->dbName = $this->_config['dbname'];
    }

    public function setForUpdate($forUpdate)
    {
        $this->forUpdate = $forUpdate;
    }

    /**
     * ReflectionClass
     */
    public function setClassRef($ref)
    {
        $this->_classRef = $ref;
    }

    public function getDBName()
    {
        return $this->dbName;
    }

    public function setDBName($dbName)
    {
        if (empty($dbName)) {
            return;
        }
        $this->dbName = $dbName;
    }

    public function getTableName()
    {
        $this->tableName = $this->_classRef->getConstant('TABLE_NAME');
        return $this->tableName;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getLibName()
    {
        return "`{$this->getDBName()}`.`{$this->getTableName()}`";
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }

    public function add($entity, $fields, $onDuplicate = null)
    {
        $strFields = '`' . implode('`,`', $fields) . '`';
        $strValues = ':' . implode(', :', $fields);

        $query = "INSERT INTO {$this->getLibName()} ({$strFields}) VALUES ({$strValues})";

        if (!empty($onDuplicate)) {
            $query .= 'ON DUPLICATE KEY UPDATE ' . $onDuplicate;
        }

        $statement = $this->pdo->prepare($query);
        $params = array();

        foreach ($fields as $field) {
            $params[$field] = $entity->$field;
        }

        \tools\Log::addDBLog($query);
        \tools\Log::addDBLog($params);
//         try {
        $statement->execute($params);
        if (!$statement instanceof \PDOStatement || $statement->errorCode() != \PDO::ERR_NONE) {
            throw new GameException(Error::COMMON_LACK_PARAM);
        }
//         } catch (\Exception $e) {
//         	throw new GameException(Error::DB_FATAL_ERROR);
//         }
        return $this->pdo->lastInsertId();
    }

    public function addMulti($entitys, $fields)
    {
        $items = array();
        $params = array();

        foreach ($entitys as $index => $entity) {
            $items[] = '(:' . implode($index . ', :', $fields) . $index . ')';

            foreach ($fields as $field) {
                $params[$field . $index] = $entity->$field;
            }
        }

        $query = "INSERT INTO {$this->getLibName()} (`" . implode('`,`', $fields) . "`) VALUES " . implode(',', $items);
        $statement = $this->pdo->prepare($query);
        return $statement->execute($params);
    }

    public function replace($entity, $fields)
    {
        $strFields = '`' . implode('`,`', $fields) . '`';
        $strValues = ':' . implode(', :', $fields);

        $query = "REPLACE INTO {$this->getLibName()} ({$strFields}) VALUES ({$strValues})";
        $statement = $this->pdo->prepare($query);
        $params = array();

        foreach ($fields as $field) {
            $params[$field] = $entity->$field;
        }
        $statement->execute($params);
        return $this->pdo->lastInsertId();
    }

    public function update($fields,  $params, $where, $change = false)
    {
        if ($change) {
            $updateFields = array_map(__CLASS__ . '::changeFieldMap', $fields);
        } else {
            $updateFields = array_map(__CLASS__ . '::updateFieldMap', $fields);
        }
        if (\is_object($params)) $params = Func::classToArray($params);
        $strUpdateFields = implode(',', $updateFields);
        $query = "UPDATE {$this->getLibName()} SET {$strUpdateFields} WHERE {$where}";
        $statement = $this->pdo->prepare($query);
        \tools\Log::addDBLog($query);
        \tools\Log::addDBLog($params);
//         try {
        $rs = $statement->execute($params);
        if (!$statement instanceof \PDOStatement || $statement->errorCode() != \PDO::ERR_NONE) {
            throw new GameException(Error::COMMON_LACK_PARAM);
        }
//         } catch (\Exception $e) {
//         	throw new GameException(Error::DB_FATAL_ERROR);
//         }
        return $rs;
    }

    public function fetchValue($where = '1', $params = null, $fields = '*')
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where} limit 1";
        if ($this->forUpdate) $query .= ' for update';
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return $statement->fetchColumn();
    }

    public function fetchArray($where = '1', $params = null, $fields = '*', $orderBy = null, $limit = null)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $query .= " limit {$limit}";
        }

        if ($this->forUpdate) $query .= ' for update';

        $statement = $this->pdo->prepare($query);
        \tools\Log::addDBLog($query);
        \tools\Log::addDBLog($params);
        $statement->execute($params);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        return $statement->fetchAll();
    }

    public function fetchCol($where = '1', $params = null, $fields = '*', $orderBy = null, $limit = null)
    {
        $results = $this->fetchArray($where, $params, $fields, $orderBy, $limit);
        return empty($results) ? array() : array_map('reset', $results);
    }

    public function fetchAll($where = '1', $params = null, $fields = '*', $orderBy = null, $limit = null)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        if ($limit) {
            $query .= " limit {$limit}";
        }

        if ($this->forUpdate) {
            $query .= ' for update';
        }

        $statement = $this->pdo->prepare($query);
        \tools\Log::addDBLog($query);
        \tools\Log::addDBLog($params);

        if (!$statement->execute($params)) {
            throw new GameException(Error::DB_CONNECTION_FAILED);
        }
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->_classRef->getName());
        return $statement->fetchAll();
    }

    public function fetchAllBySql($query, $params)
    {
        $statement = $this->pdo->prepare($query);
        \tools\Log::addDBLog($query);
        \tools\Log::addDBLog($params);
        if (!$statement->execute($params)) {
            throw new GameException(Error::DB_CONNECTION_FAILED);
        }

        $statement->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
        return $statement->fetchAll();
    }

    /**
     * 获取一条数据
     */
    public function fetchEntity($where = '1', $params = null, $fields = '*', $orderBy = null)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        $query .= " limit 1";

        if ($this->forUpdate) $query .= ' for update';
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->_classRef->getName());
        return $statement->fetch();
    }

    public function fetchCount($where = '1', $pk = "*")
    {
        $query = "SELECT count({$pk}) as count FROM {$this->getLibName()} WHERE {$where}";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetch();
        return $result["count"];
    }

    public function remove($where, $params = [])
    {
        if (empty($where)) {
            return false;
        }

        $query = "DELETE FROM {$this->getLibName()} WHERE {$where}";
        $statement = $this->pdo->prepare($query);
        \tools\Log::addDBLog($query);
        \tools\Log::addDBLog($params);
        return $statement->execute($params);
    }

    public function flush()
    {
        $query = "TRUNCATE {$this->getLibName()}";
        $statement = $this->pdo->prepare($query);
        return $statement->execute();
    }

    public static function updateFieldMap($field)
    {
        return '`' . $field . '`=:' . $field;
    }

    public static function changeFieldMap($field)
    {
        return '`' . $field . '`=`' . $field . '`+:' . $field;
    }

    public function fetchBySql($sql, $ref)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $result =  $statement->fetchObject();
        if (empty($result)) {
            return array();
        }
        return $result;
    }

    public function execute($sql)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return true;
    }

    /**
     * 获取数据表所有字段
     */
    public function tableAllStruct($tableName,$dbIP)
    {
        $connect = mysqli_connect($dbIP, $this->config['user'], $this->config['pass'],$this->getDBName());
        if(!$connect){
            throw new GameException(Error::DB_CONNECTION_FAILED);
        }
        $query = "select * from ".$tableName;
        $fieldList = [];
        if($result = mysqli_query($connect,$query)){
            $fields = mysqli_fetch_fields($result);
            foreach($fields as $val){
                $fieldList[] = array('Name'=>$val->name,'Type'=>$val->type);
            }
        }
        return $fieldList;
    }

    public function close()
    {
        $this->pdo = null;
    }
}


