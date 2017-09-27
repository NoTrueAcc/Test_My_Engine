<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 18:16
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки заголовка на корректность
 *
 * Class ValidateTitle
 * @package validator
 */
class ValidateTitle extends AbstractValidator
{
    const MAX_LEN = 100;
    const CODE_EMPTY = 'ERROR_TITLE_EMPTY';
    const CODE_MAX_LEN = 'ERROR_TITLE_MAX_LEN';

    protected function validate()
    {
        if(empty($this->data))
        {
            $this->setError(self::CODE_EMPTY);
        }
        else
        {
            $this->setError(self::CODE_MAX_LEN);
        }
    }
}