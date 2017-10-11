<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 12:14
 */

namespace modules;


use core\View;
use library\config\Config;

/**
 * Класс для работы с модулями
 *
 * Class AbstractModule
 * @package modules
 */
abstract class AbstractModule extends \core\module\AbstractModule
{
	public function __construct()
	{
		parent::__construct(new View(Config::DIR_TMPL));
	}
}