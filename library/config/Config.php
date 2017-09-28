<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 13.09.17
 * Time: 12:16
 */

namespace library\config;


class Config
{
	const SITENAME  = 'alextest.local';
	const SECRET    = 'dasdaDS4d';
	const ADDRESS   = 'http://alextest.local';
	const ADM_NAME  = 'Alex';
	const ADM_EMAIL = 'coooooller@mail.ru';

	const DB_HOST 		= 'localhost';
	const DB_USER 		= 'root';
	const DB_PASSWORD 	= 'root';
	const DB_NAME 		= 'my_engine';
	const DB_PREFIX 	= 'xyz_';
	const DB_SYM_QUERY  = '?';

	const DIR_IMG       = '/images/';
	const DIR_ARTICLES  = '/images/articles/';
	const DIR_AVATAR    = '/images/avatars/';
	const DIR_TMPL      = __DIR__ . '/../../templates/';
	const DIR_EMAIL     = __DIR__ . '/../../templates/emails/';

	const VALIDATOR_NAMESPACE = 'validators\\';

	const LAYOUT = 'main';

	const FILE_MESSAGES = __DIR__ . '/../../text/messages.ini';

	const FORMAT_DATE = 'd.m.Y H:i:s';

	const COUNT_ARTICLES_ON_PAGE    = 3;
	const COUNT_SHOW_PAGES          = 10;

	const MIN_SEARCH_LEN = 3;
	const LEN_SEARCH_RES = 255;

	const DEFAULT_AVATAR    ='default.png';
	const MAX_SIZE_AVATAR   = 51200;
}