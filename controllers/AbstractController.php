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
use modules\PageMessage;

class AbstractController extends \core\controller\AbstractController
{
	protected $title;
	protected $metaDesc;
	protected $metaKey;
	protected $mail = null;
	protected $urlActive;
	protected $sectionId = 0;

	public function __construct()
	{
		parent::__construct(new View(Config::DIR_TMPL), new Message(Config::FILE_MESSAGES));
		$this->mail = new Mail();
		$this->urlActive = Url::deletePage(Url::currentUrl(Config::ADDRESS));
	}

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

	protected function getHeader()
	{

	}
}