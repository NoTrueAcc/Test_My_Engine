<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 14.09.17
 * Time: 14:18
 */

namespace library\database;


use core\database\AbstractDataBase;
use library\config\Config;

/**
 * Класс для создания подключения к БД
 *
 * Class DataBase
 * @package library\database
 */
class DataBase extends AbstractDataBase
{
	private static $db = null;

	/**
	 * Возвращает объект подключения к БД
	 *
	 * @return DataBase
	 */
	public static function getDBO()
	{
		if(is_null(self::$db))
		{
		self::$db = new DataBase(
								Config::DB_HOST,
								Config::DB_USER,
								Config::DB_PASSWORD,
								Config::DB_NAME,
								Config::DB_SYM_QUERY,
								Config::DB_PREFIX
								);
		}

		return self::$db;
	}
}