<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 8:22
 */

namespace core\exception;

/**
 * Класс исключения валидатора
 *
 * Class Exception
 * @package core\exception
 */
class ValidatorException extends \Exception
{
	private $errors;

	public function __construct($errors)
	{
		parent::__construct();

		$this->errors = $errors;
	}

	/**
	 * Возвращает ошибки
	 *
	 * @return string
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}