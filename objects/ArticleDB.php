<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 16:13
 */

namespace objects;


use core\Url;
use library\config\Config;
use library\database\ObjectDB;
use library\database\SelectDB;

/**
 * Класс для работы с таблицей артикулов
 *
 * Class ArticleDB
 * @package objects
 */
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

	/**
	 * Возвращает все статьи в виде объектов
	 *
	 * @param bool|int $limit лимит
	 * @param bool|int $offset смещение
	 * @param bool $postHandling пост обработка
	 * @return array массив объектов таблицы статей
	 */
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

	/**
	 * Возвращает массив объектов лимитированных статей по айди секции
	 *
	 * @param int|string $sectionId айди секции
	 * @param int $limit лимит
	 * @param int $offset смещение
	 * @return array массив объектов статей
	 */
	public static function getLimitOnSectionId($sectionId, $limit, $offset)
	{
		$select = self::getBaseSelect();
		$select->where('sectionId = ' . self::$db->getSQ(), array($sectionId))
			->order('date', false)
			->limit($limit, $offset);

		$articlesData = self::$db->select($select);
		$tempArticles = ObjectDB::buildMultiple(__CLASS__, $articlesData);
		$articles = array();

		foreach ($tempArticles as $tempArticle)
		{
			$articles[] = $tempArticle->postHandling();
		}

		return $articles;
	}

	/**
	 * Возвращает массив объектов всех статей по айди секции
	 *
	 * @param int $sectionId айди секции
	 * @param bool|string $order поле сортировки
	 * @return array массив объектов статей
	 */
	public static function getAllOnSectionId($sectionId, $order = false)
	{
		return self::getAllOnField(self::$table, __CLASS__, 'sectionId', $sectionId, $order);
	}

	/**
	 * Возвращает массив объектов всех статей по айди категории
	 *
	 * @param int $sectionId айди категории
	 * @param bool|string $order поле сортировки
	 * @return array массив объектов статей
	 */
	public static function getAllOnCategoryId($categoryId, $order = false)
	{
		return self::getAllOnField(self::$table, __CLASS__, 'catId', $categoryId, $order);
	}

	/**
	 * Возвращает объект предыдущей статьи по айди
	 *
	 * @param ArticleDB $article объект статьи
	 * @return bool
	 */
	public function getPrevArticle($article)
	{
		$select = self::getBaseSelect();
		$select->where('catId = ' . self::$db->getSQ(), array($article->catId))
			->where('id < ' . self::$db->getSQ(), array($article->id))
			->order(array('id'), false);

		$articleData = self::$db->selectRow($select);

		return $this->init($articleData);
	}

	/**
	 * Возвращает объект следующей статьи по айди
	 *
	 * @param ArticleDB $article объект статьи
	 * @return bool
	 */
	public function getNextArticle($article)
	{
		$select = self::getBaseSelect();
		$select->where('catId = ' . self::$db->getSQ(), array($article->catId))
			->where('id > ' . self::$db->getSQ(), array($article->id))
			->order(array('id'), true);

		$articleData = self::$db->selectRow($select);

		return $this->init($articleData);
	}

	/**
	 * Пост инициализация
	 *
	 * @return bool
	 */
	protected function postInit()
	{
		$this->img = isset($this->img) ? Config::DIR_ARTICLES . $this->img : null;
		$this->link = Url::getUrl('article', false, array('id' => $this->getId()));

		return true;
	}

	/**
	 * Пре валидация
	 *
	 * @return bool
	 */
	protected function preValidate()
	{
		if(!is_null($this->img))
		{
			$this->img = basename($this->img);
		}

		return true;
	}

	/**
	 * Базовый селект, чтобы не создавать его лишний раз
	 *
	 * @return \core\database\AbstractSelectDB
	 */
	private static function getBaseSelect()
	{
		return (new SelectDB())->from(self::$table, array('*'));
	}

	/**
	 * Пост обработчик
	 */
	private function postHandling()
	{
		$this->getSectionAndCategory();
		$this->countComments = CommentDB::getCountOnArticleId($this->getId());
		$this->dayShow = ObjectDB::getDay($this->date);
		$this->monthShow = ObjectDB::getMonth($this->date);
	}

	/**
	 * Добавляет объекты секции и категории по айди
	 */
	private function getSectionAndCategory()
	{
		if(isset($this->sectionId))
		{
			$section = new SectionDB();
			$section->loadOnId($this->sectionId);

			if($section->isSaved())
			{
				$this->section = $section;
			}
		}

		if(isset($this->catId))
		{
			$section = new CategoryDB();
			$section->loadOnId($this->catId);

			if($section->isSaved())
			{
				$this->section = $section;
			}
		}
	}
}