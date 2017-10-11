<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 11.09.2017
 * Time: 18:22
 */

namespace modules;

/**
 * Класс для модуля Slider
 *
 * Class Slider
 * @package modules
 */
class Slider extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('course');
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'slider';
    }
}