<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 11:32
 */

namespace core;

/**
 * Класс для обработки входящих данных со стороны клиента
 *
 * Class Request
 * @package core
 */
class Request
{
    private static $sefData;
	private $data;

	public function __construct()
	{
		$this->data = $this->xss(array_merge($_REQUEST, self::$sefData));
	}

	public static function addSefData($sefData)
    {
        self::$sefData = $sefData;
    }

	/**
	 * Возвращает данные по имени
	 *
	 * @param string $name имя
	 * @return mixed|null
	 */
	public function __get($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	/**
	 * Преобразует входящие данные в безопасные
	 *
	 * @param array|string $request входящие данные
	 * @return array|string
	 */
	private function xss($request)
	{
		if(is_array($request))
		{
			$escaped = array();

			foreach ($request as $key => $value)
			{
				$escaped[$key] = $this->xss($value);
			}

			return $escaped;
		}

		return trim(htmlspecialchars($request));
	}
}