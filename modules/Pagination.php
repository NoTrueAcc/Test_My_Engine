<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12.09.2017
 * Time: 17:27
 */

namespace modules;

/**
 * Класс для модуля Pagination
 *
 * Class Pagination
 * @package modules
 */
class Pagination extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('url');
        $this->addProperty('urlPage');
        $this->addProperty('countElements');
        $this->addProperty('countOnPage');
        $this->addProperty('countShowPage');
        $this->addProperty('countPages');
        $this->addProperty('startPage');
        $this->addProperty('endPage');
        $this->addProperty('active');
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'pagination';
    }
}