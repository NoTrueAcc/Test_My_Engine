<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 14:10
 */

namespace objects;


use library\database\ObjectDB;
use library\database\SelectDB;

/**
 * Класс для работы с таблицей quotes
 *
 * Class QuoteDB
 * @package objects
 */
class QuoteDB extends ObjectDB
{
	protected static $table = 'quotes';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('author', 'ValidateTitle');
		$this->addProperty('text', 'ValidateSmallText');
	}

	/**
	 * Инициализирует объект цитат
	 *
	 * @return bool
	 */
	public function loadRandom()
	{
		$select = new SelectDB();
		$select->from(self::$table, '*')
			->orderRand()
			->limit(1);

		$quoteData = self::$db->selectRow($select);

		return $this->init($quoteData);
	}
}