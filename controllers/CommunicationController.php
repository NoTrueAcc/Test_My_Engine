<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 11.10.17
 * Time: 8:50
 */

namespace controllers;


use modules\Chat;
use modules\Header;
use objects\SmileDB;

class CommunicationController extends AbstractController
{
	public function actionChat()
	{
		$this->title = "Чат пользователей";
		$this->meta_desc = "Чат пользователей.";
		$this->meta_key = "чат пользователей, чат";

		$hornav = $this->getHornav();
		$hornav->addData('Чат пользователей');

		$chat = new Chat();
		$chat->hornav = $hornav;
		$chat->smiles = SmileDB::getAllSmiles();

		$this->render($chat);
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
		$header->css = array('/styles/main.css', '/styles/prettify.css');

		if($this->authUser)
		{
			$js = array('/js/jquery-1.10.2.min.js', '/js/functions.js', '/js/validator.js', '/js/prettify.js', '/js/main.js', '/js/chat.js');
		}
		else
		{
			$js = array('/js/jquery-1.10.2.min.js', '/js/functions.js', '/js/validator.js', '/js/prettify.js', '/js/main.js');
		}

		$header->js = $js;

		return $header;
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