<?php
/**
 * Created by PhpStorm.
 * User: arozhkov
 * Date: 21.09.17
 * Time: 14:00
 */

namespace modules;

/**
 * Класс модуля MainMenu
 *
 * Class MainMenu
 * @package modules
 */
class MainMenu extends AbstractModule
{
	public function __construct()
	{
		parent::__construct();

		$this->addProperty('uri');
		$this->addProperty('items', null, true);
	}

	/**
	 * Возвращает название шаблона модуля
	 *
	 * @return string
	 */
	public function getTemplateFile()
	{
		return 'main_menu';
	}

	/**
	 * Передает в свойство объекта массив с активными ссылками
	 */
	protected function preRender()
	{
		$this->addProperty('childrens', null, true);
		$this->addProperty('active', null, true);
		$childrens = array();
		$active = array();

		foreach ($this->items as $item)
		{
			if($item->parentId)
			{
				$childrens[$item->id] = $item->parentId;
			}

			if($item->link == $this->uri)
			{
				$active[] = $item->id;

				if($item->parentId)
				{
					$parentId = $item->parentId;
					$active[] = $parentId;

					while($parentId)
					{
						$parentId = $this->items[$parentId]->parentId;

						if($parentId)
						{
							$active[] = $parentId;
						}
					}
				}
			}
		}

		$this->childrens = $childrens;
		$this->active = $active;
	}
}