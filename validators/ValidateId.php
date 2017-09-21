<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 7:01
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Класс для проверки id на корректность
 *
 * Class ValidateId
 * @package validator
 */
class ValidateId extends AbstractValidator
{
    protected function validate()
    {
        if(!is_null($this->data) && (!is_numeric($this->data) || ($this->data < 0)))
        {
            $this->setError(self::CODE_UNKNOWN);
        }
    }
}