<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 18:19
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Класс для проверки URI на корректность
 *
 * Class ValidateUri
 * @package validator
 */
class ValidateUri extends AbstractValidator
{
    const MAX_LEN = 255;

    protected function validate()
    {
        if(mb_strlen($this->data) > self::MAX_LEN)
        {
            $this->setError(self::CODE_UNKNOWN);
        }
        else
        {
            $pattern = "~^(?:/[a-z0-9.,_@%&?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i";
            if(!preg_match($pattern, $this->data))
            {
                $this->setError(self::CODE_UNKNOWN);
            }
        }
    }
}