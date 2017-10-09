<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 28.09.17
 * Time: 9:10
 */

namespace controllers;

use core\File;
use core\Url;
use library\config\Config;
use modules\Chat;
use modules\Form;
use objects\UserDB;

class UserController extends AbstractController
{
	/**
	 * страница редактирования профиля
	 */
	public function actionEditProfile()
	{
		$messageAvatar = "avatar";
		$messageName = "name";
		$messagePassword = "password";
		$messageEmail = "email";

		if($this->request->changeAvatar)
		{
			$img = $this->formProcessor->uploadImg($messageAvatar, $_FILES['avatar'], Config::MAX_SIZE_AVATAR, Config::DIR_AVATAR);

			if($img)
			{
				$tmp = $this->authUser->getAvatar();
				$user = $this->formProcessor->process($messageAvatar, $this->authUser, array(array('avatar', $img)), array(), 'SUCCESS_AVATAR_CHANGE');

				if($user instanceof UserDB)
				{
					if($tmp)
					{
						File::deleteFile(Config::DIR_AVATAR . $tmp);
					}

					$this->redirect(Url::currentUrl());
				}
			}
		}
		elseif ($this->request->changeName)
		{
			$checks = array(array($this->authUser->checkPassword($this->request->passwordCurrentName), true, 'ERROR_PASSWORD_CURRENT'));
			$user = $this->formProcessor->process($messageName, $this->authUser, array('name'), $checks, 'SUCCESS_NAME_CHANGE');

			if($user instanceof UserDB)
			{
				$this->redirect(Url::currentUrl());
			}
		}
		elseif($this->request->changePassword)
		{
			$checks = array(
				array($this->authUser->checkPassword($this->request->passwordCurrent), true, 'ERROR_PASSWORD_CURRENT'),
				array($this->request->password, $this->request->passwordConf, true, 'ERROR_PASSWORD_CONF')
			);
			$user = $this->formProcessor->process($messagePassword, $this->authUser, array(array('setPassword()', $this->request->password)), $checks, 'SUCCESS_PASSWORD_CHANGE');

			if($user instanceof UserDB)
			{
				$this->authUser->login();
				$this->redirect(Url::currentUrl());
			}
		}
		elseif($this->request->changeEmail)
        {
			$userOld = new UserDB();
			$userOld->loadOnEmail($this->request->email);

            $checks = array(
            	array($this->authUser->checkPassword($this->request->passwordCurrentEmail), true, 'ERROR_PASSWORD_CURRENT'),
				array($userOld->isSaved(), false, 'ERROR_EMAIL_ALREADY_EXISTS')
							);
            $user = $this->formProcessor->process($messageEmail, $this->authUser, array(), $checks, 'SUCCESS_EMAIL_CHANGE');

            if($user instanceof UserDB)
            {
                $this->mail->send(
								$user->email,
								array(
								'user' => $user,
								'link' => Url::getUrl('confirm',
									'',
									array('login' => $user->login, 'email' => $this->request->email, 'key' => $user->hashUserDataOnEmail($this->request->email)),
									false, Config::ADDRESS)),
								'change_email'
								);
                $this->redirect(Url::currentUrl());
            }
        }

		$this->title = "Редактирование профиля";
		$this->meta_desc = "Редактирование профиля пользователя.";
		$this->meta_key = "редактирование профиля, редактирование профиля пользователя, редактирование профиля пользователя сайт";

		$formAvatar = new Form();
		$formAvatar->name = 'changeAvatar';
		$formAvatar->action = Url::currentUrl();
		$formAvatar->enctype = 'multipart/form-data';
		$formAvatar->message = $this->formProcessor->getSessionMessage($messageAvatar);
		$formAvatar->file('avatar', 'Аватар');
		$formAvatar->submit('Сохранить');

		$formAvatar->addJSV('avatar', $this->jsValidator->avatar());

		$formName = new Form();
		$formName->name = 'changeName';
		$formName->header = 'Изменить имя';
		$formName->action = Url::currentUrl();
		$formName->message = $this->formProcessor->getSessionMessage($messageName);
		$formName->text('name', 'Ваше имя:', $this->authUser->name);
		$formName->password('passwordCurrentName', 'Текущий пароль:');
		$formName->submit('Сохранить');

		$formName->addJSV('name', $this->jsValidator->name());
		$formName->addJSV('passwordCurrentName', $this->jsValidator->password(false, false, 'ERROR_PASSWORD_CURRENT_EMPTY'));

		$formPassword = new Form();
		$formPassword->name = 'changePassword';
		$formPassword->header = 'Изменить пароль';
		$formPassword->action = Url::currentUrl();
		$formPassword->message = $this->formProcessor->getSessionMessage($messagePassword);
		$formPassword->password('password', 'Новый пароль:');
		$formPassword->password('passwordConf', 'Повторите пароль:');
		$formPassword->password('passwordCurrent', 'Текущий пароль:');
		$formPassword->submit('Сохранить');

		$formPassword->addJSV('password', $this->jsValidator->password('passwordConf'));
		$formPassword->addJSV('passwordCurrent', $this->jsValidator->password(false, false, 'ERROR_PASSWORD_CURRENT_EMPTY'));

		$formEmail = new Form();
		$formEmail->name = 'changeEmail';
		$formEmail->header = 'Изменить e-mail';
		$formEmail->action = Url::currentUrl();
		$formEmail->message = $this->formProcessor->getSessionMessage($messageEmail);
		$formEmail->text('email', 'Новый e-mail:', $this->authUser->email);
		$formEmail->password('passwordCurrentEmail', 'Текущий пароль:');
		$formEmail->submit('Сохранить');

		$formEmail->addJSV('passwordCurrentEmail', $this->jsValidator->password(false, false, 'ERROR_PASSWORD_CURRENT_EMPTY'));
		$formEmail->addJSV('email', $this->jsValidator->email());

		$hornav = $this->getHornav();
		$hornav->addData('Редактирование профиля');

		$this->render($this->renderData(
			array('hornav' => $hornav,
				'formAvatar' => $formAvatar,
				'formName' => $formName,
				'formPassword' => $formPassword,
				'formEmail' => $formEmail),
			'profile', array('avatar' => $this->authUser->avatar, 'maxSize' =>(Config::MAX_SIZE_AVATAR / KB_B))));
	}

	public function actionChat()
    {
        $this->title = "Чат пользователей";
        $this->meta_desc = "Чат пользователей.";
        $this->meta_key = "чат пользователей, чат";

        $hornav = $this->getHornav();
        $hornav->addData('Чат пользователей');

        $chat = new Chat();
        $chat->hornav = $hornav;

        $this->render($chat);

    }

	protected function access()
	{
		if($this->authUser)
		{
			return true;
		}

		return false;
	}
}