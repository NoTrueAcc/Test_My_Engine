<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 10:12
 */

namespace controllers;


use core\Message;
use core\Url;
use core\View;
use library\config\Config;
use library\mail\Mail;
use modules\Auth;
use modules\Course;
use modules\Header;
use modules\Hornav;
use modules\MainMenu;
use modules\PageMessage;
use modules\Pagination;
use modules\Poll;
use modules\Quote;
use modules\Slider;
use modules\TopMenu;
use modules\UserPanel;
use objects\CourseDB;
use objects\MenuDB;
use objects\PollDataDB;
use objects\PollDB;
use objects\QuoteDB;

/**
 * Класс контроллер
 *
 * Class AbstractController
 * @package controllers
 */
class AbstractController extends \core\controller\AbstractController
{
	protected $title;
	protected $metaDesc;
	protected $metaKey;
	protected $mail = null;
	protected $activeUrl;
	protected $activeUri;
	protected $sectionId = 0;

	public function __construct()
	{
		parent::__construct(new View(Config::DIR_TMPL), new Message(Config::FILE_MESSAGES));
		$this->mail = new Mail();
		$this->activeUrl = Url::deletePage(Url::currentUrl(Config::ADDRESS));
		$this->activeUri = Url::deletePage(Url::currentUrl(''));
	}

	/**
	 * Ошибка 404
	 */
	public function action404()
	{
		header('HTTP/1.1 404 Not Found');
		header('status: 404 Not Found');
		$this->title = 'Страница не найдена';
		$this->metaDesc = 'Запрошенная страница не найдена';
		$this->metaKey = 'страница не найдена, страница не существует, страница 404';

		$pageMessage = new PageMessage();
		$pageMessage->header = '404 Запрашиваемая страница не найдена';
		$pageMessage->text = 'К сожалению запрошенная страница не существует. Проверьте правильность введенного адреса';
		$this->render($pageMessage);
	}

	/**
	 * Ошибка доступ закрыт
	 */
	public function accessDenied()
	{
		$this->title = 'Доступ закрыт';
		$this->metaDesc = 'Доступ к запрашиваемой странице закрыт';
		$this->metaKey = 'доступ закрыт,доступ к странице закрыт,у вас нет прав на просмотр страницы';

		$pageMessage = new PageMessage();
		$pageMessage->header = 'Доступ к странице закрыт';
		$pageMessage->text = 'У Вас нет доступа к запрашиваемой странице';
		$this->render($pageMessage);
	}

	/**
	 * Отрисовывает страницу
	 *
	 * @param string $center
	 */
	final protected function render($center)
	{
		$params = array();
		$params['header'] = $this->getHeader();
		$params['auth'] = $this->getAuth();
		$params['top'] = $this->getTop();
		$params['slider'] = $this->getSlider();
		$params['left'] = $this->getLeft();
		$params['right'] = $this->getRight();
		$params['center'] = $center;
		$params['linkSearch'] = Url::getUrl('search');

		$this->view->render(Config::LAYOUT, $params);
	}

	/**
	 * Возвращает отрисованный header
	 *
	 * @return string Header
	 */
	protected function getHeader()
	{
		$header = new Header();
		$header->title = $this->title;
		$header->meta('Content-Type', 'text/html; charset=utf-8', true);
		$header->meta('description', $this->metaDesc, false);
		$header->meta('keywords', $this->metaKey, false);
		$header->meta('viewport', 'width=device-width', false);
		$header->favicon = 'favicon.ico';
		$header->css = array('styles/main.css', 'styles/prettify.css');
		$header->js = array('js/jquery.js', 'js/functions.js', 'js/validator.js', 'js/prettify.js');

		return $header;
	}

	/**
	 * Возвращает отрисованный модуль auth
	 *
	 * @return string Auth
	 */
	protected function getAuth()
	{
		if($this->authUser)
		{
			return null;
		}

		$auth = new Auth();
		$auth->message = $this->formProcessor->getSessionMessage('auth');
		$auth->action = Url::currentUrl('', true);
		$auth->linkRegister = Url::getUrl('register');
		$auth->linkReset = Url::getUrl('reset');
		$auth->linkRemind = Url::getUrl('remind');

		return $auth;
	}

	/**
	 * Возвращает отрисованный TopMenu
	 *
	 * @return string TopMenu
	 */
	protected function getTop()
	{
		$topMenu = new TopMenu();
		$topMenu->uri = $this->activeUri;
		$topMenu->items = MenuDB::getTopMenu();

		return $topMenu;
	}

	/**
	 * Возвращает отрисованный Slider
	 *
	 * @return string Slider
	 */
	protected function getSlider()
	{
		$course = new CourseDB();
		$course->loadOnSectionId($this->sectionId, PAY_COURSE);

		$slider = new Slider();
		$slider->course = $course;

		return $slider;
	}

	/**
	 * Возвращает отрисованную левую панель
	 *
	 * @return string
	 */
	protected function getLeft()
	{
		$userPanel = '';

		$mainMenu = new MainMenu();
		$mainMenu->uri = $this->activeUri;
		$mainMenu->items = MenuDB::getMainMenu();

		if($this->authUser)
		{
			$userPanel = new UserPanel();
			$userPanel->user = $this->authUser;
			$userPanel->uri = $this->activeUri;
			$userPanel->addItem('Редактировать профиль', Url::getUrl('editprofile', 'user'));
			$userPanel->addItem('Выход', Url::getUrl('logout'));
		}

		$pollDB = new PollDB();
		$pollDB->loadRandom();

		if($pollDB->isSaved())
		{
			$poll = new Poll();
			$poll->action = Url::getUrl('poll', false, array('id' => $pollDB->id));
			$poll->title = $pollDB->title;
			$poll->data = PollDataDB::getAllOnPollId($pollDB->id);
		}
		else
		{
			$poll = '';
		}

		return $userPanel . $mainMenu . $poll;
	}

	/**
	 * Возвращает отрисованную правую панель
	 *
	 * @return string
	 */
	protected function getRight()
	{
		$courseDB1 = new CourseDB();
		$courseDB1->loadOnSectionId($this->sectionId, FREE_COURSE);
		$courseDB2 = new CourseDB();
		$courseDB2->loadOnSectionId($this->sectionId, ONLINE_COURSE);
		$courses = array($courseDB1, $courseDB2);

		$course = new Course();
		$course->courses = $courses;
		$course->authUser = $this->authUser;

		$quoteDB = new QuoteDB();
		$quoteDB->loadRandom();

		$quote = new Quote();
		$quote->quote = $quoteDB;

		return $course . $quote;
	}

	protected function getHornav()
    {
        $hornav = new Hornav();
        $hornav->addData('Главная страница', Url::getUrl(''));

        return $hornav;
    }

	/**
	 * Возвращает смещение относительно страницы
	 *
	 * @param $countOnPage
	 * @return mixed
	 */
	final protected function getOffset($countOnPage)
	{
		return $countOnPage * ($this->getPage() - 1);
	}

	/**
	 * Возвращает номер страницы
	 *
	 * @return int
	 */
	final protected function getPage()
	{
		$page = $this->request->page ? $this->request->page : 1;

		if($page < 1)
		{
			$this->notFound();
		}

		return $page;
	}

	/**
	 * Возвращает отрисованный paginator
	 *
	 * @param string|int $countElements количество элементов
	 * @param string|int $countOnPage количество элементов на странице
	 * @param bool|string $url ссылка
	 * @return Pagination
	 */
	final protected function getPagination($countElements, $countOnPage, $url = false)
	{
		$countPages = ceil($countElements / $countOnPage);
		$active = $this->getPage();
		$startPage = 0;
		$endPage = 0;

		if(($active > $countPages) && ($active > 1))
		{
			$this->notFound();
		}

		if($countPages > 1)
		{
			$left = $active - 1;

			if($left < floor(Config::COUNT_SHOW_PAGES / 2))
			{
				$startPage = 1;
			}
			else
			{
				$startPage = $active - floor(Config::COUNT_SHOW_PAGES / 2);
			}

			$endPage = $startPage + Config::COUNT_SHOW_PAGES - 1;

			if($endPage > $countPages)
			{
				$startPage -= ($endPage - $countPages);
				$endPage = $countPages;

				if($startPage < 1)
				{
					$startPage = 1;
				}
			}
		}

		$pagination = new Pagination();
		$pagination->url = $url ? $url : Url::deletePage(Url::currentUrl());
		$pagination->urlPage = Url::addPage($url);
		$pagination->countElements = $countElements;
		$pagination->countOnPage = $countOnPage;
		$pagination->countShowPages = Config::COUNT_SHOW_PAGES;
		$pagination->active = $active;
		$pagination->startPage = $startPage;
		$pagination->endPage = $endPage;

		return $pagination;
	}
}