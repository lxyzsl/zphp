<?php
namespace control\core;

class ModuleBase
{
    /**
     * @var \core\BaseStruct
     */
    protected $_baseStruct = null;
    protected $_params = null;
    protected $_stream = null;
    const STREAM = '{"loginID":0,"client":{"params":"","func":"%s"}}';
    
    public function __construct($params)
    {
        $this->_params = $params;
        $this->initBaseStruct();
    }
    
    public function initBaseStruct()
    {
        $gameData = new \store\GameData();
        $this->_stream = '{"loginID":0,"client":{"params":"","func":"%s"}}';
        $this->_stream = sprintf($this->_stream, 'a.b');
        $this->_stream = \tools\Func::decodeGameData($this->_stream);
        $this->_baseStruct = new \core\BaseStruct($this->_stream, $gameData);
    }

    
    /**
     * @param $name
     */
    public function __GET($name)
    {
        if (array_key_exists($name, $this->_params)) {
            return $this->_params[$name];
        }
    }
}