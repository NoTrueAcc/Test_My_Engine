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

	public function getAllOnArticleId($articleId)
	{
		$select = new SelectDB();
		$select->from(self::$table, array('*'))
			->where('`articleId = `' . self::$db->getSQ(), array($articleId))
			->order('date');

		$comments = ObjectDB::buildMultiple(__CLASS__, self::$db->select($select));
		$comments = ObjectDB::addSubObject($comments, 'UserDB', 'user', 'userId');

		return $comments;
	}

	public function getCountOnArticleId($articleId)
	{
		$select = new SelectDB();
		$select->from(self::$table, array('COUNT(id)'))
			->where('`articleId` = ' . self::$db->getSQ(), array($articleId));

		return self::$db->selectCell($select);
	}

	protected function postInit()
	{
		$this->link = Url::getUrl('article', '', array('id', $this->articleId));
		$this->link = Url::addID($this->link, 'comment_' . $this->getId());
	}
}