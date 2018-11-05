<?php
/**
 * 角色
 * @author yunyi
 */
namespace api\module\role;

use \core\InfoBase;
use enum\EnumGameObject;
use tools\Func;
use ZPHP\Exception\GameException;
use ZPHP\Exception\Error;

 class Role extends InfoBase
{
	public function __construct($baseStruct, array $params, $forUpdate = false)
	{
		parent::__construct($baseStruct, new Struct());
		$this->setForUpdate($forUpdate);
		$this->RoleID = $params[0];
		$this->setInfo();
	}

}