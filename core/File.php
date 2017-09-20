<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 19.09.17
 * Time: 9:47
 */

namespace core;

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
		$imageTypes = array('img/jpg', 'img/jpeg', 'img/gif', 'img/png');

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
}