<?php
/**
 * 后台控制端入口
 */
namespace control\controller;
use control\core\ControllerBase;
use ZPHP\Controller\IController;

class main extends ControllerBase implements IController
{
    public function main()
    {
        echo 1;
//        $this->_server->setTplFile('index.php');
//        $this->_server->display(array('webSite'=>'后台首页'));
    }
}