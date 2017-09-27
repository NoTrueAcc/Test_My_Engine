<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 7:10
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки имени изображения на корректность
 *
 * Class ValidateImg
 * @package validator
 */
class ValidateImg extends AbstractValidator
{
    protected function validate()
    {
        if(!is_null($this->data) && !preg_match('/[a-z0-9\-_]+\.(jpg|jpeg|png|gif)/i', $this->data))
        {
            $this->setError(self::CODE_UNKNOWN);
        }
    }
}