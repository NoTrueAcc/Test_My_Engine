<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 16:13
 */

namespace objects;


use library\database\ObjectDB;
use library\database\SelectDB;

class ArticleDB extends ObjectDB
{
	protected static $table = 'articles';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('title', 'ValidateTitle');
		$this->addProperty('img', 'ValidateImg');
		$this->addProperty('intro', 'ValidateText');
		$this->addProperty('full', 'ValidateText');
		$this->addProperty('sectionId', 'ValidateId');
		$this->addProperty('catId', 'ValidateId');
		$this->addProperty('date', 'ValidateDate', self::TYPE_TIMESTAMP, $this->getDate());
		$this->addProperty('metaDesc', 'ValidateMetaDesc');
		$this->addProperty('metaKey', 'ValidateMetaKey');
	}

	public static function getAllArticles($limit = false, $offset = false, $postHandling = false)
	{
		$select = self::getBaseSelect();
		$select->order('date', false);

		if($limit)
		{
			$select->limit($limit, $offset);
		}

		$articlesData = self::$db->select($select);
		$articles = self::buildMultiple(__CLASS__, $articlesData);

		if($postHandling)
		{
			$tempArticles = $articles;
			$articles = array();

			foreach ($tempArticles as $tempArticle)
			{
				$articles[] = $tempArticle->postHandling();
			}
		}

		return $articles;
	}

	private static function getBaseSelect()
	{
		return (new SelectDB())->from(self::$table, array('*'));
	}

	private function postHandling()
	{
		$this->getSectionAndCategory();
		$this->countComments = C
	}

	private function getSectionAndCategory()
	{
		if(isset($this->sectionId))
		{
			$section = new Sec;
			$section->loadOnId($this->sectionId);

			if($section->isSaved())
			{
				$this->section = $section;
			}
		}

		if(isset($this->catId))
		{
			$section = new Cat;
			$section->loadOnId($this->catId);

			if($section->isSaved())
			{
				$this->section = $section;
			}
		}
	}
}