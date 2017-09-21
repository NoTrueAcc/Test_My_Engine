<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 9:09
 */

namespace autoloader;


class Autoloader
{
	public static function register()
	{
		spl_autoload_register(array('\autoloader\Autoloader', 'autoload'), true, true);
	}

	public static function autoload($className)
	{
		static $includePaths = null;

		if (is_null($includePaths))
		{
			$includePaths = explode(PATH_SEPARATOR, get_include_path());
		}

		$file = str_replace(array('_', '\\'), '/', $className) . '.php';
		$file = preg_replace('/^[\/]/', '', $file);

		foreach ($includePaths as $includePath)
		{
			$includePath = rtrim($includePath, '/').'/';
			if (file_exists($includePath . $file))
			{
				include_once $includePath . $file;
			}
		}

		return;
	}
}