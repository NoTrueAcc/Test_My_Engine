<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 16:46
 */

namespace objects;


use core\Url;
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
			->where('`articleId = `' . self::$db->getSQ(), array($articleId))
			->order('date');

		$comments = ObjectDB::buildMultiple(__CLASS__, self::$db->select($select));
		$comments = ObjectDB::addSubObject($comments, 'UserDB', 'user', 'userId');

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
	 * Формирует ссылку на комментарии
	 */
	protected function postInit()
	{
		$this->link = Url::getUrl('article', '', array('id', $this->articleId));
		$this->link = Url::addID($this->link, 'comment_' . $this->getId());
	}
}