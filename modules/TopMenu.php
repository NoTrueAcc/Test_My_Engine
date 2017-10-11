<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 11.09.2017
 * Time: 7:37
 */

namespace modules;

/**
 * Класс для модуля TopMenu
 *
 * Class TopMenu
 * @package modules
 */
class TopMenu extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('uri');
        $this->addProperty('items', null, true);
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'topmenu';
    }
}