<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 14.09.17
 * Time: 9:27
 */

namespace core\database;

/**
 * Класс для работы с объектами типа Select
 *
 * Class SelectDB
 * @package core\database
 */
class SelectDB
{
	private $db;
	private $from;
	private $where;
	private $order;
	private $limit;
	private static $logic = array('AND', 'OR');

	/**
	 * Select constructor.
	 * @param AbstractDataBase $db объект подключения к БД
	 */
	public function __construct(AbstractDataBase $db)
	{
		$this->db = $db;
	}

	/**
	 * Добавляет свойство from объекту Select
	 *
	 * @param string $table название таблицы
	 * @param array|string $fields массив полей для получения
	 * @return SelectDB $this объект класса Select
	 */
	public function from($table, $fields)
	{
		$fields = is_array($fields) ? $fields : array($fields);

		$tableName = $this->db->getTableName($table);
		$from = '';

		if((count($fields) == 1) && ($fields[0] == '*'))
		{
			$from = '*';
		}
		else
		{
			for($i = 0; $i < count($fields); $i++)
			{
				// Если передана функция добавляем апострафы ее переменной
				$from .= strpos($fields[$i], '(') ? preg_replace('/\((.*)?\)/i', '(`$1`),', $fields[$i]) : $fields[$i] . ',';
			}

			$from = $this->db->removeLastSymbol($from);
		}

		$from .= " FROM `$tableName`";
		$this->from = $from;

		return $this;
	}

	/**
	 * Добавляет свойство where объекту Select
	 *
	 * @param string $where условие
	 * @param array|string $params массив параметров для подстановки в условие
	 * @param string $logic логическое объединение условий, может принимать значения И|ИЛИ, по умолчанию 'И'
	 * @return SelectDB $this объект класса Select
	 */
	public function where($where, $params = '', $logic = 'AND')
	{
		$params = is_array($params) ? $params : array($params);

		$where = $this->db->getQuery($where, $params);

		return $this->setWhere($where, $logic);
	}

	/**
	 * Добавляет свойство whereIn объекту Select
	 *
	 * @param string $field поле
	 * @param array|string $params массив параметров для подстановки
	 * @param string $logic логическое объединение условий, может принимать значения И|ИЛИ, по умолчанию 'И'
	 * @return SelectDB $this объект класса Select
	 */
	public function whereIn($field, $params, $logic = 'AND')
	{
		$params = is_array($params) ? $params : array($params);

		$where = "`$field` IN (";

		for ($i = 0; $i < count($params); $i++)
		{
			$where .= $this->db->getSQ() . ",";
		}

		$where = $this->db->removeLastSymbol($where);
		$where .= ')';

		return $this->where($where, $params, $logic);
	}

	public function whereFieldInSet($field, $value, $logic = 'AND')
	{
		$where = 'FIELD_IN_SET(' . $this->db->getSQ() . "', `$field`) > 0";

		return $this->where($where, array($value), $logic);
	}

	/**
	 * Добавляет свойство order объекту Select
	 *
	 * @param array|string $fields массив полей сортировки
	 * @param array|string $asc массив типов сортировки
	 * @return SelectDB $this объект класса Select
	 */
	public function order($fields, $asc = true)
	{
		$asc = is_array($asc) ? $asc : array($asc);
		$fields = is_array($fields) ? $fields : array($fields);

		$order = ' ORDER BY ';

		for($i = 0; $i < count($fields); $i++)
		{
			$order .= "`" . $fields[$i] . "`" . ($asc[$i] ? ',' : ' DESC,');
		}

		$this->order = $this->db->removeLastSymbol($order);

		return $this;
	}

	/**
	 * Добавляет свойство order(рандомный) объекту Select
	 *
	 * @return SelectDB $this объект класса Select
	 */
	public function orderRand()
	{
		$this->order = ' ORDER BY RAND()';

		return $this;
	}

	/**
	 * Добавляет свойство limit объекту Select
	 *
	 * @param int $limit количество строк для вывода
	 * @param int $offset смещение
	 * @return SelectDB $this объект класса Select
	 */
	public function limit($limit, $offset = 0)
	{
		$limit = (int) $limit;
		$offset = (int) $offset;

		if(($limit < 0) || ($offset < 0))
		{
			return $this;
		}

		$this->limit = " LIMIT $limit OFFSET $offset";

		return $this;
	}

	/**
	 * Преобразует свойства объекта Select в строку
	 *
	 * @return string строка запроса select
	 */
	public function __toString()
	{
		$where = isset($this->where) ? $this->where : '';
		$order = isset($this->order) ? $this->order : '';
		$limit = isset($this->limit) ? $this->limit : '';

		return isset($this->from) ? 'SELECT ' . $this->from . $where . $order . $limit : '';
	}

	/**
	 * Проверяет существует ли условие и добавляет новое
	 *
	 * @param string $where условие
	 * @param string $logic логическое объединение условий, может принимать значения И|ИЛИ, по умолчанию 'И'
	 * @return SelectDB $this объект класса Select
	 */
	private function setWhere($where, $logic)
	{
		$logic = mb_strtoupper($logic);

		if(!in_array($logic, self::$logic))
		{
			return $this;
		}

		$this->where = isset($this->where) ? $this->where . " $logic " . $where : " WHERE $where";

		return $this;
	}
}