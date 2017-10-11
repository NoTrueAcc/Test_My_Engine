<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 6:42
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки e-mail на корректность
 *
 * Class ValidateEmail
 * @package validator
 */
class ValidateEmail extends AbstractValidator
{
    const MAX_LEN = 100;
    const CODE_EMPTY = 'ERROR_EMAIL_EMPTY';
    const CODE_INVALID = 'ERROR_EMAIL_INVALID';
    const CODE_MAX_LEN = 'ERROR_EMAIL_MAX_LEN';

    protected function validate()
    {
        if(empty($this->data))
        {
            $this->setError(self::CODE_EMPTY);
        }
        else
        {
            if(mb_strlen($this->data) > self::MAX_LEN)
            {
                $this->setError(self::CODE_MAX_LEN);
            }
            else
            {
                $pattern = '/^[a-z0-9_][a-z0-9\._\-]*@([a-z0-9]+([a-z0-9\-)*[a-z0-9]+)*\.)+[a-z]+$/i';
				if (!preg_match($pattern, $this->data))
				{
					$this->setError(self::CODE_INVALID);
				}
            }
        }
    }
}