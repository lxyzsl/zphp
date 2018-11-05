<?php
namespace ctrl\main;
use ZPHP\Controller\IController,
    ZPHP\Core\Config,
    ZPHP\View;
use ZPHP\Protocol\Request;
use control\core\Control;
use control\core\ControllerBase;
use control\core\ShowTable;
use tools\Func;

class main extends ControllerBase implements IController
{

    public function main()
    {
        $this->_server->setTplFile('index.php');
        $this->_server->display(array('webSite'=>'后台首页'));
    }
}

