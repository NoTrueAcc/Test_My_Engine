<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 10.10.17
 * Time: 12:31
 */

namespace objects;


use library\config\Config;
use library\database\ObjectDB;
use library\database\SelectDB;

class SmileDB extends ObjectDB
{
	protected static $table = 'smiles';

	public function __construct()
	{
		parent::__construct(self::$table);

		$this->addProperty('code', 'ValidateSmile');
		$this->addProperty('title', 'ValidateTitle');
		$this->addProperty('img', 'ValidateImg');
	}

	public static function getAllSmiles()
	{
		$select = new SelectDB();
		$select->from(self::$table, '*');

		$smilesDataList = self::$db->select($select);
		$smiles = ObjectDB::buildMultiple(__CLASS__, $smilesDataList);

		return $smiles;
	}

	protected function postInit()
	{
		$this->img = Config::DIR_SMILES . $this->img;

		return true;
	}

	protected function preValidate()
	{
		if(!is_null($this->img))
		{
			$this->img = basename($this->img);
		}

		return true;
	}
}