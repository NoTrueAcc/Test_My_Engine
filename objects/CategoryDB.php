<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 8:32
 */

namespace objects;


use core\Url;
use library\config\Config;
use library\database\ObjectDB;

/**
 * Класс для работы с талицей категории
 *
 * Class CategoryDB
 * @package objects
 */
class CategoryDB extends ObjectDB
{
	protected static $table = 'categories';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('title', 'ValidateTitle');
		$this->addProperty('img', 'ValidateImg');
		$this->addProperty('description', 'ValidateText');
		$this->addProperty('metaDesc', 'ValidateMetaDesc');
		$this->addProperty('metaKey', 'ValidateMetaKey');
	}

	/**
	 * Формирует директорию изображения, ссылку на категорию, заполняет свойство объектом секции по айди
	 *
	 * @return bool
	 */
	protected function postInit()
	{
		$this->img = $this->img ? Config::DIR_ARTICLES . $this->img : null;
		$this->link = Url::getUrl('category', false, array('id' => $this->getId()));

		$section = new SectionDB();
		$section->loadOnId($this->sectionId);
		$this->section = $section;

		return true;
	}

	/**
	 * Меняет путь к картинке на имя
	 *
	 * @return bool
	 */
	protected function preValidate()
	{
		$this->img = is_null($this->img) ? null : basename($this->img);

		return true;
	}
}