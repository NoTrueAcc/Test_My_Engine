<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 12:07
 */

namespace library\mail;


use core\mail\AbstractMail;
use core\View;
use library\config\Config;

/**
 * Класс для работы с сообщениями
 *
 * Class Mail
 * @package library\mail
 */
class Mail extends AbstractMail
{
	public function __construct()
	{
		parent::__construct(new View(Config::DIR_EMAIL), Config::ADM_EMAIL);
		$this->setFromName(Config::ADM_NAME);
	}
}