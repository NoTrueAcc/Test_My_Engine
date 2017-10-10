<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 9:47
 */

namespace core;
use library\config\Config;

/**
 * Класс для работы с файлами
 *
 * Class File
 * @package core
 */
class File
{
	/**
	 * Загрузка изображения
	 *
	 * @param array $file данные файла
	 * @param int|string $maxSize максимальный размер файла
	 * @param string $dir директория расположения файла
	 * @param bool $root брать путь от корневой директории?
	 * @param bool $sourceName название файла, если не указано, генерируется случайно
	 * @return mixed|string
	 * @throws \Exception
	 */
	public static function uploadImg(array $file, $maxSize, $dir, $root = false, $sourceName = false)
	{
		$badFormats = array('.php', '.phtml', '.php3', '.php4', '.html', '.htm');
		$imageTypes = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');

		foreach ($badFormats as $badFormat)
		{
			if(preg_match("/$badFormat/i", $file['name']))
			{
				throw new \Exception('ERROR_IMAGE_TYPE');
			}
		}

		$type = $file['type'];
		$size = $file['size'];

		if(!in_array($type, $imageTypes))
		{
			throw new \Exception('ERROR_IMAGE_TYPE');
		}

		if($size > $maxSize)
		{
			throw new \Exception('ERROR_IMAGE_SIZE');
		}

		if($sourceName)
		{
			$imageName = $file['name'];
		}
		else
		{
			$imageName = self::generateName() . '.' . substr($type, mb_strlen('image/'));
		}

		$uploadFile = $dir . $imageName;

		if(!$root)
		{
			$uploadFile = $_SERVER['DOCUMENT_ROOT'] . $uploadFile;
		}

		if(!move_uploaded_file($file['tmp_name'], $uploadFile))
		{
			throw new \Exception('UNKNOWN_ERROR');
		}

		return $imageName;
	}

	/**
	 * Генерирует случайное имя
	 *
	 * @return string
	 */
	public static function generateName()
	{
		return uniqid();
	}

	/**
	 * Удаляет файл
	 *
	 * @param string $file путь к файлу
	 * @param bool $root путь от корневой директории?
	 */
	public static function deleteFile($file, $root = false)
	{
		if(!$root)
		{
			$file = $_SERVER['DOCUMENT_ROOT'] . $file;
		}

		if(file_exists($file))
		{
			unlink($file);
		}
	}

	/**
	 * Проверяет существует ли файл
	 *
	 * @param string $file путь к файлу
	 * @param bool $root путь от корневой директории?
	 * @return bool
	 */
	public static function isExists($file, $root = false)
	{
		$file = $root ? $file : $_SERVER['DOCUMENT_ROOT'] . $file;

		return file_exists($file);
	}

	public static function saveChatMessage($chatMessage, $name, $userId)
	{
		$chatMessage = preg_replace('/ {2, }/i', ' ', $chatMessage);
		$chatMessage = trim($chatMessage);

		if(empty($chatMessage) || ($chatMessage == ' '))
		{
			return false;
		}

		$fp = file(Config::FILE_CHAT);

		if (count($fp) > Config::COUNT_SHOW_CHAT_MESSAGES)
		{
			unset($fp[0]);
		}

		foreach ($fp as $key => $message)
		{
			$fp[$key] = preg_replace("/\\n$/i", '', $fp[$key]);
		}

		$newMessage = date('d-m-Y H:i:s', time()) . '\&/' . $name . '\&/' . htmlspecialchars($chatMessage) . '\&/' . $userId;
		$newMessage = str_replace(array("\r\n", "\r", "\n"), ' ', strip_tags($newMessage));

		array_push($fp, $newMessage);
		$file = fopen(Config::FILE_CHAT, 'w');
		fputs($file, implode(PHP_EOL, $fp));
		fclose($file);

		return array(
			'date' => date('d-m-Y H:i:s', time()),
			'name' => $name,
			'message' =>$chatMessage
		);
	}

	public static function getChatMessages()
	{
		$fp = file(Config::FILE_CHAT);
		$chatMessageDataList = array();

		foreach ($fp as $key => $chatMessage)
		{
			$chatMessageData = explode('\&/', $chatMessage);
			$chatMessageDataList[$key] = array(
				'date' => $chatMessageData[0],
				'name' => $chatMessageData[1],
				'message' => htmlspecialchars_decode($chatMessageData[2]),
				'userId' => $chatMessageData[3]
			);
		}

		return $chatMessageDataList;
	}
}