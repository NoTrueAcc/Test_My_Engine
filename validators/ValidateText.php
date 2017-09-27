<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 18:05
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки текста на корректность
 *
 * Class ValidateText
 * @package validator
 */
class ValidateText extends AbstractValidator
{
    const MAX_LEN = 50000;
    const CODE_EMPTY = 'ERROR_TEXT_EMPTY';
    const CODE_MAX_LEN = 'ERROR_TEXT_MAX_LEN';

    protected function validate()
    {
        if(empty($this->data))
        {
            $this->setError(self::CODE_EMPTY);
        }
        elseif(mb_strlen($this->data) > self::MAX_LEN)
        {
            $this->setError(self::CODE_MAX_LEN);
        }
    }
}