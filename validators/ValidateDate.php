<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 6:35
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки даты на корректность
 *
 * Class ValidateDate
 * @package validator
 */
class ValidateDate extends AbstractValidator
{
    protected function validate()
    {
        if(!is_null($this->data) && !strtotime($this->data))
        {
            $this->setError(self::CODE_UNKNOWN);
        }
    }
}