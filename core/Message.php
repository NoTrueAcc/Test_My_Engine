<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 11:32
 */

namespace core;

/**
 * Класс для работы с сообщениями
 *
 * Class Message
 * @package core
 */
class Message
{
	private $data;

	/**
	 * Конструктор парсит переданный файл и добавляет данные в виде ассоциативного массива свойству $data
	 *
	 * Message constructor.
	 * @param string $file путь к файлу с сообщениями
	 */
	private function __construct($file)
	{
		$this->data = parse_ini_file($file);
	}

	/**
	 * Возвращает сообщение по ключу
	 *
	 * @param string $messageName ключ
	 * @return string
	 */
	public function getMessage($messageName)
	{
		return $this->data[$messageName];
	}
}