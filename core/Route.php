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

		print_r($controllerAndAction);
		try
		{
			if(class_exists($controllerName))
			{
				$controller = new $controllerName;

				if(method_exists($controller, $actionName))
				{
					$controller->$actionName();
				}
			}
		}
		catch (\Exception $e)
		{
			if($e->getMessage() !== 'ACCESS_DENIED')
			{
				throw new \Exception('ERROR_404');
			}
		}
	}
}