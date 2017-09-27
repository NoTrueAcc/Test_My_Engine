<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 11:30
 */

namespace core;
use core\database\AbstractObjectDB;
use core\exception\ValidatorException;


/**
 * Класс для работы с формами
 *
 * Class FormProcessor
 * @package core
 */
class FormProcessor
{
	private $request;
	private $message;

	/**
	 * Задает свойствам объекты
	 *
	 * FormProcessor constructor.
	 * @param Request $request
	 * @param Message $message
	 */
	public function __construct($request, $message)
	{
		$this->request = $request;
		$this->message = $message;
	}

	/**
	 * Сохраняет данные формы и записывает в сессию сообщение
	 *
	 * @param string $formName имя формы
	 * @param AbstractObjectDB $obj объект формы
	 * @param array $fieldsDataList  массив дынных формы
	 * @param array $checks массив эквивалентности
	 * @param bool $successMessage сообщение об успешной отправке формы
	 * @return null
	 */
	public function process($formName, $obj, array $fieldsDataList, array $checks = array(), $successMessage = false)
	{
		try
		{
			if(is_null($this->checks($formName, $checks)))
			{
				return null;
			}

			foreach ($fieldsDataList as $fieldData)
			{
				if(is_array($fieldData))
				{
					$field = $fieldData[0];
					$value = $fieldData[1];

					if(strpos($field, '()'))
					{
						$field = str_replace('()', '', $field);
						$obj->{$field}($value);
					}
					else
					{
						$obj->{$field} = $value;
					}
				}
				else
				{
					$obj->{$fieldData} = $this->request->{$fieldData};
				}
			}

			if($obj->save())
			{
				if($successMessage)
				{
					$this->setSessionMessage($formName, $successMessage);
				}
			}

			return $obj;
		}
		catch (\Exception $e)
		{
			$this->setSessionMessage($formName, $this->getError($e));
		}
	}

	/**
	 * Проверяет значения массива $checks на эквивалентность
	 *
	 * @param string $formName имя формы
	 * @param array $checks массив эквивалентности
	 * @return bool|null
	 */
	public function checks($formName, array $checks)
	{
		try
		{
			for($i = 0; $i < count($checks); $i++)
			{
				$equal = isset($checks[$i][3]) ? $checks[$i][3] : true;

				if($equal && ($checks[$i][0] != $checks[$i][1]))
				{
					throw new \Exception($checks[$i][2]);
				}
				elseif (!$equal && ($checks[$i][0] == $checks[$i][1]))
				{
					throw new \Exception($checks[$i][2]);
				}
			}

			return true;
		}
		catch (\Exception $e)
		{
			$this->setSessionMessage($formName, $this->getError($e));

			return null;
		}
	}

	/**
	 * Авторизация пользователя в системе
	 *
	 * @param string $formName название формы
	 * @param string $obj название класса юзеров
	 * @param string $method название метода авторизации
	 * @param string $login логин пользователя
	 * @param string $password пароль пользователя
	 * @return bool
	 */
	public function auth($formName, $obj, $method, $login, $password)
	{
		try
		{
			$user = $obj::$method($login, $password);

			return $user;
		}
		catch (\Exception $e)
		{
			$this->setSessionMessage($formName, $this->getError($e));

			return false;
		}
	}

	/**
	 * Записывает сообщение в сессию
	 *
	 * @param string $formName имя формы
	 * @param string $message сообщение
	 */
	public function setSessionMessage($formName, $message)
	{
		if(!session_id())
		{
			session_start();
		}

		$_SESSION['message'] = array($formName => $message);
	}

	/**
	 * Возвращает сообщение формы
	 *
	 * @param string $formName имя формы
	 * @return bool|string
	 */
	public function getSessionMessage($formName)
	{
		if(!session_id())
		{
			session_start();
		}

		$message = isset($_SESSION['message'][$formName]) ? $_SESSION['message'][$formName] : null;

		if(is_null($message))
		{
			return false;
		}

		unset($_SESSION['message'][$formName]);

		return $this->message->getMessage($message);
	}

	/**
	 * Возвращает сообщение об ошибке
	 *
	 * @param \Exception $e
	 * @return mixed
	 */
	private function getError($e)
	{
		if($e instanceof ValidatorException)
		{
			$error = current($e->getErrors());

			return $error[0];
		}
		elseif ($message = $e->getMessage())
		{
			return $message;
		}

		return 'UNKNOWN_ERROR';
	}
}