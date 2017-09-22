<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 9:52
 */

namespace objects;


use library\database\ObjectDB;
use library\database\SelectDB;

/**
 * Класс для работы с таблицей courses
 *
 * Class CourseDB
 * @package objects
 */
class CourseDB extends ObjectDB
{
	protected static $table = 'courses';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('type', 'ValidateCourseType');
		$this->addProperty('header', 'ValidateTitle');
		$this->addProperty('subHeader', 'ValidateTitle');
		$this->addProperty('img', 'ValidateImg');
		$this->addProperty('link', 'ValidateUrl');
		$this->addProperty('text', 'ValidateText');
		$this->addProperty('did', 'ValidateId');
		$this->addProperty('latest', 'ValidateBoolean');
		$this->addProperty('sectionIds', 'ValidateIds');
	}

	/**
	 * Инициализирует объект по айди секции
	 *
	 * @param int $sectionId айди секции
	 * @param string $type тип
	 */
	public function loadOnSectionId($sectionId, $type)
	{
		$whereType = '`type` = ' . self::$db->getSQ();

		$select = new SelectDB();
		$select->from(self::$table, array('*'))
			->where($whereType, array($type))
			->where('latest = ' . self::$db->getSQ(), array(1))
			->orderRand();
		$latestData = self::$db->select($select);

		$select = new SelectDB();
		$select->from(self::$table, array('*'))
			->where($whereType, array($type))
			->whereFieldInSet('sectionIds', $sectionId)
			->orderRand();
		$sectionData = self::$db->select($select);

		$data = array_merge($latestData, $sectionData);

		if(count($data) == 0)
		{
			$select = new SelectDB();
			$select->from(self::$db, '*')
				->where($whereType, array($type))
				->orderRand();

			$data = self::$db->select($select);
		}

		$objectsDataList = ObjectDB::buildMultiple(__CLASS__, $data);
		uasort($objectsDataList, array(__CLASS__, 'compare'));
		$first = array_shift($objectsDataList);
		$this->loadOnId($first->id);
	}

	/**
	 * Пост инициализация
	 *
	 * @return bool
	 */
	protected function postInit()
	{
		$this->img = isset($this->img) ? AbstractConfig::DIR_IMG . $this->img : null;

		return true;
	}

	/**
	 * Сравнивает значения и выдает то, которое в наибольшем приоритете
	 *
	 * @param ObjectDB $firstValue первое значение
	 * @param ObjectDB $secondValue второе значение
	 * @return bool
	 */
	private function compare($first, $second)
	{
		if($first->latest != $second->latest)
		{
			return $first->latest < $second->latest;
		}

		if($first->type == $second->type)
		{
			return 0;
		}

		return ($first->type > $second->type);
	}
}