<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 20.09.17
 * Time: 9:31
 */

namespace library;


use core\Message;

class JsValidator
{
	private $message;

	/**
	 * JsValidator constructor.
	 * @param Message$message
	 */
	public function __construct($message)
	{
		$this->message = $message;
	}

	public function password($fieldEqual = false, $minLen = true, $textEmpty = false)
	{

	}
}