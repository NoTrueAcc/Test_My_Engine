<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 04.09.2017
 * Time: 7:38
 */

namespace validator;


use core\validator\AbstractValidator;

/**
 * Валидатор для типа курсов
 *
 * Class ValidateCourseType
 * @package validator
 */
class ValidateCourseType extends AbstractValidator
{
    const MAX_COURSE_TYPE = 3;

    protected function validate()
    {
        if(!is_int($this->data))
        {
            $this->setError(self::CODE_UNKNOWN);
        }
        else
        {
            if(($this->data < 1) || ($this->data > self::MAX_COURSE_TYPE))
            {
                $this->setError(self::CODE_UNKNOWN);
            }
        }
    }
}