<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 17:13
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Класс для проверки мета тегов описания
 *
 * Class ValidateMetaDesc
 * @package validator
 */
class ValidateMetaDesc extends AbstractValidator
{
    const MAX_LEN = 255;
    const CODE_EMPTY = 'ERROR_MT_EMPTY';
    const CODE_MAX_LEN = 'ERROR_MT_MAX_LEN';

    protected function validate()
    {
        if(empty($this->data))
        {
            $this->setError(self::CODE_EMPTY);
        }

        if(mb_strlen($this->data) > self::MAX_LEN)
        {
            $this->setError(self::CODE_MAX_LEN);
        }
    }
}