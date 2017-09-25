<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 13:12
 */

namespace core;

/**
 * Класс маршрутизатор
 *
 * Class Route
 * @package core
 */
class Route
{
	/**
	 * Получаем контроллер и действие и выполняем его
	 */
	public static function route()
	{
		$controllerAndAction = Url::getControllerAndAction();
		$controllerName = $controllerAndAction[0] . 'Controller';
		$actionName = 'action' . $controllerAndAction[1];

		try
		{
			if(class_exists($controllerName))
			{
				$controller = new $controllerName;
			}

			if(method_exists($controller, $actionName))
			{
				$controller->$actionName();
			}
			else
			{
				throw new \Exception();
			}
		}
		catch (\Exception $e)
		{
			if($e->getMessage() !== 'ACCESS_DENIED')
			{
				$controller->action404();
			}
		}
	}
}