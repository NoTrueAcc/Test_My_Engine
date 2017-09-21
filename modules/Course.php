<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12.09.2017
 * Time: 6:34
 */

namespace modules;

/**
 * Класс для модуля Course
 *
 * Class Course
 * @package modules
 */
class Course extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('authUser');
        $this->addProperty('courses', null, true);
    }

	/**
	 * Возвращает название шаблона модуля
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'course';
    }
}