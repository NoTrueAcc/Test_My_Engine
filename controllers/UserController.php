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
use modules\Form;
use modules\PageMessage;
use objects\UserDB;

class UserController extends AbstractController
{
	public function actionEditProfile()
	{
		$messageAvatar = "avatar";
		$messageName = "name";
		$messagePassword = "password";
		$messageEmail = "email";

		if($this->request->change_avatar)
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
		elseif ($this->request->change_name)
		{
			$checks = array(array($this->authUser->checkPassword($this->request->passwordCurrentName), true, 'ERROR_PASSWORD_CURRENT'));
			$user = $this->formProcessor->process($messageName, $this->authUser, array('name'), $checks, 'SUCCESS_NAME_CHANGE');

			if($user instanceof UserDB)
			{
				$this->redirect(Url::currentUrl());
			}
		}
		elseif($this->request->change_password)
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
		elseif($this->request->change_email)
        {
            $checks = array(array($this->authUser->checkPassword($this->request->passwordCurrentEmail), true, 'ERROR_PASSWORD_CURRENT'));
            $user = $this->formProcessor->process($messageEmail, $this->authUser, array(), $checks, 'SUCCESS_EMAIL_CHANGE');

            if($user instanceof UserDB)
            {
                $this->mail->send($user->email, array('user' => $user,
                    'link' => Url::getUrl('email/confirm', 'user', array('login' => $user->login, 'email' => $this->request->email, 'key' => $user->hashUserLoginAndEmail()), false, Config::ADDRESS)),
                    'change_email');
                $this->redirect(Url::currentUrl());
            }
        }

		$this->title = "Редактирование профиля";
		$this->meta_desc = "Редактирование профиля пользователя.";
		$this->meta_key = "редактирование профиля, редактирование профиля пользователя, редактирование профиля пользователя сайт";

		$formAvatar = new Form();
		$formAvatar->name = 'change_avatar';
		$formAvatar->action = Url::currentUrl();
		$formAvatar->enctype = 'multipart/form-data';
		$formAvatar->message = $this->formProcessor->getSessionMessage($messageAvatar);
		$formAvatar->file('avatar', 'Аватар');
		$formAvatar->submit('Сохранить');

		$formAvatar->addJSV('avatar', $this->jsValidator->avatar());

		$formName = new Form();
		$formName->name = 'change_name';
		$formName->header = 'Изменить имя';
		$formName->action = Url::currentUrl();
		$formName->message = $this->formProcessor->getSessionMessage($messageName);
		$formName->text('name', 'Ваше имя:', $this->authUser->name);
		$formName->password('passwordCurrentName', 'Текущий пароль:');
		$formName->submit('Сохранить');

		$formName->addJSV('name', $this->jsValidator->name());
		$formName->addJSV('passwordCurrentName', $this->jsValidator->password(false, false, 'ERROR_PASSWORD_CURRENT_EMPTY'));

		$formPassword = new Form();
		$formPassword->name = 'change_password';
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
		$formEmail->name = 'change_email';
		$formEmail->header = 'Изменить Email';
		$formEmail->action = Url::currentUrl();
		$formEmail->message = $this->formProcessor->getSessionMessage($messageEmail);
		$formEmail->text('email', 'Новый email:', $this->authUser->email);
		$formEmail->password('passwordCurrentEmail', 'Текущий пароль:');
		$formEmail->submit('Сохранить');

		$formEmail->addJSV('passwordCurrentEmail', $this->jsValidator->password(false, false, 'ERROR_PASSWORD_CURRENT_EMPTY'));
		$formEmail->addJSV('email', $this->jsValidator->email());

		$hornav = $this->getHornav();
		$hornav->addData('Редактирование профиля');

		$this->render($this->renderData(array('hornav' => $hornav, 'formAvatar' => $formAvatar, 'formName' => $formName, 'formPassword' => $formPassword, 'formEmail' => $formEmail), 'profile', array('avatar' => $this->authUser->avatar, 'maxSize' =>(Config::MAX_SIZE_AVATAR / KB_B))));
	}

	public function actionEmailConfirm()
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

            if($userDB->hashUserLoginAndEmail() == $this->request->key)
            {
                $userDB = $this->formProcessor->process('email', $userDB, array('email', $this->request->email), array());

                if($userDB instanceof UserDB)
                {
                    $pageMessage->hornav = $hornav;
                    $pageMessage->header = 'Email-адрес успешно изменен';
                    $pageMessage->text = 'Вы успешно подтвердили изменение email-адреса!';

                    $this->render($pageMessage);
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
                $pageMessage->text = 'Попробуйте еще раз. Проверьте корректность введенного адреса. При повторении ошибки обратитесь в администратору';

                $this->render($pageMessage);
            }
        }
        else
        {
            $this->accessDenied();
        }
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