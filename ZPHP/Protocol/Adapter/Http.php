<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace ZPHP\Protocol\Adapter;

use ZPHP\Core\Config;
use ZPHP\Protocol\IProtocol;
use ZPHP\Common\Route as ZRoute;
use ZPHP\Protocol\Request;
use ZPHP\View;


class Http implements IProtocol
{
    private $_action = 'main\\main';
    private $_method = 'main';
    private $_params = array();
    private $_view_mode = '';
    private $_tpl_file = '';
    /**
     * 直接 parse $_REQUEST
     * @param $_data
     * @return bool
     */
    public function parse($data)
    {
        $apn = Config::getField('project', 'ctrl_name', 'controller');
        $mpn = Config::getField('project', 'method_name', 'action');
        if (isset($data[$apn])) {
            $this->_action = \str_replace('/', '\\', $data[$apn]);
        } else {
            $this->_action = Config::getField('project', 'init_apn', $this->_action);
        }
        if (isset($data[$mpn])) {
            $this->_method = $data[$mpn];
        }

        $pathInfo = Request::getPathInfo();
        if (!empty($pathInfo) && '/' !== $pathInfo) {
            $routeMap = ZRoute::match(Config::get('route', false), $pathInfo);
            if (is_array($routeMap)) {
                $this->_action = \str_replace('/', '\\', $routeMap[0]);
                $this->_method = $routeMap[1];
                if (!empty($routeMap[2]) && is_array($routeMap[2])) {
                    //参数优先
                    $this->_params = $data + $routeMap[2];
                }
            }
        }else{
            $this->_params = $data;
        }
        Request::init($this->_action, $this->_method, $this->_params, Config::getField('project', 'view_mode', 'Php'));
        return true;
    }


    public function getParamsByName($name)
    {
        $result = '';
        if (isset($this->_params[$name])) {
            $result = $this->_params[$name];
        }

        return $result;
    }


}
