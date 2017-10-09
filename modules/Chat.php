<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 06.10.2017
 * Time: 16:25
 */

namespace modules;


class Chat extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();

        $this->addProperty('hornav');

    }

    public function getTemplateFile()
    {
        return 'chat';
    }
}