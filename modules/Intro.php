<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25.09.2017
 * Time: 17:40
 */

namespace modules;


class Intro extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();

        $this->addProperty('hornav');
        $this->addProperty('obj');
    }

    public function getTemplateFile()
    {
        return 'intro';
    }
}