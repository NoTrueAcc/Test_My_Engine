<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 20.09.17
 * Time: 10:32
 */

namespace core\mail;


use core\View;

/**
 * Класс для работы с сообщениями
 *
 * Class AbstractMail
 * @package core\mail
 */
abstract class AbstractMail
{
	private $view;
	private $from;
	private $fromName = '';
	private $type = 'text/html';
	private $encoding = 'utf-8';

	/**
	 * AbstractMail constructor.
	 * @param View $view
	 * @param string $from
	 */
	public function __construct($view, $from)
	{
		$this->view = $view;
		$this->from = $from;
	}

	/**
	 * Устанавливает имя отправителя
	 *
	 * @param string $fromName
	 */
	public function setFromName($fromName)
	{
		$this->fromName = $fromName;
	}

	/**
	 * Устанавливает тип
	 *
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * Устанавливает кодировку
	 *
	 * @param string $encoding
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	/**
	 * Отправляет письмо адресату
	 *
	 * @param string $to адресат
	 * @param array $data массив данных для подстановки в шаблон письма
	 * @param string $template шаблон письма
	 * @return bool
	 */
	public function send($to, array $data, $template)
	{
		$from = "=?utf-8?B?" . base64_encode($this->fromName) . "?="." <" . $this->from . ">";
		$headers = "From: " . $from . "\r\nReply-To: " . $from . "\r\nContent-type: " . $this->type . "; charset=\"" . $this->encoding . "\"\r\n";
		$text = $this->view->render($template, $data, true);
		$lines = preg_split('/\\r\\n|\\n/', $text);
		$subject = $lines[0];
		$subject = '?=utf-8?B?' . base64_encode($subject) . '?=';
		$body = '';

		for($i = 1; $i < count($lines); $i++)
		{
			$body .= $lines[$i] . '\n';
		}

		$body = substr($body, 0, -strlen('\n'));

		if ($this->type = "text/html")
		{
			$body = nl2br($body);
		}

		return mail($to, $subject, $body, $headers);
	}
}