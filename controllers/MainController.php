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
use modules\SearchResult;
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
	/**
	 * Главная страница
	 */
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

	/**
	 * Страница секции /section
	 */
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

	/**
	 * Страница категории
	 */
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

	/**
	 * страница статьи
	 */
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

	/**
	 * страница голосования
	 */
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

	/**
	 * страница регистрации
	 */
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
				$this->mail->send($user->email, array('user' => $user,
					'link' => Url::getUrl('activate', '', array('login' => $user->login, 'key' => $user->activation),
						false, Config::ADDRESS)), 'register');

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

	/**
	 * страница успешной регистрации
	 */
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

	/**
	 * страница активации учетки
	 *
	 * @throws \Exception
	 */
	public function actionActivate()
	{
		if($this->request->activateResend)
		{
			$this->title = 'Подтверждение e-mail';
			$this->metaDesc = 'Подтверждение e-mail.';
			$this->metaKey = 'подтверждение e-mail,подтверждение e-mail сайт';

			$hornav = $this->getHornav();
			$hornav->addData('Подтверждение e-mail');

			$formEmail = new Form();
			$formEmail->hornav = $hornav;
			$formEmail->name = 'emailActivate';
			$formEmail->header = 'Подтверждение e-mail';
			$formEmail->action = Url::getUrl('activate');
			$formEmail->message = $this->formProcessor->getSessionMessage('email');
			$formEmail->text('email', 'Для повторной отправки инструкции по активации введите E-mail, указанный при регистрации:', $this->request->email);
			$formEmail->submit('Отправить');

			$formEmail->addJSV('email', $this->jsValidator->email());

			$this->render($formEmail);
		}
		elseif($this->request->emailActivate)
		{
			$user = new UserDB();
			$user->loadOnEmail($this->request->email);

			if($user->isSaved())
			{
				try
				{
					$this->mail->send($user->email,
						array('user' => $user, 'link' => Url::getUrl('activate', '', array('login' => $user->login, 'key' => $user->activation), false, Config::ADDRESS)),
						'register');

					$this->formProcessor->setSessionMessage('email', 'UNKNOWN_ERROR');
				}
				catch (\Exception $e)
				{
					$this->formProcessor->setSessionMessage('email', 'UNKNOWN_ERROR');

					$this->redirect(Url::currentUrl());
				}
			}
		}
		else
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
			elseif($userDB->isSaved() && ($userDB->activation === $this->request->key))
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
	}

	/**
	 * действие выход
	 */
	public function actionLogout()
	{
		UserDB::logout();
		$this->redirect($_SERVER['HTTP_REFERER']);
	}

	/**
	 * страница изменения e-mail
	 */
	public function actionConfirm()
	{
		$this->title = 'Подтверждение изменения email-адреса';
		$this->metaDesc = 'Подтверждение изменения email-адреса.';
		$this->metaKey = 'подтверждение изменения email-адреса,изменение email,смена email';

		$pageMessage = new PageMessage();
		$hornav = $this->getHornav();
		$hornav->addData('Подтверждение изменения email-адреса');

		if($this->request->login && $this->request->email && $this->request->key)
		{
			$userDB = new UserDB();
			$userDB->loadOnLogin($this->request->login);

			if($userDB->isSaved() && $userDB->isActive() && ($userDB->hashUserDataOnEmail($this->request->email) == $this->request->key))
			{
				$user = $this->formProcessor->process('', $userDB, array('email', $this->request->email),array());

				if($user instanceof UserDB)
				{
					Url::setCookiePageAccess('sconfirm');
					$this->redirect('sconfirm');
				}
				else
				{
					$pageMessage->hornav = $hornav;
					$pageMessage->header = 'При смене email-адреса произошла ошибка';
					$pageMessage->text = 'Попробуйте еще раз. При повторении ошибки обратитесь в администратору';

					$this->render($pageMessage);
				}
			}
			else
			{
				$pageMessage->hornav = $hornav;
				$pageMessage->header = 'При смене email-адреса произошла ошибка';
				$pageMessage->text = 'Проверьте корректность введенного адреса. При повторении ошибки обратитесь в администратору';

				$this->render($pageMessage);
			}
		}
		else
		{
			$this->accessDenied();
		}
	}

	/**
	 * страница успешного изменения e-mail
	 */
	public function actionSconfirm()
	{
		if(!isset($_COOKIE['sconfirm']))
		{
			$this->accessDenied();
		}

		$this->title = 'Изменение e-mail';
		$this->metaDesc = 'Изменение e-mail сайте ' . Config::SITENAME . '.';
		$this->metaKey = 'изменение e-mail сайт ' . mb_strtolower(Config::SITENAME) . ',изменение email успешно';

		$hornav = $this->getHornav();
		$hornav->addData('Изменение e-mail');

		$pageMessage = new PageMessage();
		$pageMessage->hornav = $hornav;
		$pageMessage->header = 'Изменение e-mail';
		$pageMessage->text = 'Ваш e-mail успешно изменен';

		$this->render($pageMessage);
	}

	/**
	 * страница восстановления пароля
	 */
	public function actionReset()
	{
		$messageName = 'reset';
		$this->title = 'Восстановление пароля';
		$this->metaDesc = "Восстановление пароля пользователя.";
		$this->metaKey = "восстановление пароля, восстановление пароля пользователя";
		$hornav = $this->getHornav();
		$hornav->addData('Восстановление пароля');

		if($this->request->reset && $this->request->email)
		{
			$userDB = new UserDB();
			$userDB->loadOnEmail($this->request->email);

			if($userDB->isSaved() && $userDB->isActive())
			{
				$this->mail->send(
								$this->request->email,
								array(
									'user' => $userDB,
									'link' => Url::getUrl('reset', '', array('email' => $this->request->email, 'key' => $userDB->hashUserDataOnEmail($this->request->email)), false, Config::ADDRESS)),
									'reset'
								);
				$pageMessage = new PageMessage();
				$pageMessage->hornav = $hornav;
				$pageMessage->header = 'Восстановление пароля';
				$pageMessage->text = 'Инструкция по восстановлению пароля выслана на указанный e-mail адрес';

				$this->render($pageMessage);
			}
			else
			{
				if($userDB->isSaved())
				{
					$pageMessage = new PageMessage();
					$pageMessage->hornav = $hornav;
					$pageMessage->header = 'Восстановление пароля';
					$pageMessage->text = 'Ваш e-mail адрес не подтвержден, для продолжения необходимо подтвердить его! Для повторной отправки инструкции по активации пройдите по <a href="' .
						Url::getUrl('activate', '', array('activateResend' => 'redirect', 'email' => $this->request->email)) . '">ссылке</a>';

					$this->render($pageMessage);
				}
				else
				{
					$this->formProcessor->setSessionMessage($messageName, 'ERROR_EMAIL_NOT_EXISTS');
				}


				$this->redirect(Url::currentUrl());
			}
		}
		elseif($this->request->email && $this->request->key)
		{
			$userDB = new UserDB();
			$userDB->loadOnEmail($this->request->email);

			if($userDB->isSaved() && $userDB->isActive() && ($userDB->hashUserDataOnEmail($this->request->email) === $this->request->key))
			{
				if($this->request->resetPassword)
				{
					$checks = array(array($this->request->passwordReset, $this->request->passwordResetConf, 'ERROR_PASSWORD_CONF'));
					$userDB = $this->formProcessor->process($messageName, $userDB, array(array('setPassword()', $this->request->passwordReset)), $checks);

					if($userDB instanceof UserDB)
					{
						$userDB->login();

						$this->redirect(Url::getUrl('sreset'));
					}
				}

				$form = new Form();
				$form->header = 'Восстановление пароля';
				$form->name = 'resetPassword';
				$form->action = Url::currentUrl();
				$form->message = $this->formProcessor->getSessionMessage($messageName);
				$form->password('passwordReset', 'Новый пароль:');
				$form->password('passwordResetConf', 'Повторите пароль:');
				$form->submit('Восстановить');

				$form->addJSV('passwordReset', $this->jsValidator->password('passwordResetConf'));

				$this->render($form);
			}
			else
			{
				$pageMessage = new PageMessage();
				$pageMessage->hornav = $hornav;
				$pageMessage->header = 'Ошибка при восстановлении пароля';
				$pageMessage->text = 'При восстановлении пароля возникла ошибка, проверьте правильно ли введен адрес и повторите попытку. <br> Если ощибка будет повторяться,- обратитесь к администратору.';

				$this->render($pageMessage);
			}
		}
		else
		{
			$form = new Form();
			$form->hornav = $hornav;
			$form->header = 'Восстановление пароля';
			$form->name = 'reset';
			$form->action = Url::currentUrl();
			$form->message = $this->formProcessor->getSessionMessage($messageName);
			$form->text('email', 'Введите e-mail, указанный при регистрации:', $this->request->email);
			$form->submit('Восстановить');
			$form->addJSV('email', $this->jsValidator->email());

			$this->render($form);
		}
	}

	/**
	 * страница успешного восстановления пароля
	 */
	public function actionSreset()
	{
		$this->title = "Восстановление пароля";
		$this->metaDesc = "Восстановление пароля успешно завершено.";
		$this->metaKey = "восстановление пароля, восстановление пароля пользователя, восстановление пароля пользователя завершено";

		$hornav = $this->getHornav();
		$hornav->addData('Восстановление пароля');

		$pageMessage = new PageMessage();
		$pageMessage->hornav = $hornav;
		$pageMessage->header = 'Восстановление пароля';
		$pageMessage->text = "Теперь Вы можете войти на сайт, используя новый пароль, если Вы не авторизовались автоматически.";

		$this->render($pageMessage);
	}

	/**
	 * страница восстановления логина
	 */
	public function actionRemind()
	{
		$messageName = 'remind';
		$this->title = "Восстановление логина";
		$this->meta_desc = "Восстановление логина пользователя.";
		$this->meta_key = "восстановление логина, восстановление логина пользователя";
		$hornav = $this->getHornav();
		$hornav->addData('Восстановление логина');

		if($this->request->remind)
		{
			$userDB = new UserDB();
			$userDB->loadOnEmail($this->request->email);

			if($userDB->isSaved() && $userDB->isActive())
			{
				$this->mail->send($userDB->email, array("user" => $userDB), "remind");

				$pageMessage = new PageMessage();
				$pageMessage->header = 'Успешное восстановление логина';
				$pageMessage->text = 'На Ваш e-mail было выслано письмо с логином.';

				$this->render($pageMessage);
			}
			elseif($userDB->isSaved())
			{
					$pageMessage = new PageMessage();
					$pageMessage->hornav = $hornav;
					$pageMessage->header = 'Восстановление логина';
					$pageMessage->text = 'Ваш e-mail адрес не подтвержден, для продолжения необходимо подтвердить его! Для повторной отправки инструкции по активации пройдите по <a href="' .
						Url::getUrl('activate', '', array('activateResend' => 'redirect', 'email' => $this->request->email)) . '">ссылке</a>';

					$this->render($pageMessage);
			}
			else
			{
				$this->formProcessor->setSessionMessage($messageName, 'ERROR_EMAIL_NOT_EXISTS');

				$this->redirect(Url::currentUrl());
			}
		}
		else
		{
			$form = new Form();
			$form->hornav = $hornav;
			$form->header = 'Восстановление логина';
			$form->name = 'remind';
			$form->action = Url::currentUrl();
			$form->message = $this->formProcessor->getSessionMessage($messageName);
			$form->text('email', 'Введите e-mail, указанный при регистрации:', $this->request->email);
			$form->submit('Восстановить');
			$form->addJSV('email', $this->jsValidator->email());

			$this->render($form);
		}
	}

	public function actionSearch()
	{
		$hornav = $this->getHornav();
		$hornav->addData('Поиск:');
		$this->title = 'Поиск: ' . $this->request->query;
		$this->metaDesc = "Поиск ".$this->request->query.".";
		$this->metaKey = "поиск, поиск ".$this->request->query;

		$articles = ArticleDB::search($this->request->query);
		$searchResult = new SearchResult();

		if(mb_strtolower($this->request->query) < Config::MIN_SEARCH_LEN)
		{
			$searchResult->errorLen = true;
		}

		$searchResult->hornav = $hornav;
		$searchResult->field = 'full';
		$searchResult->query = $this->request->query;
		$searchResult->data = $articles;

		$this->render($searchResult);
	}
}