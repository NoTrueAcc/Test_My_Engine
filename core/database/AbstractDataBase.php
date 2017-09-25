<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 13.09.17
 * Time: 10:39
 */

namespace core\database;

/**
 * Класс для подключения и выполнения запросов к БД
 *
 * Class AbstractDataBase
 * @package core\database
 */
abstract class AbstractDataBase
{
	private $mysqli;
	private $sq;
	private $prefix;

	protected function __construct($host, $user, $password, $dbName, $sq, $prefix)
	{
		$this->mysqli = @new \mysqli($host, $user, $password, $dbName);

		if($this->mysqli->connect_errno)
		{
			exit('Ошибка подключения к БД');
		}

		$this->sq = $sq;
		$this->prefix = $prefix;

		$this->mysqli->set_charset('utf8');
		$this->mysqli->query('SET lc_time_names = "ru_RU"');
	}

	/**
	 * Возвращает спецсимвол для подстановки в запрос
	 *
	 * @return mixed
	 */
	public function getSQ()
	{
		return $this->sq;
	}

	/**
	 * Возвращает название таблицы с префиксом
	 *
	 * @param $table
	 * @return string
	 */
	public function getTableName($table)
	{
		return $this->prefix . $table;
	}

	/**
	 * Удаляет последний символ в строке
	 *
	 * @param string $string строка
	 * @return string преобразованная строка
	 */
	public function removeLastSymbol($string)
	{
		return substr($string, 0, -1);
	}

	/**
	 * Выполняет запрос селект к БД и возвращает результат в виде массива
	 *
	 * @param SelectDB $select объект запроса
	 * @return bool|array результат в виде ассоциативного массива
	 */
	public function select(SelectDB $select)
	{
		if(!($result = $this->getResult($select, true, true)))
		{
			return false;
		}

		$resultData = array();

		while($row = $result->fetch_assoc())
		{
			$resultData[] = $row;
		}

		return $resultData;
	}

	/**
	 * Выполняет запрос селект к БД и возвращает одну строку в виде массива
	 *
	 * @param SelectDB $select объект запроса к БД
	 * @return bool|array результат запроса в виде массива
	 */
	public function selectRow(SelectDB $select)
	{
		if(!($result = $this->getResult($select, true, true)))
		{
			return false;
		}

		return $result->fetch_assoc();
	}

	/**
	 * Выполняет запрос селект к БД и возвращает столбец в виде массива
	 *
	 * @param SelectDB $select объект запроса к БД
	 * @return bool|array результат запроса в виде массива
	 */
	public function selectCol(SelectDB $select)
	{
		if(!($result = $this->getResult($select, true, true)))
		{
			return false;
		}

		$resultData = array();

		while($row = $result->fetch_assoc())
		{
			foreach ($row as $value)
			{
				$resultData[] = $value;
			}
		}

		return $resultData;
	}

	/**
	 * Выполняет запрос селект к БД и возвращает значение ячейки
	 *
	 * @param SelectDB $select объект запроса
	 * @return string результат запроса в виде строки
	 */
	public function selectCell(SelectDB $select)
	{
		if(!($result = $this->getResult($select, true, true)))
		{
			return false;
		}

		$result = $result->fetch_assoc();

		return array_shift($result);
	}

	/**
	 * Добавляет строку в таблицу
	 *
	 * @param string $table таблица
	 * @param array $newFieldsData ассоциативный массив с данными для добавления
	 * @return bool
	 */
	public function insert($table, array $newFieldsData)
	{
		$tableName = $this->getTableName($table);
		$insertParams = array();
		$fields = '(';
		$values = 'VALUES(';

		foreach ($newFieldsData as $field => $value)
		{
			$fields .= "`$field`,";
			$values .= $this->sq . ',';
			$insertParams[] = $value;
		}

		$fields = $this->removeLastSymbol($fields) . ')';
		$values = $this->removeLastSymbol($values) . ')';
		$query = "INSERT INTO `$tableName` $fields $values";

		return $this->query($query, $insertParams);
	}

	/**
	 * Изменяет значения в строках по условию
	 *
	 * @param string $table таблица
	 * @param array $newFieldsData новые данные
	 * @param string $where условие
	 * @param array $whereParams параметры для подстановки в условие
	 * @return bool
	 */
	public function update($table, array $newFieldsData, $where = '', array $whereParams)
	{
		$tableName = $this->getTableName($table);
		$query = "UPDATE `$tableName` SET ";
		$insertParams = array();

		foreach ($newFieldsData as $field => $value)
		{
			$query .= "`$field` = " . $this->sq . ',';
			$insertParams[] = $value;
		}

		$query = $this->removeLastSymbol($query);

		if($where != '')
		{
			$insertParams = array_merge($insertParams, $whereParams);
			$query .= " WHERE $where";
		}

		return $this->query($query, $insertParams);
	}

	/**
	 * Удаляет строки по условию
	 *
	 * @param string $table таблица
	 * @param string $where условие
	 * @param array $whereParams параметры для подстановки в условие
	 * @return bool
	 */
	public function delete($table, $where, array $whereParams)
	{
		$tableName = $this->getTableName($table);
		$query = "DELETE FROM `$tableName` WHERE $where";

		return $this->query($query, $whereParams);
	}

	/**
	 * Возвращает сформированный запрос
	 *
	 * @param string $query запрос
	 * @param array $params массив параметров для подстановки в запрос
	 * @return string $query сформированный запрос
	 */
	public function getQuery($query, array $params)
	{
		if(count($params))
		{
			$sqLen = mb_strlen($this->sq);
			$offset = 0;

			foreach ($params as $param)
			{
				$sqPos = strpos($query, $this->sq, $offset);
				$arg = is_null($param) ? 'NULL' : "'" . $this->mysqli->real_escape_string($param) . "'";
				$query = substr_replace($query, $arg, $sqPos, $sqLen);
				$offset = $sqPos + mb_strlen($arg);
			}
		}

		return $query;
	}

	/**
	 * Отправляет запрос к БД
	 *
	 * @param string $query запрос
	 * @param array $params параметры для подстановки в запрос
	 * @return bool|mixed если было добавление данных - последний добавленный id
	 */
	private function query($query, array $params = array())
	{
		if(!$this->mysqli->query($this->getQuery($query, $params)))
		{
			return false;
		}

		return ($this->mysqli->insert_id === 0) ? true : $this->mysqli->insert_id;
	}

	/**
	 * Выполняет запрос к БД и возвращает результат
	 *
	 * @param SelectDB $select объект запроса
	 * @param bool $zero может ли вернуть 0 строк
	 * @param bool $one может ли вернуть 1 строку
	 * @return bool|\mysqli_result результат запроса
	 */
	private function getResult(SelectDB $select, $zero, $one)
	{
		if(!($result = $this->mysqli->query($select)) || (!$zero && ($result->num_rows == 0)) || (!$one && ($result->num_rows == 1)))
		{
			return false;
		}

		return $result;
	}

	/**
	 * Закрываем подключение, когда не активно
	 */
	public function __destruct()
	{
		if($this->mysqli && !$this->mysqli->connect_errno)
		{
			$this->mysqli->close();
		}
	}
}