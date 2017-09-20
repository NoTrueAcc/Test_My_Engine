<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 14.09.17
 * Time: 14:14
 */

namespace library\database;


use core\database\AbstractSelectDB;

/**
 * Класс для работы с объектами типа Select
 *
 * Class SelectDB
 * @package library\database
 */
class SelectDB extends AbstractSelectDB
{
	/**
	 * Select constructor.
	 */
	public function __construct()
	{
		parent::__construct(DataBase::getDBO());
	}
}