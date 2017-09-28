<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 20.09.17
 * Time: 9:31
 */

namespace library;


use core\Message;
use validator\ValidateEmail;
use validator\ValidateLogin;
use validator\ValidateName;
use validator\ValidatePassword;

/**
 * Класс валидатор для проверки форм
 *
 * Class JavaScriptValidator
 * @package library
 */
class JavaScriptValidator
{
	private $message;

	/**
	 * JavaScriptValidator constructor.
	 * @param Message $message
	 */
	public function __construct($message)
	{
		$this->message = $message;
	}

	/**
	 * Заполняет свойства объекта данными по паролю
	 *
	 * @param bool $fieldEqual должно ли поле быть эквивалентно
	 * @param bool $minLen минимальная длина?
	 * @param bool $textEmpty пустое поле?
	 * @return \stdClass
	 */
	public function password($fieldEqual = false, $minLen = true, $textEmpty = false)
	{
		$newClass = $this->getBaseClass();

		if($minLen)
		{
			$newClass->minLen = \validators\ValidatePassword::MIN_LEN;
			$newClass->textMinLen = $this->message->getMessage(\validators\ValidatePassword::CODE_MIN_LEN);
		}

		$newClass->maxLen = \validators\ValidatePassword::MAX_LEN;
		$newClass->textMaxLen = $this->message->getMessage(\validators\ValidatePassword::CODE_MAX_LEN);
		$newClass->textEmpty = $textEmpty ? $this->message->getMessage($textEmpty) :
			$this->message->getMessage(\validators\ValidatePassword::CODE_EMPTY);

		if($fieldEqual)
		{
			$newClass->fieldEqual = $fieldEqual;
			$newClass->textFieldEqual = $this->message->getMessage('ERROR_PASSWORD_CONF');
		}

		return $newClass;
	}

	/**
	 * Заполняет свойства объекта данными по имени
	 *
	 * @param bool $textEmpty пустой текст?
	 * @param bool $maxLen максимальная длина
	 * @param bool $textType тип
	 * @return \stdClass
	 */
	public function name($textEmpty = false, $maxLen = false, $textType = false)
	{
		$newClass = $this->getBaseClass();
		$newClass->type = 'name';
		$newClass->maxLen = \validators\ValidateName::MAX_LEN;

		$newClass->textEmpty = $textEmpty ? $this->message->getMessage($textEmpty) :
			$this->message->getMessage(\validators\ValidateName::CODE_EMPTY);
		$newClass->textMaxLen = $maxLen ? $this->message->getMessage($maxLen)
			: $this->message->getMessage(\validators\ValidateName::CODE_MAX_LEN);
		$newClass->textType = $textType ? $this->message->getMessage($textType)
			: $this->message->getMessage(\validators\ValidateName::CODE_INVALID);

		return $newClass;
	}

	/**
	 * Заполняет свойства объекта данными по логину
	 *
	 * @param bool $textEmpty пустой текст?
	 * @param bool $maxLen максимальная длина?
	 * @param bool $textType тип
	 * @return \stdClass
	 */
	public function login($textEmpty = false, $maxLen = false, $textType = false)
	{
		$newClass = $this->getBaseClass();
		$newClass->type = 'login';
		$newClass->maxLen = \validators\ValidateName::MAX_LEN;

		$newClass->textEmpty = $textEmpty ? $this->message->getMessage($textEmpty) :
			$this->message->getMessage(\validators\ValidateLogin::CODE_EMPTY);
		$newClass->textMaxLen = $maxLen ? $this->message->getMessage($maxLen)
			: $this->message->getMessage(\validators\ValidateLogin::CODE_MAX_LEN);
		$newClass->textType = $textType ? $this->message->getMessage($textType)
			: $this->message->getMessage(\validators\ValidateLogin::CODE_INVALID);

		return $newClass;
	}

	/**
	 * Заполняет свойства объекта данными по почте
	 *
	 * @param bool $textEmpty пустой текст
	 * @param bool $maxLen максимальная длина
	 * @param bool $textType тип
	 * @return \stdClass
	 */
	public function email($textEmpty = false, $maxLen = false, $textType = false)
	{
		$newClass = $this->getBaseClass();
		$newClass->type = 'email';
		$newClass->maxLen = \validators\ValidateName::MAX_LEN;

		$newClass->textEmpty = $textEmpty ? $this->message->getMessage($textEmpty) :
			$this->message->getMessage(\validators\ValidateEmail::CODE_EMPTY);
		$newClass->textMaxLen = $maxLen ? $this->message->getMessage($maxLen)
			: $this->message->getMessage(\validators\ValidateEmail::CODE_MAX_LEN);
		$newClass->textType = $textType ? $this->message->getMessage($textType)
			: $this->message->getMessage(\validators\ValidateEmail::CODE_INVALID);

		return $newClass;
	}

	/**
	 * Заполняет свойства объекта данными по аватару
	 *
	 * @return \stdClass
	 */
	public function avatar()
	{
		$newClass = $this->getBaseClass();
		$newClass->textEmpty = $this->message->getMessage('ERROR_IMAGE_EMPTY');

		return $newClass;
	}

	/**
	 * Заполняет свойства объекта данными по капче
	 *
	 * @return \stdClass
	 */
	public function captcha()
	{
		$newClass = $this->getBaseClass();
		$newClass->textEmpty = $this->message->getMessage('ERROR_CAPTCHA_EMPTY');

		return $newClass;
	}

	/**
	 * Возвращает базовый объект с пустыми свойствами
	 *
	 * @return \stdClass
	 */
	private function getBaseClass()
	{
		$newClass = new \stdClass();
		$newClass->type = '';
		$newClass->minLen = '';
		$newClass->maxLen = '';
		$newClass->fieldEqual = '';
		$newClass->textMinLen = '';
		$newClass->textMaxLen = '';
		$newClass->textEmpty = '';
		$newClass->textType = '';
		$newClass->textFieldEqual = '';

		return $newClass;
	}
}