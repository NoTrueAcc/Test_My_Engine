<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.08.2017
 * Time: 18:10
 */

namespace validator;

/**
 * Класс для проверки короткого текста на корректность
 *
 * Class ValidateSmallText
 * @package validator
 */
class ValidateSmallText extends ValidateText
{
    const MAX_LEN = 500;
}