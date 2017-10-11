<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 30.08.2017
 * Time: 6:38
 */

namespace validators;


use core\validator\AbstractValidator;

/**
 * Класс для проверки URL на корректность
 *
 * Class ValidateUri
 * @package validator
 */
class ValidateUrl extends AbstractValidator
{
    const MAX_LEN = 255;

    protected function validate()
    {
        $data = $this->data;
        if (mb_strlen($data) > self::MAX_LEN)
        {
            $this->setError(self::CODE_UNKNOWN);
        }
        else
        {
            $patternfirst = "~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}".
                "(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
                "org|mil|edu|arpa|gov|biz|info|aero|inc|name|local|[a-z]{2})|(?!0)(?:(?".
                "!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&".
                "?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i";
            $patternsecond = "~^(?:/[a-z0-9.,_@%&?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i";
            if (!preg_match($patternfirst, $data) && !preg_match($patternsecond, $data)) $this->setError(self::CODE_UNKNOWN);
        }
    }
}