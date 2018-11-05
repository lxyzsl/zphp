<?php
namespace api\module\role;

use core\InfoConstBase;
class Constant extends  InfoConstBase
{
	const MODULE = 'Role';
	// -- table name
	const TABLE_NAME = 'role';
	// -- db primary
	const PRIMARY = 'RoleID';
	// redis db setting
	public static $One_Where = array('RoleID');
}