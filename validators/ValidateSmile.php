<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 10.10.17
 * Time: 12:34
 */

namespace validators;


use core\validator\AbstractValidator;

class ValidateSmile extends ValidateText
{
	const MAX_LEN = 45;
}