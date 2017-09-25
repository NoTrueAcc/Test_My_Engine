<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 13:13
 */

namespace core;

/**
 * Класс для работы со ссылками
 *
 * Class Url
 * @package core
 */
class Url
{
	/**
	 * Возвращает url с переданными данными
	 *
	 * @param string $action действие
	 * @param string $controller контроллер
	 * @param array $data данные
	 * @param bool $amp амперсант
	 * @param string $address адрес
	 * @return string
	 */
	public static function getUrl($action, $controller = false, $data = array(), $amp = true, $address = '')
	{
		$amp = $amp ? '&amp;' : '&';
		$uri = $controller ? "/$controller/$action" : "/$action";

		if(count($data))
		{
			$uri .= '?';

			foreach ($data as $key => $value)
			{
				$uri .= "$key=$value" . $amp;
			}

			$uri = mb_substr($uri, 0, -mb_strlen($amp));
		}

		return self::getAbsoluteAddress($address, $uri);
	}

	/**
	 * Возвращает полный адрес
	 *
	 * @param string $address адрес
	 * @param string $uri путь
	 * @return string
	 */
	public static function getAbsoluteAddress($address, $uri)
	{
		return $address . $uri;
	}

	/**
	 * Возвращает адрес с измененными амперсантами
	 *
	 * @param string $address адрес
	 * @param string $amp путь
	 * @return mixed|string
	 */
	public static function currentUrl($address = '', $amp = false)
	{
		$url = self::getAbsoluteAddress($address, $_SERVER['REQUEST_URI']);

		if($amp)
		{
			$url = str_replace('&', '&amp;', $url);
		}

		return $url;
	}

	/**
	 * Возвращает контроллер и действие по ссылке
	 *
	 * @return array
	 */
	public static function getControllerAndAction()
	{
		$uri = $_SERVER['REQUEST_URI'];

		$routes =  preg_replace('/^(.*)?\?.*/i', '$1', $uri);
		$routes = explode('/', $routes);

		$controller = 'main';
		$action = 'index';

		if(isset($routes[2]) && !empty($routes[2]))
		{
			$controller = !empty($routes[1]) ? $routes[1] : $controller;
			$action = $routes[2];
		}
		else
		{
			$action = !empty($routes[1]) ? $routes[1] : $action;
		}

		$controller = '\controllers\\' . ucfirst($controller);
		$action = ucfirst($action);

		return array($controller, $action);
	}

	/**
	 * Добавляет параметр page
	 *
	 * @param string $url адрес
	 * @param bool $amp амперсант
	 * @return string
	 */
	public static function addPage($url, $amp = true)
	{
		return self::addGet($url, 'page', '', $amp);
	}

	/**
	 * Удаляет параметр page
	 *
	 * @param string $url ссылка
	 * @param bool $amp амперсант
	 * @return bool|mixed|string
	 */
	public static function deletePage($url, $amp = true)
	{
		return self::deleteGet($url, 'page', $amp);
	}

	/**
	 * Добавляет параметр
	 *
	 * @param string $url ссылка
	 * @param string $name имя
	 * @param string $value значение
	 * @param bool $amp амперсант
	 * @return string
	 */
	public static function addGet($url, $name, $value, $amp = true)
	{
		$amp = $amp ? '&amp;' : '&';
		$url .= strpos($url, '?') ? $amp . "$name=$value" : "?$name=$value";

		return $url;
	}

	/**
	 * Удаляет параметр
	 *
	 * @param string $url ссылка
	 * @param string $name имя
	 * @param bool $amp амперсант
	 * @return bool|mixed|string
	 */
	public static function deleteGet($url, $name, $amp = true)
	{
		if(!strpos($url, '?'))
		{
			return $url;
		}

		$url = $amp ? str_replace('&', '&amp;', $url) : $url;
		// Присваиваем списку переменных массив количество элементов которого не может быть меньше 2ух(при необходимости заполняется пустой строкой до 2ух)
		list($urlPart,$getPart) = array_pad(explode('?', $url), 2, '');
		// Разбиваем строку на переменные
		parse_str($getPart, $getVars);

		// Удаляем переменную
		unset($getVars[$name]);

		if($getVars)
		{
			// Формируем строку с параметрами
			$url = $urlPart . '?' . http_build_query($getVars);
			$url = $amp ? str_replace('&', '&amp;', $url) : $amp;
		}
		else
		{
			$url = $urlPart;
		}

		return $url;
	}

	/**
	 * Добавляет идентификатор к адресу
	 *
	 * @param string $url ссылка
	 * @param string|int $id идентификатор
	 * @return string
	 */
	public static function addID($url, $id) {
		return $url . "#" . $id;
	}
}