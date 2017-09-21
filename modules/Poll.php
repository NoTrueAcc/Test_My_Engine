<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 11.09.2017
 * Time: 18:27
 */

namespace modules;

/**
 * Класс для модуля Poll
 *
 * Class Poll
 * @package modules
 */
class Poll extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('title');
        $this->addProperty('action');
        $this->addProperty('data', null, true);
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'pool';
    }
}