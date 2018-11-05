<?php
/**
 * 角色接口
 * 
 * 角色创建接口 createRole params:{"userID":3,"name":"啊啊","sex":"F","factionID":1,"avatarID":1}
 * 获取角色信息 getRole params:{"roleID"}
 * 
 * @author yunyi
 */
namespace api\service;

use ctrl\RoleCtrl;
use core\ServiceBase;

class RoleService extends ServiceBase
{
	const CTRL_NAME = 'RoleCtrl';

	
	public function getRole()
	{
		$this->aResult = $this->_ctrl->getRole();
	}
	

}