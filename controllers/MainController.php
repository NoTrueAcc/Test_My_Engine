<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 25.09.17
 * Time: 8:51
 */

namespace controllers;


use core\Url;
use library\Captcha;
use library\config\Config;
use modules\Article;
use modules\Blog;
use modules\Category;
use modules\Form;
use modules\Intro;
use modules\PageMessage;
use modules\PollResult;
use objects\ArticleDB;
use objects\CategoryDB;
use objects\CommentDB;
use objects\PollDataDB;
use objects\PollDB;
use objects\PollVoterDB;
use objects\SectionDB;
use objects\UserDB;

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

            $pollData = PollDataDB::getAllOnPollId($this->request->id);
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

        $pollDataDB = PollDataDB::getAllSortDataByVotersOnPollId($this->request->id);
        $hornav = $this->getHornav();
        $hornav->addData($pollDB->title);

        $pollResult = new PollResult();
        $pollResult->hornav = $hornav;
        $pollResult->message = $this->formProcessor->getSessionMessage('poll');
        $pollResult->title = $pollDB->title;
        $pollResult->data = $pollDataDB;

        $this->render($pollResult);
    }

    public function actionRegister()
	{
		$messageName = 'register';

		if($this->request->register)
		{
			$userOldFirst = new UserDB();
			$userOldFirst->loadOnEmail($this->request->email);
			$userOldSecond = new UserDB();
			$userOldSecond->loadOnLogin($this->request->login);
			$captcha = $this->request->captcha;

			$checks = array(
				array(Captcha::check($captcha), true, 'ERROR_CAPTCHA_CONTENT'),
				array($this->request->password, $this->request->passwordConf, 'ERROR_PASSWORD_CONF'),
				array($userOldFirst->isSaved(), false, 'ERROR_EMAIL_ALREADY_EXISTS'),
				array($userOldSecond->isSaved(), false, 'ERROR_LOGIN_ALREADY_EXISTS')
			);

			$user = new UserDB();
			$fields = array('name', 'login', 'email', array('setPassword()', $this->request->password));
			$user = $this->formProcessor->process($messageName, $user, $fields, $checks);

			if($user instanceof UserDB)
			{
				$this->mail->send($user->email, array('user' => $user, 'link' => Url::getUrl('activate', '', array('login' => $user->login, 'key' => $user->activation), false, Config::ADDRESS)), 'register');

				Url::setCookiePageAccess('sregister');
				$this->redirect(Url::getUrl('sregister'));
			}
		}

		$this->title = 'Регистрация на сайте ' . Config::SITENAME;
		$this->metaDesc = 'Регистрация на сайте ' .Config::SITENAME . '.';
		$this->metaKey = 'регистрация на сайте ' . mb_strtolower(Config::SITENAME) . ', зарегистрироваться сайт ' . mb_strtolower(Config::SITENAME);
		$hornav = $this->getHornav();
		$hornav->addData('Регистрация');

		$form = new Form();
		$form->hornav = $hornav;
		$form->header = 'Регистрация';
		$form->name = 'register';
		$form->action = Url::currentUrl();
		$form->message = $this->formProcessor->getSessionMessage($messageName);
		$form->text('name', 'Ваше имя:', $this->request->name);
		$form->text('login', 'Логин:', $this->request->login);
		$form->text('email', 'Email:', $this->request->email);
		$form->password('password', 'Пароль:');
		$form->password('passwordConf', 'Подтвердите пароль:');
		$form->captcha('captcha', 'Введите код с картинки:');
		$form->submit('Регистрация');

		$form->addJSV('name', $this->jsValidator->name());
		$form->addJSV('login', $this->jsValidator->login());
		$form->addJSV('email', $this->jsValidator->email());
		$form->addJSV('password', $this->jsValidator->password('passwordConf'));
		$form->addJSV('captcha', $this->jsValidator->captcha());

		$this->render($form);
	}

	public function actionSregister()
	{
		if(!isset($_COOKIE['sregister']))
		{
			$this->accessDenied();
		}

		$this->title = 'Регистрация на сайте ' . Config::SITENAME;
		$this->metaDesc = 'Регистрация на сайте ' . Config::SITENAME . '.';
		$this->metaKey = 'регистрация сайт ' . mb_strtolower(Config::SITENAME) . ', зарегистрироваться сайт ' . mb_strtolower(Config::SITENAME);

		$hornav = $this->getHornav();
		$hornav->addData('Регистрация');

		$pageMessage = new PageMessage();
		$pageMessage->hornav = $hornav;
		$pageMessage->header = 'Регистрация';
		$pageMessage->text = 'Учетная запись создана. На указанный Вами адрес электронной почты отправлено письмо с инструкцией по активации. Если письмо не доходит, то обратитесь к администрации. <br> Данная страница станет недоступна в течении 60 секунд.';

		$this->render($pageMessage);
	}

	public function actionActivate()
	{
		$userDB = new UserDB();
		$userDB->loadOnLogin($this->request->login);
		$hornav = $this->getHornav();

		if(!$userDB->login || !$this->request->key)
        {
            $this->accessDenied();
        }

		if($userDB->isSaved() && ($userDB->activation == ''))
		{
			$this->title = 'Ваш аккаунт уже активирован!';
			$this->metaDesc = 'Вы можете войти в ваш аккаунт, используя Ваш логин и пароль.';
			$this->metaKey = 'активация, успешная активация, успешная активация регистрация';
			$hornav->addData('Активация');
		}
		elseif($userDB->activation != $this->request->key)
		{
			$this->title = 'Ошибка при активации';
			$this->metaDesc = 'Неверный код активации, если ошибка будет повторяться, то обратитесь к администратору.';
			$this->metaKey = 'активация, ошибка активация, ошибка активация регистрация';
			$hornav->addData('Ошибка активации');
		}
		elseif($userDB->isSaved() && ($userDB->activation == $this->request->key))
		{
			$userDB->activation = '';

			try
			{
				$userDB->save();
			}
			catch (\Exception $e)
			{
				throw new \Exception($e);
			}

			$this->title = "Ваш аккаунт успешно активирован";
			$this->metaDesc = "Теперь Вы можете войти в свою учётную запись, используя Ваши логин и пароль.";
			$this->metaKey = "активация, успешная активация, успешная активация регистрация";
			$hornav->addData("Активация");
		}

		$pageMessage = new PageMessage();
		$pageMessage->hornav = $hornav;
		$pageMessage->header = $this->title;
		$pageMessage->text = $this->metaDesc;

		$this->render($pageMessage);
	}

	public function actionLogout()
	{
		UserDB::logout();
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
}