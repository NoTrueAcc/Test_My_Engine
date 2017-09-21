<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 04.09.2017
 * Time: 7:43
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Валидатор для списка идентификаторов
 *
 * Class ValidateIds
 * @package validator
 */
class ValidateIds extends AbstractValidator
{
    protected function validate()
    {
        if(is_null($this->data))
        {
            return;
        }
        elseif(!preg_match('/^\d+(,\d+)*$/', $this->data))
        {
            $this->setError(self::CODE_UNKNOWN);
        }
    }
}