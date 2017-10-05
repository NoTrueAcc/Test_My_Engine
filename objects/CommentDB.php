<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 16:46
 */

namespace objects;


use core\Url;
use library\config\Config;
use library\database\ObjectDB;
use library\database\SelectDB;

/**
 * Класс для раблоты с таблицей comments
 *
 * Class CommentDB
 * @package objects
 */
class CommentDB extends ObjectDB
{
	protected static $table = 'comments';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('articleId', 'ValidateId');
		$this->addProperty('userId', 'ValidateId');
		$this->addProperty('parentId', 'ValidateId');
		$this->addProperty('text', 'ValidateSmallText');
		$this->addProperty('date', 'ValidateDate', self::TYPE_TIMESTAMP, $this->getDate());
	}

	/**
	 * Возвращает все записи в виде объектов по айди артикула
	 *
	 * @param string|int $articleId айди артикула
	 * @return array
	 */
	public static function getAllOnArticleId($articleId)
	{
		$select = new SelectDB();
		$select->from(self::$table, array('*'))
			->where('`articleId` = ' . self::$db->getSQ(), array($articleId))
			->order('date');

		$comments = ObjectDB::buildMultiple(__CLASS__, self::$db->select($select));
		$comments = ObjectDB::addSubObject($comments, 'objects\UserDB', 'user', 'userId');

		return $comments;
	}

	/**
	 * Возвращает количество записей по айди артикула
	 *
	 * @param string|int $articleId айди артикула
	 * @return string
	 */
	public static function getCountOnArticleId($articleId)
	{
		$select = new SelectDB();
		$select->from(self::$table, array('COUNT(id)'))
			->where('`articleId` = ' . self::$db->getSQ(), array($articleId));

		return self::$db->selectCell($select);
	}

	/**
	 * Проверяет права на редактирование
	 *
	 * @param UserDB $authUser объект юзера
	 * @param string $field тип поля
	 * @return bool
	 */
	public function accessEdit($authUser, $type)
	{
		if($type == 'text')
		{
			return $this->userId == $authUser->id;
		}

		return false;
	}

	/**
	 * Проверяет на возможность удалить комментарий
	 *
	 * @param UserDB $authUser объект юзера
	 * @return bool
	 */
	public function accessDelete($authUser)
	{
		return $this->userId == $authUser->id;
	}

	/**
	 * Формирует ссылку на комментарии
	 */
	protected function postInit()
	{
		$this->link = Url::getUrl('article', '', array('id', $this->articleId), false, Config::ADDRESS);
		$this->link = Url::addID($this->link, 'comment_' . $this->getId());
	}

	/**
	 * Перед удалением основного комментария удаляет дочерние
	 *
	 * @return bool
	 */
	protected function preDelete()
	{
		$comments = self::getAllOnParentId($this->id);

		foreach ($comments as $comment)
		{
			try
			{
				$comment->delete();
			}
			catch (\Exception $e)
			{
				return false;
			}
		}

		return true;
	}

	protected function postInsert($success = false)
	{
		return $success;
	}

	/**
	 * Все статьи по айди родителя
	 *
	 * @param string|int $parentId айди родителя
	 * @return array
	 */
	private static function  getAllOnParentId($parentId)
	{
		return CommentDB::getAllOnField(self::$table, __CLASS__, 'parentId', $parentId);
	}
}