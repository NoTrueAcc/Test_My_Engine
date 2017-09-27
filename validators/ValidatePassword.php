<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 17:29
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки корректности пароля
 *
 * Class ValidatePassword
 * @package validator
 */
class ValidatePassword extends AbstractValidator
{
    const MIN_LEN = 6;
    const MAX_LEN = 100;
    const CODE_MIN_LEN = 'ERROR_PASSWORD_MIN_LEN';
    const CODE_MAX_LEN = 'ERROR_PASSWORD_MAX_LEN';
    const CODE_EMPTY = 'ERROR_PASSWORD_EMPTY';
    const CODE_CONTENT = 'ERROR_PASSWORD_CONTENT';

    protected function validate()
    {
        if(empty($this->data))
        {
            $this->setError(self::CODE_EMPTY);
        }
        else
        {
            if(mb_strlen($this->data) < self::MIN_LEN)
            {
                $this->setError(self::CODE_MIN_LEN);
            }
            elseif(mb_strlen($this->data) > self::MAX_LEN)
            {
                $this->setError(self::CODE_MAX_LEN);
            }
        }
    }
}