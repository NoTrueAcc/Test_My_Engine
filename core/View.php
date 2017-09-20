<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 15:47
 */

namespace core;

/**
 * Класс шаблонизатор
 *
 * Class View
 * @package core
 */
class View
{
	private $templateDir;

	public function __construct($templateDir)
	{
		$this->templateDir = $templateDir;
	}

	/**
	 * Обрабатывает шаблон, подставляя параметры и возвращает или отрисовывает его
	 *
	 * @param string $file название файла шаблона
	 * @param array $params массив параметров для подстановки
	 * @param bool $return вернуть или отрисовать шаблон
	 * @return string
	 */
	public function render($file, array $params, $return = false)
	{
		$template = $this->templateDir . $file . '.phtml';

		extract($params);
		ob_start();
		include($template);

		if($return)
		{
			return ob_get_clean();
		}
		else
		{
			echo ob_get_clean();

			return true;
		}
	}
}