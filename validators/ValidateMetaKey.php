<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 17:21
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Класс для проверки мета тегов ключевых слов
 *
 * Class ValidateMetaDesc
 * @package validator
 */
class ValidateMetaKey extends AbstractValidator
{
    const MAX_LEN = 255;
    const CODE_EMPTY = 'ERROR_MK_EMPTY';
    const CODE_MAX_LEN = 'ERROR_MK_MAX_LEN';

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