<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 13:38
 */

namespace objects;


use library\database\ObjectDB;
use library\database\SelectDB;

/**
 * Класс для работы с таблицей poll_voters
 *
 * Class PollVoterDB
 * @package objects
 */
class PollVoterDB extends ObjectDB
{
	protected static $table = 'poll_voters';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('pollDataId', 'ValidateId');
		$this->addProperty('ip', 'ValidateIP', self::TYPE_IP, $this->getIP());
		$this->addProperty('date', 'ValidateDate', self::TYPE_TIMESTAMP, $this->getDate());
	}

	/**
	 * Возвращает количество проголосовавших по айди
	 *
	 * @param int $pollDataId идентификатор варианта ответа
	 * @return string
	 */
	public static function getCountOnPollDataId($pollDataId)
	{
		return self::getCountOnField(self::$table, 'pollDataId', $pollDataId);
	}

	/**
	 * Проверяет голосовал ли участник по айпи
	 *
	 * @param array|string $pollDataIds массив вариантов ответа
	 * @return bool
	 */
	public static function isAlreadyPoll(array $pollDataIds)
	{
		$select = new SelectDB();
		$select->from(self::$table, '*')
			->whereIn('pollDataId', $pollDataIds)
			->where('`ip` = ' . self::$db->getSQ(), array(ip2long($_SERVER['REMOTE_ADDR'])))
			->limit(1);

		return self::$db->selectCell($select) ? true : false;
	}
}