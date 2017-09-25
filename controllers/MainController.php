<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 25.09.17
 * Time: 8:51
 */

namespace controllers;


use library\config\Config;
use modules\Blog;
use objects\ArticleDB;

class MainController extends AbstractController
{
	public function actionIndex()
	{
		$this->title = 'Как создать свой сайт';
		$this->metaDesc = 'Как создать свой сайт';
		$this->metaKey = 'Создание сайта,как создать свой сайт';

		$articles = ArticleDB::getAllArticles(Config::COUNT_ARTICLES_ON_PAGE, $this->getOffset(Config::COUNT_ARTICLES_ON_PAGE), true);
		$pagination = $this->getPagination(ArticleDB::getCount(), Config::COUNT_ARTICLES_ON_PAGE, '/');

		$blog = new Blog();
		$blog->articles = $articles;
		$blog->pagination = $pagination;

		$this->render($this->renderData(array('blog' => $blog), 'index'));
	}
}