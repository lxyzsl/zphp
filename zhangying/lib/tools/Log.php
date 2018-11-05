<?php
namespace tools;
use ZPHP\Core\Config;
define('GAME_LOG_DIV_SIGN', date('Y-m-d'));
/**
 * 日志类
 * @author Micah
 * @version 20130906
 */
class Log
{
    // 写入缓冲的计数器达到此设定时，将强制刷入文件中
    const MAX_WRITE_BUFFER_ITEMS = 200;
    
    /**
     * 写入日志的缓冲
     * @var array
     */
    private static $_write_buffer = [];
    /**
     * 写入日至的缓冲计数器
     * @var unknown
     */
    private static $_write_buffer_counter = 0;
    
    /**
     * 写入缓冲中
     * @param unknown $file
     * @param string $content
     */
    private static function writeBuffer($file, $content = '')
    {
        // 若不存在，创建一个新块
        if (!isset(self::$_write_buffer[$file])) {
            self::$_write_buffer[$file] = '';
        }
        // 写入到块中
        self::$_write_buffer[$file] .= $content . "\r\n";
        // 标记缓冲计数器++，若达到
        if (++self::$_write_buffer_counter >= self::MAX_WRITE_BUFFER_ITEMS) {
            self::flushWriteBuffer();
        }
    }
    
    /**
     * 将缓冲刷入文件中
     */
    public static function flushWriteBuffer()
    {
        try {
            // 准备刷入文件中
            foreach (self::$_write_buffer as $file => $content) {
                // 写入指定的文件中
                error_log($content, 3, $file);
                // 清空已经写入到文件的缓冲（即使给定文件打开失败）
                self::$_write_buffer[$file] = '';
            }
        } catch (\Exception $e) {
            // 遭遇错误时，清空所有的写入缓冲
            self::$_write_buffer = [];
        }
        
        // 重置计数器
        self::$_write_buffer_counter = 0;
    }
    
    /**
     * 操作数据库的日志
     * @param unknown $err
     */
	public static function addDBLog($err)
	{
	    if (Config::get('log_level') < 2) return;
// 		$log_file = '1-db-' . GAME_LOG_DIV_SIGN . '.log';
// 		self::addLog($log_file,$err);
	}
	
	/**
	 * 异常的日志
	 * @param unknown $err
	 */
    public static function addErrorLog($err)
    {
        if (Config::get('log_level') < 1) return;
        $log_file = 'error-' . GAME_LOG_DIV_SIGN . '.log';
        self::addLog($log_file,$err);
    }
    
    /**
     * 调试的日志
     * @param unknown $msg
     */
    public static function addDebugLog($msg)
    {
        if (Config::get('log_level') < 2) return;
        $log_file = 'debug' . GAME_LOG_DIV_SIGN . '.log';
        self::addLog($log_file,$msg);
    }
    
    public static function addMethodLog($msg)
    {
    	if (Config::get('log_level') < 2) return;
    	$log_file = \common\Common::$servID.'_method_' . GAME_LOG_DIV_SIGN . '.log';
    	self::addLog($log_file,$msg);
    }
    /**
     * 标准方法
     * @param unknown $flname
     * @param unknown $msg
     */
    public static function addLog($flname, $msg)
    {
        $flname = dirname(__DIR__). '/logs/' . $flname;
        $msg = sprintf("%s(%s) %s\r\n",date("Y-m-d H:i:s "), 
                microtime(true), (is_string($msg) || is_integer($msg) ? $msg : var_export($msg, true)));
        self::writeBuffer($flname, $msg);
//         error_log($msg, 3, $flname);
    }	
}