<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 13:25
 */

namespace modules;


use library\JavaScriptValidator;

class Form extends AbstractModule
{
	public function __construct()
	{
		parent::__construct();

		$this->addProperty('hornav');
		$this->addProperty('name');
		$this->addProperty('action');
		$this->addProperty('method', 'post');
		$this->addProperty('header');
		$this->addProperty('message');
		$this->addProperty('check', true);
		$this->addProperty('enctype');
		$this->addProperty('inputs', null, true);
		$this->addProperty('jsv', null, true);
	}

	/**
	 * Добавляет объект валидатора свойству объекта
	 *
	 * @param string $field название валидатора
	 * @param object $jsv объект валидатора
	 */
	public function addJSV($field, $jsv)
	{
		$this->addObject('jsv', $field, $jsv);
	}

	/**
	 * Создает объект типа текст
	 *
	 * @param string $name имя input
	 * @param string $label имя label
	 * @param string $value значение поля по умолчанию
	 * @param string $placeholder описание
	 */
	public function text($name, $label = '', $value = '', $placeholder = '')
	{
		$this->input($name, 'text', $label, $value, $placeholder);
	}

	/**
	 * Создает объект типа password
	 *
	 * @param string $name имя input
	 * @param string $label имя label
	 * @param string $placeholder описание
	 */
	public function password($name, $label = '', $placeholder = '')
	{
		$this->input($name, 'password', $label, '', $placeholder);
	}

	/**
	 * Создает объект типа captcha
	 *
	 * @param string $name имя input
	 * @param string $label имя label
	 */
	public function captcha($name, $label)
	{
		$this->input($name, 'captcha', $label);
	}

	/**
	 * Создает объект типа fileIMG
	 *
	 * @param string $name имя input
	 * @param string $label имя label
	 * @param string $img путь к изображению
	 */
	public function fileIMG($name, $label, $img)
	{
		$this->input($name, 'file_img', $label, $img);
	}

	/**
	 * Создает объект типа hidden
	 *
	 * @param string $name имя input
	 * @param string $value значение поля по умолчанию
	 */
	public function hidden($name, $value)
	{
		$this->input($name, 'hidden', '', $value);
	}

	/**
	 * Создает объект типа submit
	 *
	 * @param string $name имя input
	 * @param string $value значение поля по умолчанию
	 */
	public function submit($value, $name = false)
	{
		$this->input($name, 'submit', '', $value);
	}

	/**
	 * Создает объект типа input с переданными параметрами
	 *
	 * @param string $name имя input
	 * @param string $type тип
	 * @param string $label описание label
	 * @param string $value значение поля по умолчанию
	 * @param string $placeholder описание
	 */
	private function input($name, $type, $label = '', $value = '', $placeholder = '')
	{
		$newClass = new \stdClass();
		$newClass->name = $name;
		$newClass->type = $type;
		$newClass->label = $label;
		$newClass->value = $value;
		$newClass->placeholder = $placeholder;

		$this->inputs = $newClass;
	}

	/**
	 * Возвращает название шаблона модуля
	 *
	 * @return string
	 */
	public function getTemplateFile()
	{
		return 'form';
	}
}