<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 6:29
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Класс для проверки булевской переменной
 *
 * Class ValidateBoolean
 * @package validator
 */
class ValidateBoolean extends AbstractValidator
{
    protected function validate()
    {
        if(($this->data != 0) || ($this->data != 1))
        {
            $this->setError(self::CODE_UNKNOWN);
        }
    }
}