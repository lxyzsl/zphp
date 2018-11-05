<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */
namespace ZPHP\Manager;

class Apc
{
    private static $instances;

    public static function getInstance()
    {
        if (empty(self::$instances)) {
            $apc = new \ZPHP\Cache\Adapter\Apc();
            self::$instances = $apc;
        }
        return self::$instances;
    }

}