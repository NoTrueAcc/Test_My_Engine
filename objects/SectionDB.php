<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 8:23
 */

namespace objects;


use core\Url;
use library\config\Config;
use library\database\ObjectDB;

/**
 * Класс для работы с таблицей секции
 *
 * Class SectionDB
 * @package objects
 */
class SectionDB extends ObjectDB
{
	protected static $table = 'sections';

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
	 * Формирует ссылку на статью
	 */
	protected function postInit()
	{
		$this->img = $this->img ? Config::DIR_ARTICLES . $this->img : null;
		$this->link = Url::getUrl('section', false, array('id' => $this->getId()));
	}

	/**
	 * Возвращает название изображения
	 *
	 * @return bool
	 */
	protected function preValidate()
	{
		$this->img = is_null($this->img) ? null : basename($this->img);

		return true;
	}
}