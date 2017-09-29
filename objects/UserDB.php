<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 22.09.17
 * Time: 14:15
 */

namespace objects;


use library\config\Config;
use library\database\ObjectDB;
use library\database\SelectDB;

/**
 * Класс для работы с таблицей users
 *
 * Class UserDB
 * @package objects
 */
class UserDB extends ObjectDB
{
	protected static $table = 'users';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('login', 'ValidateLogin');
		$this->addProperty('email', 'ValidateEmail');
		$this->addProperty('password', 'ValidatePassword');
		$this->addProperty('name', 'ValidateName');
		$this->addProperty('avatar', 'ValidateImg');
		$this->addProperty('dateReg', 'ValidateDate', self::TYPE_TIMESTAMP, $this->getDate());
		$this->addProperty('activation', 'ValidateActivation', null, $this->getKey());
	}

	/**
	 * Устанавливает новый пароль
	 *
	 * @param $password
	 */
	public function setPassword($password)
	{
		$this->newPassword = $password;
	}

	/**
	 * Возвращает новый пароль
	 *
	 * @return int|null
	 */
	public function getPassword()
	{
		return $this->newPassword;
	}

	/**
	 * Возвращает название аватара пользователя
	 *
	 * @return null|string
	 */
	public function getAvatar()
	{
		$avatar = basename($this->avatar);

		if($avatar != Config::DEFAULT_AVATAR)
		{
			return $avatar;
		}

		return null;
	}

	/**
	 * Инициализирует объект по email
	 *
	 * @param string $email email
	 * @return bool
	 */
	public function loadOnEmail($email)
	{
		return $this->loadOnField('email', $email);
	}

	/**
	 * Инициализирует объект по логину
	 *
	 * @param string $login логин
	 * @return bool
	 */
	public function loadOnLogin($login)
	{
		return $this->loadOnField('login', $login);
	}

	/**
	 * Авторизация
	 *
	 * @return bool
	 */
	public function login()
	{
		if(is_null($this->activation))
		{
			return false;
		}

		if(!session_id())
		{
			session_start();
		}

		$_SESSION['auth_login'] = $this->login;
		$_SESSION['auth_password'] = $this->password;
	}

	/**
	 * Выход
	 */
	public static function logout()
	{
		if(!session_id())
		{
			session_start();
		}

		if(isset($_SESSION['auth_login']) && isset($_SESSION['auth_password']))
		{
			unset($_SESSION['auth_login']);
			unset($_SESSION['auth_password']);
		}
	}

	/**
	 * Проверяет авторизован ли пользовватель или проверяет данные автоизованного пользователя
	 *
	 * @param bool|string $login логин
	 * @param bool|string $password пароль
	 * @return UserDB
	 * @throws \Exception
	 */
	public static function authUser($login = false, $password = false)
	{
		if($login || $password)
		{
			$auth = true;
		}
		else
		{
			if(!session_id())
			{
				session_start();
			}

			if(isset($_SESSION['auth_login']) && isset($_SESSION['auth_password']))
			{
				$login = $_SESSION['auth_login'];
				$password = $_SESSION['auth_password'];
			}
			else
			{
				return null;
			}

			$auth = false;
		}

		$select = new SelectDB();
		$select->from(self::$table, 'password')
			->where('`login` = ' . self::$db->getSQ(), $login);

		$passwordHash = self::$db->selectCell($select);

		if($auth)
		{
			$checkPassword = password_verify($password, $passwordHash);
		}
		else
		{
			$checkPassword = ($password == $passwordHash);
		}

		if($passwordHash && $checkPassword)
		{
			$user = new UserDB();
			$user->loadOnLogin($login);

			if($user->activation != '')
			{
				throw new \Exception('ERROR_ACTIVATE_USER');
			}

			if($auth)
			{
				$user->login();
			}

			return $user;
		}

		if($auth)
		{
			throw new \Exception('ERROR_AUTH_USER');
		}
	}

	/**
	 * проверяет соответствие введенного пароля паролю авторизованного пользователя
	 *
	 * @param string $password пароль для сравнения
	 * @return bool
	 */
	public function checkPassword($password)
	{
		return password_verify($password, $this->password);
	}

    /**
     * Хэширует текущие логин и емэил пользователя
     *
     * @return string
     */
    public function hashUserLoginAndEmail()
    {
        return md5($this->login . $this->email, Config::SECRET);
    }

	/**
	 * Пост инициализация
	 *
	 * @return bool
	 */
	protected function postInit()
	{
		if(is_null($this->avatar))
		{
			$this->avatar = Config::DEFAULT_AVATAR;
		}

		$this->avatar = Config::DIR_AVATAR . $this->avatar;

		return true;
	}

	/**
	 * Пре валидация
	 *
	 * @return bool
	 */
	protected function preValidate()
	{
		if($this->avatar == Config::DIR_AVATAR . Config::DEFAULT_AVATAR)
		{
			$this->avatar = null;
		}

		if(!is_null($this->avatar))
		{
			$this->avatar = basename($this->avatar);
		}

		if(!is_null($this->newPassword))
		{
			$this->password = $this->newPassword;
		}

		return true;
	}

	/**
	 * Пост валидация
	 */
	protected function postValidate()
	{
		if(!is_null($this->newPassword))
		{
			$this->password = self::hash($this->newPassword);
		}

		return true;
	}
}