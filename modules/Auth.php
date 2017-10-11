<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 12:49
 */

namespace modules;

/**
 * Класс для модуля Auth
 *
 * Class Auth
 * @package modules
 */
class Auth extends AbstractModule
{
	public function __construct()
	{
		parent::__construct();

		$this->addProperty('action');
		$this->addProperty('message');
		$this->addProperty('linkRegister');
		$this->addProperty('linkReset');
		$this->addProperty('linkRemind');
	}

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
	public function getTemplateFile()
	{
		return 'auth';
	}
}