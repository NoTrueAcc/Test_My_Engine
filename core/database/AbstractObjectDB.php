<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 14.09.17
 * Time: 11:03
 */

namespace core\database;
use library\config\Config;

/**
 * Класс для создания объектов и работы с ними
 *
 * Class AbstractObjectDB
 * @package core\database
 */
class AbstractObjectDB
{
	const TYPE_TIMESTAMP = 1;
	const TYPE_IP = 2;

	const ACTION_UPDATE = 1;
	const ACTION_INSERT = 2;

	private static $types = array(self::TYPE_TIMESTAMP, self::TYPE_IP);

	/**
	 * Объект класса DataBase
	 *
	 * @var AbstractDataBase
	 */
	protected static $db = null;

	private $formatDate = '';
	private $id = null;
	private $properties = array();
	protected static $table = '';

	public function __construct($table, $formatDate)
	{
		self::$table = $table;
		$this->formatDate = $formatDate;
	}

	/**
	 * Подключение к БД
	 *
	 * @param AbstractDataBase $db
	 */
	public static function setDB($db)
	{
		self::$db = $db;
	}

	/**
	 * Загружает свойства объекта по id и инициализирует объект
	 *
	 * @param int $id идентификатор
	 * @return bool
	 */
	public function loadOnId($id)
	{
		$id = (int) $id;

		if($id < 0)
		{
			return false;
		}

		$select = new SelectDB(self::$db);
		$select->from(self::$table, $this->getSelectFields())
			->where("`id` = " . self::$db->getSQ(), array($id));

		if(!($propertiesData = self::$db->selectRow($select)))
		{
			return false;
		}

		if($this->init($propertiesData))
		{
			$this->postLoad();
		}
	}

	/**
	 * Инициализирует значения свойств объекта
	 *
	 * @param $propertiesData
	 * @return bool
	 */
	public function init($propertiesData)
	{
		foreach ($this->properties as $property => $data)
		{
			$value = isset($propertiesData[$property]) ? $propertiesData[$property] : null;

			switch ($data['type'])
			{
				case self::TYPE_TIMESTAMP :
					$value = is_null($value) ? null : date($this->formatDate, $value);
					break;
				case self::TYPE_IP :
					$value = is_null($value) ? null : long2ip($value);
					break;
			}

			$this->properties[$property]['value'] = $value;
		}

		$this->id = isset($propertiesData['id']) ? $propertiesData['id'] : null;

		return $this->postInit();
	}

	/**
	 * Добалвяет или изменяет записи в БД свойствами объекта
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function save()
	{
		$action = $this->isSaved() ? self::ACTION_UPDATE : self::ACTION_INSERT;
		$commit = ($action == self::ACTION_UPDATE) ? $this->preUpdate() : $this->preInsert();

		if(!$commit)
		{
			return false;
		}

		$propertiesData = array();

		foreach ($this->properties as $property => $data)
		{
			switch ($data['type'])
			{
				case self::TYPE_TIMESTAMP :
					$data['value'] = strtotime($data['value']);
					break;
				case self::TYPE_IP :
					$data['value'] = ip2long($data['value']);
					break;
			}

			$propertiesData[$property] = $data['value'];
		}

		if(!is_null($propertiesData))
		{
			if($action == self::ACTION_UPDATE)
			{
				$success = self::$db->update(self::$table, $propertiesData, '`id` = ' . self::$db->getSQ(), array($this->getId()));

				if(!$success)
				{
					throw new \Exception();
				}

				return $this->postUpdate();
			}
			elseif ($action == self::ACTION_INSERT)
			{
				$success = self::$db->insert(self::$table, $propertiesData);

				if(!$success)
				{
					throw new \Exception();
				}

				return $this->postInsert();
			}
			else
			{
				throw new \Exception();
			}
		}

		return false;
	}

	/**
	 * Удаляет запись из БД
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function delete()
	{
		if(!$this->isSaved() || !$this->preDelete())
		{
			return false;
		}

		$success = self::$db->delete(self::$table, '`id` = ' . self::$db->getSQ(), array($this->getId()));

		if(!$success)
		{
			throw new \Exception();
		}

		$this->removeId();

		return $this->postDelete();
	}

	/**
	 * Создает несколько объектов с переданными свойствами
	 *
	 * @param string $className имя класса
	 * @param array $propertiesDataList массив свойств
	 * @return array массив_объектов
	 * @throws \Exception
	 */
	public static function buildMultiple($className, array $propertiesDataList)
	{
		if(!class_exists($className))
		{
			throw new \Exception();
		}

		$resultObjectsDataList = array();

		foreach ($propertiesDataList as $propertiesData)
		{
			$obj = new $className;

			if(!($obj instanceof AbstractObjectDB))
			{
				throw new \Exception();
			}

			$obj->init($propertiesData);
			$resultObjectsDataList[$obj->id] = $obj;
		}

		return $resultObjectsDataList;
	}

	/**
	 * Создает объекты запроса по всем полям
	 *
	 * @param bool $limit лимит
	 * @param bool $offset смещение
	 * @return array массив объектов
	 */
	public static function getAll($limit = false, $offset = false)
	{
		$class = get_called_class();

		return self::getAllWithOrder($class::$table, $class, 'id', true, $limit, $offset);
	}

	/**
	 * Создает объекты по значению поля
	 *
	 * @param string $table таблица
	 * @param string $class имя класса
	 * @param string $field поле
	 * @param string $value значение
	 * @param string $order поле сортировки
	 * @param bool $asc тип сортировки
	 * @param int $limit лимит
	 * @param int $offset смещение
	 * @return array массив объектов
	 */
	public static function getAllOnField($table, $class, $field, $value, $order = false, $asc = true, $limit = false, $offset = false)
	{
		$where = "`$field` = " . self::$db->getSQ();
		$value = is_array($value) ? $value : array($value);

		return self::getAllOnWhere($table, $class, $where, $value, $order, $asc, $limit, $offset);
	}

	/**
	 * Создает объекты по id
	 *
	 * @param array $ids массив id
	 * @return array массив объектов
	 */
	public static function getAllOnIds(array $ids)
	{
		return self::getAllOnFieldValues('id', $ids);
	}

	/**
	 * Создает объеты по значениям поля
	 *
	 * @param string $field поле
	 * @param array $values массив значений
	 * @return array массив объектов
	 */
	public static function getAllOnFieldValues($field, array $values)
	{
		$class = get_called_class();

		$select = new SelectDB(self::$db);
		$select->from($class::$table, '*')
			->whereIn($field, $values);

		$propertiesDataList = self::$db->select($select);

		return AbstractObjectDB::buildMultiple($class, $propertiesDataList);
	}

	/**
	 * Возвращает даты в определенном формате
	 *
	 * @param int $date дата
	 * @return string дата в определенном формате
	 */
	public function getDate($date = false)
	{
		$date = $date ? $date : time();

		return date($this->formatDate, $date);
	}

	/**
	 * Возвращает количество записей
	 *
	 * @return int количество записей
	 */
	public static function getCount()
	{
		$class = get_called_class();

		return self::getCountOnWhere($class::$table);
	}

	/**
	 * Устанавливает значение свойства
	 *
	 * @param string $property свойство
	 * @param string $value значение
	 */
	public function __set($property, $value)
	{
		if(array_key_exists($property, $this->properties))
		{
			$this->properties[$property]['value'] = $value;

			return true;
		}
		else
		{
			$this->{$property} = $value;
		}
	}

	/**
	 * Возвращает значение свойства
	 *
	 * @param string $property свойство
	 * @return int|null
	 */
	public function __get($property)
	{
		if($property == 'id')
		{
			return $this->getId();
		}

		return array_key_exists($property, $this->properties) ? $this->properties[$property]['value'] : null;
	}

	/**
	 * Проверяет сохранен ли объект
	 *
	 * @return bool
	 */
	public function isSaved()
	{
		return ($this->getId() > 0);
	}

	/**
	 * Возвращает свойство id
	 *
	 * @return int
	 */
	public function getId()
	{
		return (int) $this->id;
	}

	/**
	 * Возвращает день переданной даты или текущий
	 *
	 * @param bool $date дата
	 * @return false|string
	 */
	public static function getDay($date = false)
	{
		$date = $date ? strtotime($date) : time();

		return date('d', $date);
	}

	/**
	 * Возвращает массив объектов со всеми записями
	 *
	 * @param string $table таблица
	 * @param string $class класс
	 * @param string $order поле сортировки
	 * @param bool $asc тип сортировки
	 * @param int $limit лимит
	 * @param int $offset смещение
	 * @return array массив объектов со свойствами полей таблицы
	 */
	protected static function getAllWithOrder($table, $class, $order = false, $asc = true, $limit, $offset)
	{
		return self::getAllOnWhere($table, $class, false, false, $order, $asc, $limit, $offset);
	}

	/**
	 * Формирует запрос и создает массив объектов со свойствами из строк таблицы
	 *
	 * @param string $table таблица
	 * @param string $class класс
	 * @param string $where условие
	 * @param array $whereParams массив параметров
	 * @param string $order поле сортировки
	 * @param bool $asc тип сортировки
	 * @param int $limit лимит
	 * @param int $offset смещение
	 * @return array массив объектов со свойствами из строк таблицы
	 */
	protected static function getAllOnWhere($table, $class, $where = false, array $whereParams = array(), $order =false, $asc = false, $limit = false, $offset = false)
	{
		$select = new SelectDB(self::$db);
		$select->from($table, array('*'));

		if($where)
		{
			$select->where($where, $whereParams);
		}

		if($order)
		{
			$select->order(array($order), array($asc));
		}
		else
		{
			$select->order(array('id'));
		}

		if($limit)
		{
			$select->limit($limit, $offset);
		}

		$propertiesDataList = self::$db->select($select);

		return AbstractObjectDB::buildMultiple($class, $propertiesDataList);
	}

	/**
	 * Возвращает количество строк по условию
	 *
	 * @param string $table таблица
	 * @param string $where условие
	 * @param array $whereParams массив параметров для подстановки в условие
	 * @return string количество строк
	 */
	protected static function getCountOnWhere($table, $where = false, $whereParams = array())
	{
		$select = new SelectDB(self::$db);
		$select->from($table, array('COUNT(id)'));

		if($where)
		{
			$select->where($where, $whereParams);
		}

		return self::$db->selectCell($select);
	}

	/**
	 * Возвращает количство строк по значению поля
	 *
	 * @param string $table таблица
	 * @param string $field поле
	 * @param string|int $value значение
	 * @return string количество строк
	 */
	protected static function getCountOnField($table, $field, $value)
	{
		$value = is_array($value) ? $value : array($value);

		return self::getCountOnWhere($table, "`$field` = " . self::$db->getSQ(), $value);
	}

	/**
	 * Добавляет новые свойства объектам по связывающему полю id
	 *
	 * @param $objectsDataList
	 * @param AbstractObjectDB $class
	 * @param string $fieldOut
	 * @param string $fieldIn
	 * @return array
	 */
	protected static function addSubObject($objectsDataList, $class, $fieldOut, $fieldId)
	{
		// Массив id таблицы переданного класса
		$ids = array();

		// Цикл для получения id связывающего поля (например в таблице комментарии есть user_id, который оставил комментарий)
		foreach ($objectsDataList as $objectData)
		{
			$ids[] = self::getComplexValue($objectData, $fieldId);
		}

		// Если количество id = 0, то нам нечего добавлять
		if(count($ids) == 0)
		{
			return array();
		}

		// Получаем все данные таблицы, относящейся к классу по полученным ранее id
		$newObjectsData = $class::getAllOnIds($ids);

		// Если ничего не получили,- то и добавлять нечего
		if(count($newObjectsData) == 0)
		{
			return $objectsDataList;
		}

		// Запускаем цикл для добавления нового свойства
		foreach ($objectsDataList as $id => $objectData)
		{
			// Если значение такого по id существует среди новых объектов
			if(isset($newObjectsData[self::getComplexValue($objectData, $fieldId)]))
			{
				// Присваиваем новому свойству объект по id
				$objectsDataList[$id]->{$fieldOut} = $newObjectsData[self::getComplexValue($objectData, $fieldId)];
			}
			else
			{
				// Иначе присваиваем пустое значение
				$objectData->{$fieldOut} = null;
			}
		}

		return $objectsDataList;
	}

	/**
	 * Получаем значение свойства. Может быть большая вложенность.
	 *
	 * @param $objectData
	 * @param string $fieldIn
	 * @return mixed
	 */
	protected static function getComplexValue($objectData, $fieldIn)
	{
		if(strpos($fieldIn, '->'))
		{
			$fields = explode('->', $fieldIn);
			$data = $objectData;

			foreach ($fields as $field)
			{
				$data = $data->{$field};
			}
		}
		else
		{
			$data = $objectData->{$fieldIn};
		}

		return $data;
	}

	/**
	 * Загружает объект по значению поля
	 *
	 * @param string $field поле
	 * @param string|int $value значение
	 * @return bool
	 */
	protected function loadOnField($field, $value)
	{
		$value = is_array($value) ? $value : array($value);

		$select = new SelectDB(self::$db);
		$select->from(self::$table, array('*'))
			->where("`$field` = " . self::$db->getSQ(), $value);

		if(($data = self::$db->selectRow($select)))
		{
			if($this->init($data))
			{
				return $this->postLoad();
			}
		}

		return false;
	}

	/**
	 * Добавляет свойство объекту
	 *
	 * @param string $property свойство
	 * @param string $validator валидатор
	 * @param string $type тип
	 * @param string|int $default значение по дефолту
	 */
	protected function addProperty($property, $validator, $type = null, $default = null)
	{
		$this->properties[$property] = array(
											'value' => $default,
											'validator' => $validator,
											'type' => in_array($type, self::$types) ? $type : null
										);
	}

	/**
	 * Возвращает ip адрес полльзователя
	 *
	 * @return mixed
	 */
	protected function getIP()
	{
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Хэширует строку с секретным словом
	 *
	 * @param string $str строка
	 * @param string $secret секретное слово
	 * @return string
	 */
	protected static function hash($str, $secret)
	{
		return md5($str, $secret);
	}

	/**
	 * Возвращает случайно сгенерированный id
	 *
	 * @return string
	 */
	protected function getKey()
	{
		return uniqid();
	}

	protected function preInsert()
	{
		return $this->validate();
	}

	protected function preUpdate()
	{
		return $this->validate();
	}

	protected function preDelete()
	{
		return true;
	}

	protected function preValidate()
	{
		return true;
	}

	protected function postLoad()
	{
		return true;
	}

	protected function postInit()
	{
		return true;
	}

	protected function postInsert()
	{
		return true;
	}

	protected function postUpdate()
	{
		return true;
	}

	protected function postDelete()
	{
		return true;
	}

	protected function postValidate()
	{
		return true;
	}

	/**
	 * Возвращает массив свойст объекта
	 *
	 * @return array массив свойств
	 */
	private function getSelectFields()
	{
		$fields = array_keys($this->properties);
		array_push($fields, 'id');

		return $fields;
	}

	/**
	 * Удаляет объекту идентификатор
	 */
	private function removeId()
	{
		$this->id = null;
	}

	/**
	 * Проверяет на валидность свойства объекта через валидаторы
	 *
	 * @return bool
	 * @throws \Exception
	 */
	private function validate()
	{
		if(!$this->preValidate())
		{
			throw new \Exception();
		}

		$validatorsDataList = array();
		$errors = array();

		foreach ($this->properties as $property => $data)
		{
		    $validatorClass = Config::VALIDATOR_NAMESPACE . $data['validator'];
			$validatorsDataList[$property] = new $validatorClass($data['value']);
		}

		foreach ($validatorsDataList as $property => $validator)
		{
			if(!$validator->isValid())
			{
				$errors[$property] = $validator->getErrors();
			}
		}

		if(!count($errors))
		{
			if(!$this->postValidate())
			{
				throw new \Exception();
			}
			else
			{
				return true;
			}
		}
		else
		{
		    foreach ($errors as $error)
            {
                throw new \Exception($error[0]);
            }
		}
	}
}