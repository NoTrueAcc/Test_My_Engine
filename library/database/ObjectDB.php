<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 20.09.17
 * Time: 11:41
 */

namespace library\database;


use core\database\AbstractObjectDB;
use library\config\Config;

/**
 * Класс для работы с объектами
 *
 * Class ObjectDB
 * @package library\database
 */
class ObjectDB extends AbstractObjectDB
{
	private static $months = array('янв', 'фев', 'март', 'апр', 'май', 'июнь', 'июль', 'авг', 'сент', 'окт', 'ноя', 'дек');

	public function __construct($table)
	{
		parent::__construct($table, Config::FORMAT_DATE);
	}

	/**
	 * Возвращает краткое название месяца по дате
	 *
	 * @param bool string|int $date дата
	 * @return mixed
	 */
	public static function getMonth($date = false)
	{
		$date = $date ? strtotime($date) : time();

		return self::$months[date('n', $date) - 1];
	}
}