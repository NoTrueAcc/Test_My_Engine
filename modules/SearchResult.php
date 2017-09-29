<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 14.09.2017
 * Time: 7:32
 */

namespace modules;
use library\config\Config;

/**
 * Класс для модуля SearchResult
 *
 * Class SearchResult
 * @package modules
 */
class SearchResult extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('query');
        $this->addProperty('field');
        $this->addProperty('errorLen', false);
        $this->addProperty('data', null, true);
    }

	/**
	 * Заменяет 2 и более пробелов на 1
	 */
    public function preRender()
    {
        $query = $this->query;
        $query = mb_strtolower($query);
        $query = preg_replace('/ {2,}/', ' ', $query);

        foreach ($this->data as $data)
        {
            $data->description = $this->getDescription($data->{$this->field}, $query);
        }
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'search_result';
    }

    private function getDescription($text, $query)
    {
		$len = Config::LEN_SEARCH_RES;
		if (strlen($text) > $len)
		{
			$i = 0;
			$k = 0;
			$arrayWords = explode(" ", $query);
			$pos = array();

			foreach ($arrayWords as $key => $value)
			{
				while (strpos($text, $value, $i) !== false)
				{
					$pos[$k] = strpos($text, $value, $i);
					$i += $pos[$k] + 1;
					if ($i < strlen($text)) $i = strlen($text);
					$k++;
				}

				$i = 0;
			}
			if (count($pos) != 0)
			{
				if (count($pos) > 1)
				{
					$max = 1;
					$maxFreq = array();

					for ($i = 0; $i < count($pos); $i++)
					{
						$k = 1;
						$sum = 0;
						$tempFreq[$k - 1] = $pos[$i];

						for ($j = $i; $j < count($pos) - 1; $j++)
						{
							$sum += $pos[$j + 1] - $pos[$j];
							if ($sum <= $len) $k++;
							else break;
							$tempFreq[$k] = $pos[$j + 1];
						}

						if ($k > $max)
						{
							$max = $k;
							$maxFreq = $tempFreq;
						}
					}

					if (count($maxFreq) == 0)
					{
						$max = 0;
						$maxFreq[] = $pos[0];
					}
				}
				else
				{
					$max = 0;
					$maxFreq = $pos;
				}
				$freeSpace = $len - ($maxFreq[$max] - $maxFreq[0]);
				$start = $maxFreq[0] - $freeSpace / 2;
				$end = $maxFreq[$max] + $freeSpace / 2;

				if ($start < 0)
				{
					$end -= $start;
					$start = 0;
				}

				if ($end > strlen($text))
				{
					$start -= ($end - strlen($text));
					$end = strlen($text);
				}
			}
			else
			{
				$start = 0;
				$end = $len;
			}

			while (!preg_match("/[[:space:]]/", substr($text, $start - 1, 1)) && ($start - 1) > 0)
			{
				$start--;
			}

			while (!preg_match("/[[:space:]]/", substr($text, $end, 1)) && $end < strlen($text))
			{
				$end++;
			}
		}
		else
		{
			$start = 0;
			$end = strlen($text);
		}

		if ($start == 1) $start = 0;

		if ($start < 1)
		{
			$stD = "";
		}
		else
		{
			$stD = "... ";
		}

		if ($end == strlen($text))
		{
			$endD = "";
		}
		else
		{
			$endD = " ...";
		}

		$description = substr($text, $start, $end - $start);
		$description = $stD.$description.$endD;

		return $this->selectSearchWords($description, $query);
	}

	private function selectSearchWords($description, $query)
	{
		$arrayWords = explode(" ", $query);

		foreach ($arrayWords as $value)
		{
			$description = preg_replace("/".$value."/i", "<span>$value</span>", $description);
		}

		return $description;
	}
}