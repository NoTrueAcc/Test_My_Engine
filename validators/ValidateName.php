<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 17:25
 */

namespace validators;


use core\validator\AbstractValidator;

class ValidateName extends AbstractValidator
{
    const MAX_LEN = 100;
    const CODE_EMPTY = 'ERROR_NAME_EMPTY';
    const CODE_INVALID = 'ERROR_NAME_INVALID';
    const CODE_MAX_LEN = 'ERROR_NAME_MAX_LEN';

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
            elseif($this->isContainQuotes($this->data))
            {
                $this->setError(self::CODE_INVALID);
            }
        }
    }
}