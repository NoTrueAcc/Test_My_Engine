<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 7:32
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Класс для проверки ip адреса на корректность
 *
 * Class ValidateIP
 * @package validator
 */
class ValidateIP extends AbstractValidator
{
    protected function validate()
    {
        if (!($this->data == 0) && !preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}/i', $this->data))
        {
            $this->setError(self::CODE_UNKNOWN);
        }
    }
}