<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 13:05
 */

namespace modules;

/**
 * Класс модуля Blog
 *
 * Class Blog
 * @package modules
 */
class Blog extends AbstractModule
{
	public function __construct()
	{
		parent::__construct();

		$this->addProperty('articles', null, true);
		$this->addProperty('moreArticles', null, true);
		$this->addProperty('pagination');
	}

	/**
	 * Возвращает название шаблона модуля
	 *
	 * @return string
	 */
	public function getTemplateFile()
	{
		return 'blog';
	}

	/**
	 * Добавляет склонение слова свойству объекта Article
	 */
	protected function preRender()
	{
		$words = array('комментарий', 'комментария', 'комментариев');

		foreach ($this->articles as $article)
		{
			$article->countCommentsText = $this->declensionStringByNumber($article->countComments, $words);
		}
	}
}