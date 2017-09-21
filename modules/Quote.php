<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12.09.2017
 * Time: 7:02
 */

namespace modules;

/**
 * Класс для модуля Quote
 *
 * Class Quote
 * @package modules
 */
class Quote extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('quote');
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'quotes';
    }
}