<?php
namespace api\controller;
use control\core\ControllerBase;
use ZPHP\Controller\IController;
use ZPHP\Protocol\Request;

class login extends ControllerBase implements IController
{
    public function main()
    {
        echo 2222;
    }
//    public function loginDeal()
//    {
//        $params = $this->_server->getParams();
//        $login = new \control\module\login($params);
//        $result = $login->login();
//        //设置返回给模板的是Json数据
//        $this->_server->setViewMode('Json');
//        $this->_server->display($result);
//
//    }
//
//    public function logOut()
//    {
//        $params = $this->_server->getParams();
//        $logout = new \control\module\login($params);
//        $logout->logout();
//        $this->_server->setViewMode('Json');
//        $this->_server->display([]);
//    }
}