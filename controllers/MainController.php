<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 25.09.17
 * Time: 8:51
 */

namespace controllers;


use core\Url;
use library\config\Config;
use modules\Article;
use modules\Blog;
use modules\Category;
use modules\Intro;
use modules\PollResult;
use objects\ArticleDB;
use objects\CategoryDB;
use objects\CommentDB;
use objects\PollDataDB;
use objects\PollDB;
use objects\PollVoterDB;
use objects\SectionDB;

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

	public function actionSection()
    {
        $sectionDB = new SectionDB();
        $sectionDB->loadOnId($this->request->id);

        if(!$sectionDB->isSaved())
        {
            $this->notFound();
        }

        $this->sectionId = $sectionDB->sectionId;
        $this->title = $sectionDB->title;
        $this->metaDesc = $sectionDB->metaDesc;
        $this->metaKey = $sectionDB->metakey;

        $hornav = $this->getHornav();
        $hornav->addData($sectionDB->title);

        $intro = new Intro();
        $intro->hornav = $hornav;
        $intro->obj = $sectionDB;

        $blog = new Blog();
        $articles = ArticleDB::getLimitOnSectionId($this->request->id, Config::COUNT_ARTICLES_ON_PAGE);
        $moreArticles = ArticleDB::getAllOnSectionId($this->request->id);

        foreach ($articles as $article)
        {
            unset($moreArticles[$article->id]);
        }

        $blog->articles = $articles;
        $blog->moreArticles = $moreArticles;

        $this->render($intro . $blog);
    }

	public function actionCategory()
    {
        $categoryDB = new CategoryDB();
        $categoryDB->loadOnId($this->request->id);

        if(!$categoryDB->isSaved())
        {
            $this->notFound();
        }

        $this->sectionId = $categoryDB->sectionId;
        $this->title = $categoryDB->title;
        $this->metaDesc = $categoryDB->metaDesc;
        $this->metaKey = $categoryDB->metakey;

        $sectionDB = new SectionDB();
        $sectionDB->loadOnId($this->sectionId);

        $hornav = $this->getHornav();
        $hornav->addData($sectionDB->title, $sectionDB->link);
        $hornav->addData($categoryDB->title);

        $intro = new Intro();
        $intro->hornav = $hornav;
        $intro->obj = $categoryDB;

        $category = new Category();
        $articles = ArticleDB::getLimitOnCategoryId($this->request->id, Config::COUNT_ARTICLES_ON_PAGE);
        $moreArticles = ArticleDB::getAllOnCategoryId($this->request->id);

        foreach ($articles as $article)
        {
            unset($moreArticles[$article->id]);
        }

        $category->articles = $articles;
        $category->moreArticles = $moreArticles;

        $this->render($intro . $category);
    }

    public function actionArticle()
    {
        $articleDB = new ArticleDB();
        $articleDB->loadOnId($this->request->id);

        if(!$articleDB->isSaved())
        {
            $this->notFound();
        }

        $this->title = $articleDB->title;
        $this->metaDesc = $articleDB->metaDesc;
        $this->metaKey = $articleDB->metaKey;

        $hornav = $this->getHornav();

        if($articleDB->section)
        {
            $this->sectionId = $articleDB->sectionId;
            $hornav->addData($articleDB->section->title, $articleDB->section->link);
            $this->activeUri = Url::getUrl('section', '', array('id', $articleDB->section->id));
        }

        if($articleDB->category)
        {
            $hornav->addData($articleDB->category->title, $articleDB->category->link);
            $this->activeUri = Url::getUrl('category', '', array('id', $articleDB->category->id));
        }

        $hornav->addData($articleDB->title);

        $prevArticle = new ArticleDB();
        $prevArticle->getPrevArticle($articleDB);
        $nextArticle = new ArticleDB();
        $nextArticle->getNextArticle($articleDB);

        $article = new Article();
        $article->hornav = $hornav;
        $article->authUser = $this->authUser;
        $article->article = $articleDB;
        $article->prevArticle = ($prevArticle->isSaved()) ? $prevArticle : null;
        $article->nextArticle = ($nextArticle->isSaved()) ? $nextArticle : null;
        $article->linkRegister = Url::getUrl('register');

		$article->comments = CommentDB::getAllOnArticleId($this->request->id);

        $this->render($article);
    }

    public function actionPoll()
    {
        if($this->request->poll)
        {
            $pollVoterDB = new PollVoterDB();

            $pollData = PollDataDB::getAllSortDataByVotersOnPollId($this->request->id);
            $alreadyPoll = PollVoterDB::isAlreadyPoll(array_keys($pollData));
            $checks = array(array($alreadyPoll, false, 'ERROR_ALREADY_POLL'));

            $this->formProcessor->process('poll', $pollVoterDB, array('pollDataId'), $checks, 'SUCCESS_POLL');
            $this->redirect(Url::currentUrl());
        }

        $pollDB = new PollDB();
        $pollDB->loadOnId($this->request->id);

        if(!$pollDB->isSaved())
        {
            $this->notFound();
        }

        $this->title = 'Результаты голосования: ' . $pollDB->title;
        $this->metaDesc = 'Результаты голосования: ' . $pollDB->title . '.';
        $this->metaKey = 'результаты голосования,' . mb_strtolower($pollDB->title);

        $pollDataDB = PollDataDB::getAllOnPollId($this->request->id);
        $hornav = $this->getHornav();
        $hornav->addData($pollDB->title);

        $pollResult = new PollResult();
        $pollResult->hornav = $hornav;
        $pollResult->message = $this->formProcessor->getSessionMessage('poll');
        $pollResult->title = $pollDB->title;
        $pollResult->data = $pollDataDB;

        $this->render($pollResult);
    }
}