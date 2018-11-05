<?php
namespace ZPHP\Exception;
use ZPHP\Cache\Adapter\Apc;

class AGameException extends \ErrorException
{
	// -- constants -- //

	// 日志记录样式
	const LOG_PATTERN = "\r\n#GEStart\r\nError Code: %d @ [%s]\r\nError Msg: %s\r\nTrace Detail:\r\n%s\r\n#GEEnd\r\n";
	// trace记录过滤样式
	const FILTER_TRACE_PATTERN = '/((?:.*)(?:^internal\sfunction)*(?:internal\sfunction)[^\n]*)(.*)/s';

	// -- static methods -- //
	/**
	 * 过滤trace记录
	 * @param $str
	 */
	final protected static function filterTraceString($str = '')
	{
		return preg_replace(self::FILTER_TRACE_PATTERN, '\1', $str);
	}

	// -- properties -- //
	protected static $errorMsg;
	// Trace String
	protected $traceString;
	// Log's Filename
	protected $fileName;
}


class GameException extends AGameException
{
	const MODULE = 'Error';

	public function __construct($code)
	{
		$this->code = $code;
		$this->traceString = $this->getTraceAsString();
		parent::__construct($this->message,$this->code);
	}

	public function __toString()
	{
		$struct = new Struct();
		$struct->Module = static::MODULE;
		$struct->Code = $this->code;
		$struct->Message = '';
		$struct->IsShow = 0;
		$struct->Description = $this->getTraceAsString();
		return $struct;
	}
}

class Struct
{
	public $Module = '';
	public $Code = '';
	public $Message = '';
	public $IsShow = 0;
	public $Description = '';
}