<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace ZPHP\Protocol\Adapter;

use ZPHP\Core\Config;
use ZPHP\Protocol\Request;
use ZPHP\Protocol\IProtocol;
use ZPHP\Core;
use ZPHP\View;

class Cli implements IProtocol
{

    private $_action = 'index';
    private $_method = 'main';
    private $_params = array();
    private $_view_mode;
    /**
     * 会取$_SERVER['argv']最后一个参数
     * 原始格式： a=action&m=method&param1=val1
     * @param $_data
     * @return bool
     */
    public function parse($_data)
    {
        \parse_str(array_pop($_data), $data);
        $apn = Config::getField('project', 'ctrl_name', 'a');
        $mpn = Config::getField('project', 'method_name', 'm');
        if (isset($data[$apn])) {
            $this->_action = \str_replace('/', '\\', $data[$apn]);
        } else {
            $this->_action = Config::getField('project', 'init_apn', $this->_action);
        }
        if (isset($data[$mpn])) {
            $this->_method = $data[$mpn];
        }
        $this->_params = $data;
        Request::init($this->_action, $this->_method, $data, Config::getField('project', 'view_mode', 'String'));
        return true;
    }




}
