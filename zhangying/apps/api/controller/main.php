<?php
namespace game\controller;

use ZPHP\Controller\IController,
    service\Service;

class main implements IController
{
    /**
     * @var \ZPHP\Protocol\Adapter\Http
     */
    private $_server;
    
    public function setServer($server)
    {
        $this->_server = $server;
    }

    public function _before()
    {
        return true;
    }

    public function _after()
    {
    }

    public function main()
    {
    	if (isset($_GET['Debug'])) {
    		echo '<pre>';
    	}
        $params = $this->_server->getParams();
    	try {
			$service = new Service($params['stream']);
			$result = $service->opr();
		} catch (\Exception $e) {
			$result = array('isSucce'=>0,'error'=>'','aResult'=>'','cResult'=>'');
			$result['error'] = $e->__toString();
		}
		$this->_server->setViewMode('Json');
		$this->_server->display($result);
    }
}
