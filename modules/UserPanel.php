<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13.09.2017
 * Time: 6:18
 */

namespace modules;

/**
 * Класс для модуля UserPanel
 *
 * Class UserPanel
 * @package modules
 */
class UserPanel extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('user');
        $this->addProperty('uri');
        $this->addProperty('items', null, true);
    }

	/**
	 * Добавляет новый элемент
	 *
	 * @param string $title название
	 * @param string $link ссылка
	 */
    public function addItem($title, $link)
    {
        $newClass = new \stdClass();
        $newClass->title = $title;
        $newClass->link = $link;

        $this->items = $newClass;
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'user_panel';
    }
}