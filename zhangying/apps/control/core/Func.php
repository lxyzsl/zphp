<?php
namespace game\core;

class Func
{
	public static function tree($directory)
	{
		$result = [];
		 
		$mydir = dir($directory);
		while($file = $mydir->read())
		{
			if ($file != '.' && $file != '..') {
				list($name, $p) = explode('.',$file);
				$result[] = 'game\service\\'.$name;
			}
		}
		$mydir->close();
		return $result;
	}
}