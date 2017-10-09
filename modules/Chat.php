<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 06.10.2017
 * Time: 16:25
 */

namespace modules;


use core\File;

class Chat extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();

        $this->addProperty('hornav');
		$this->addProperty('messages', File::getChatMessages(), true);
    }

    public function getTemplateFile()
    {
        return 'chat';
    }
}