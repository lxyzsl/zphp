<?php
namespace api\ctrl;

use enum\EnumGameObject;
use tools\Func;
use core\CtrlBase;
use enum\EnumBackStageAction;
use game\module\rolecounter\RoleCounter;
use intraface\ArenaWeekAward;
use enum\EnumCrossModule;
use game\module\battle\proxy\Scenes;
use ZPHP\Exception\Error;
use ZPHP\Exception\GameException;

class RoleCtrl extends CtrlBase
{
    /**
     * 获取角色信息
     * @param RoleID
     */
    public function getRole()
    {
        $role = \factory\Factory::getInstance($this->getBaseStruct(), EnumGameObject::ROLE, $this->RoleID);
        return $role->getInfo();
    }
}