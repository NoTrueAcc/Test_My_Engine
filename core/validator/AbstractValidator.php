<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 14.09.17
 * Time: 15:23
 */

namespace core\validator;

/**
 * Класс валидатор
 *
 * Class AbstractValidator
 * @package core\validator
 */
abstract class AbstractValidator
{
	const CODE_UNKNOWN = 'UNKNOWN_ERROR';

	protected $data;
	private $errors = array();

	public function __construct($data)
	{
		$this->data = $data;
		$this->validate();
	}

	/**
	 * Метод проверки валидации у каждого дочернего класса свой
	 *
	 * @return mixed
	 */
	abstract protected function validate();

	/**
	 * Возвращает ошибки валидации
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Проверка валидности
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return !count($this->errors);
	}

	/**
	 * Устанавливает ошибку
	 *
	 * @param $code
	 */
	protected function setError($code)
	{
		$this->errors[] = $code;
	}

	/**
	 * Проверка на наличие кавычек
	 *
	 * @param string $string строка
	 * @return bool
	 */
	protected function isContainQuotes($string)
	{
		$quotesList = array('"', '\'', '`', '&quot;', '&apos;');

		foreach ($quotesList as $quote)
		{
			if(strpos($string, $quote))
			{
				return true;
			}
		}

		return false;
	}
}