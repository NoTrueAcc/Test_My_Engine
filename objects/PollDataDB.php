<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 13:31
 */

namespace objects;


use library\database\ObjectDB;

/**
 * Класс для работы с таблицей poll_data
 *
 * Class PollDataDB
 * @package objects
 */
class PollDataDB extends ObjectDB
{
	protected static $table = 'poll_data';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('pollId', 'ValidateId');
		$this->addProperty('title', 'ValidateTitle');
	}

	/**
	 * Возвращает все варианты ответов определенного опроса
	 *
	 * @param int $pollId айди опроса
	 * @return array массив объектов вариантов ответа
	 */
	public static function getAllOnPollId($pollId)
	{
		return self::getAllOnField(self::$table, __CLASS__, 'pollId', $pollId, 'id');
	}

	/**
	 * Возвращает отсортированные варианты ответа по количеству проголосовавших
	 *
	 * @param int $pollId айди опроса
	 * @return array массив объектов вариантов ответа
	 */
	public static function getAllSortDataByVotersOnPollId($pollId)
	{
		$pollDataList = self::getAllOnPollId($pollId);

		foreach ($pollDataList as $pollData)
		{
			$pollData->voters = PollVoterDB::getCountOnPollDataId($pollData->id);
		}

		uasort($pollDataList, array(__CLASS__, 'compare'));

		return $pollDataList;
	}

	/**
	 * Сортирует данные по количеству проголосовавших
	 *
	 * @param ObjectDB $first первое значение
	 * @param ObjectDB $second второе значение
	 * @return bool
	 */
	public static function compare($first, $second)
	{
		return $first->voters < $second->voters;
	}
}