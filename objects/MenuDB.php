<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 04.09.2017
 * Time: 18:06
 */

namespace objects;

use library\database\ObjectDB;

/**
 * Класс для работы с меню
 *
 * Class MenuDB
 * @package objects
 */
class MenuDB extends ObjectDB
{
    protected static $tableName = 'menu';

    public function __construct()
    {
        parent::__construct(self::$tableName);

        $this->addProperty('type', 'ValidateId');
        $this->addProperty('title', 'ValidateTitle');
        $this->addProperty('link', 'ValidateUrl');
        $this->addProperty('parentId', 'ValidateId');
        $this->addProperty('external', 'ValidateBoolean');
    }

    /**
     * Возвращает объект верхнего меню
     *
     * @return array
     */
    public static function getTopMenu()
    {
        return ObjectDB::getAllOnField(self::$tableName, __CLASS__, 'type', TOP_MENU, 'id');
    }

    /**
     * Возвращает объект главного меню
     *
     * @return array
     */
    public static function getMainMenu()
    {
        return ObjectDB::getAllOnField(self::$tableName, __CLASS__, 'type', MAIN_MENU, 'id');
    }
}