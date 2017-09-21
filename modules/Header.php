<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 12:40
 */

namespace modules;

/**
 * Класс для работы с модулем header
 *
 * Class Header
 * @package modules
 */
class Header extends AbstractModule
{
	public function __construct()
	{
		parent::__construct();

		$this->addProperty('title');
		$this->addProperty('favicon');
		$this->addProperty('meta', null,true);
		$this->addProperty('css', null,true);
		$this->addProperty('js', null,true);
	}

	/**
	 * Добавляет объект мета-тега свойству(типа массив) объекта
	 *
	 * @param string $name имя мета тега или тип httpEquiv
	 * @param string $content данные
	 * @param bool $httpEquiv тип мета тега
	 */
	public function meta($name, $content, $httpEquiv)
	{
		$newClass = new \stdClass();
		$newClass->name = $name;
		$newClass->content = $content;
		$newClass->httpEquiv = $httpEquiv;

		$this->meta[] = $newClass;
	}

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
	public function getTemplateFile()
	{
		return 'header';
	}
}