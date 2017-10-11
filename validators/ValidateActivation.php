<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 15:29
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки хэща
 *
 * Class ValidateActivation
 * @package validator
 */
class ValidateActivation extends AbstractValidator
{
	const MAX_LEN = 100;

	protected function validate()
	{
		if(mb_strlen($this->data) > self::MAX_LEN)
		{
			$this->setError(self::CODE_UNKNOWN);
		}
	}
}