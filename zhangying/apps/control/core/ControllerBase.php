<?php
namespace control\core;

use ZPHP\Protocol\Request;

class ControllerBase
{
    /**
     * @var \ZPHP\Protocol\Adapter\Http
     */
    protected $_server;

    public function setServer($server)
    {
        $this->_server = $server;
    }
    
    public function _before()
    {
        $this->checkIsLogin();
        return true;
    }

    public function _after()
    {
        return true;
    }
    
    public function checkIsLogin()
    {
        $params = Request::getParams();
		if (PHP_SAPI == 'cli') return;
        if ((!isset($_SESSION["isauth"]) || $_SESSION["isauth"]!=1)) {
        	if (!in_array($params['controller'], ['login','server','redis'])) {
        		header('Location: http://'.$_SERVER["HTTP_HOST"].'/control/login/main');
        	}
        }
    }
}