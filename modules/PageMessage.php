<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 12:16
 */

namespace modules;

/**
 * Класс для модуля PageMessage
 *
 * Class PageMessage
 * @package modules
 */
class PageMessage extends AbstractModule
{
	public function __construct()
	{
		parent::__construct();

		$this->addProperty('hornav');
		$this->addProperty('header');
		$this->addProperty('text');
	}

	/**
	 * Возвращает шаблон модуля
	 *
	 * @return string
	 */
	public function getTemplateFile()
	{
		return 'page_message';
	}
}