<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 13:17
 */

namespace modules;

/**
 * Класс модуля категории
 *
 * Class Category
 * @package modules
 */
class Category extends AbstractModule
{
	public function __construct()
	{
		parent::__construct();

		$this->addProperty('category');
		$this->addProperty('articles', null, true);
	}

	/**
	 * Возвращает название шаблона модуля
	 *
	 * @return string
	 */
	public function getTemplateFile()
	{
		return 'category';
	}
}