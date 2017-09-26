<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13.09.2017
 * Time: 6:41
 */

namespace modules;

/**
 * Класс модуля Hornav
 *
 * Class Hornav
 * @package modules
 */
class Hornav extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('data', null, true);
    }

	/**
	 * Добавляет объект свойству
	 *
	 * @param string $title описание
	 * @param string $link ссылка
	 */
    public function addData($title, $link = false)
    {
        $newClass = new \stdClass();
        $newClass->title = $title;
        $newClass->link = $link;

        $this->data = $newClass;
    }

	/**
	 * Возвращает название шаблона модуля
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'hornav';
    }
}