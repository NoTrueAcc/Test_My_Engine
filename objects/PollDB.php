<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 13:24
 */

namespace objects;


use library\database\ObjectDB;
use library\database\SelectDB;

/**
 * Класс для работы с таблицей polls
 *
 * Class PollDB
 * @package objects
 */
class PollDB extends ObjectDB
{
	protected static $table = 'polls';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('title', 'ValidateTitle');
		$this->addProperty('state', 'ValidateBoolean', null, 0);
	}

	/**
	 * Инициализирует случайный опрос
	 *
	 * @return bool
	 */
	public function loadRandom()
	{
		$select = new SelectDB();
		$select->from(self::$table, '*')
			->where('state = ' . self::$db->getSQ(), '1')
			->orderRand()
			->limit(1);

		$pollData = self::$db->selectRow($select);

		return $this->init($pollData);
	}
}