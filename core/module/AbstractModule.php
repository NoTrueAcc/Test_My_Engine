<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 20.09.17
 * Time: 10:49
 */

namespace core\module;


use core\View;

/**
 * Класс для работы с модулями
 *
 * Class AbstractModule
 * @package core\module
 */
abstract class AbstractModule
{
	private $properties = array();
	private $view;

	/**
	 * AbstractModule constructor.
	 * @param View $view
	 */
	public function __construct($view)
	{
		$this->view = $view;
	}

	abstract public function getTemplateFile();

	/**
	 * Возвращает значение свойства объекта
	 *
	 * @param string $propertyName имя свойства
	 * @return null
	 */
	final public function getProperty($propertyName)
	{
		return array_key_exists($propertyName, $this->properties) ? $this->properties[$propertyName]['value'] : null;
	}

	/**
	 * Устанавливает значенрие свойству объекта
	 *
	 * @param string $propertyName имя свойства
	 * @param string|int|array $value значение свойства
	 */
	final public function setProperty($propertyName, $value)
	{
		if(array_key_exists($propertyName, $this->properties))
		{
			if(is_array($this->properties[$propertyName]['value']))
			{
				if(is_array($value))
				{
					$this->properties[$propertyName]['value'] = $value;
				}
				else
				{
					$this->properties[$propertyName]['value'][] = $value;
				}
			}
			else
			{
				$this->properties[$propertyName]['value'] = $value;
			}
		}
	}

	/**
	 * Преобразует объект в строку подставляя его свойства в шаблон и возвращает сгенерированный шаблон
	 *
	 * @return string
	 */
	final public function __toString()
	{
		$this->preRender();

		return $this->view->render($this->getTemplateFile(), $this->getProperties(), true);
	}

	/**
	 * Действия перед преобразованием у разных модулей могут быть разные или не быть
	 */
	protected function preRender()
	{
		return;
	}

	/**
	 * Добавляет свойство объекту
	 *
	 * @param string $propertyName имя свойства
	 * @param string|array|int $value значение
	 * @param bool $isArray является ли свойство массивом
	 */
	final protected function addProperty($propertyName, $value = null, $isArray = false)
	{
		$this->properties[$propertyName]['isArray'] = $isArray;
		$value = ($isArray && !is_array($value)) ? array($value) : $value;

		if(empty($value) && $isArray)
		{
			$this->properties[$propertyName]['value'] = array();
		}
		else
		{
			$this->properties[$propertyName]['value'] = $value;
		}
	}

	/**
	 * Добавляет свойству объект
	 *
	 * @param string $propertyName имя свойства
	 * @param string $field название нового поля для объекта
	 * @param object $obj объект
	 */
	final protected function addObject($propertyName, $field, $obj)
	{
		if(array_key_exists($propertyName, $this->properties))
		{
			$this->properties[$propertyName]['value'][$field] = $obj;
		}
	}

	/**
	 * Возвращает все свойства объекта
	 *
	 * @return array ассоциативный массив свойств
	 */
	final protected function getProperties()
	{
		$properties = array();

		foreach ($this->properties as $property => $data)
		{
			$properties[$property] = $data['value'];
		}

		return $properties;
	}

	/**
	 * Возвращает свойство объекта по пути к нему
	 *
	 * @param object $obj объект
	 * @param string $fields путь к свойству
	 * @return mixed
	 */
	final protected function getComplexValue($obj, $fields)
	{
		if(strpos($fields, '->'))
		{
			$fields = explode('->', $fields);
			$value = $obj;

			foreach ($fields as $field)
			{
				$value = $value->{$field};
			}
		}
		else
		{
			$value = $obj->{$fields};
		}

		return $value;
	}

	/**
	 * Склонение слова в зависимости от числа
	 *
	 * @param int $number число
	 * @param array $suffix массив склонений слова
	 * @return mixed
	 */
	final protected function declensionStringByNumber($number, $suffix)
	{
		$keys = array(2, 0, 1, 1, 2);
		$mod = $number % 100;

		$suffixKey = (($mod > 7) && ($mod < 20)) ? 2 : $keys[min($mod % 10, 5)];

		return $suffix[$suffixKey];
	}
}