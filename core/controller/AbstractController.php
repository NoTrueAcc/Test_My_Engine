<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 20.09.17
 * Time: 9:19
 */

namespace core\controller;


use core\FormProcessor;
use core\Message;
use core\Request;
use core\View;
use library\JavaScriptValidator;

/**
 * Класс контроллер
 *
 * Class AbstractController
 * @package core\controller
 */
abstract class AbstractController
{
	protected $view;
	protected $message;
	protected $request;
	protected $formProcessor = null;
	protected $authUser = null;
	protected $jsValidator = null;

	/**
	 * AbstractController constructor.
	 * @param View $view
	 * @param Message $message
	 */
	public function __construct($view, $message)
	{
		$this->view = $view;
		$this->request = new Request();
		$this->formProcessor = new FormProcessor($this->request, $message);
		$this->jsValidator = new JavaScriptValidator($message);
		$this->authUser = $this->authUser();
		$this->message = $message;

		if(!$this->access())
		{
			$this->accessDenied();
			throw new \Exception('ACCESS_DENIED');
		}
	}

	abstract protected function render($center);
	abstract protected function accessDenied();
	abstract protected function action404();

	/**
	 * По умолчанию пользователь не авторизован
	 *
	 * @return null
	 */
	protected function authUser()
	{
		return null;
	}

	/**
	 * По умолчанию доступ к странице есть
	 *
	 * @return bool
	 */
	protected function access()
	{
		return true;
	}

	/**
	 * Страница не найдена
	 */
	final protected function notFound()
	{
		$this->action404();
	}

	/**
	 * Редирект на страницу
	 *
	 * @param string $url страница
	 */
	final protected function redirect($url)
	{
		header("Location: $url");
		exit;
	}

	/**
	 * Группирует входящие данные, подставляет в шаблон и возвращает его
	 *
	 * @param array $modules массив данных
	 * @param string $layout шаблон
	 * @param array $params параметры
	 * @return bool|string
	 */
	final protected function renderData(array $modules, $layout, array $params = array())
	{
		if(!is_array($modules))
		{
			return false;
		}

		foreach ($modules as $module => $data)
		{
			$params[$module] = $data;
		}

		return $this->view->render($layout, $params, true);
	}
}