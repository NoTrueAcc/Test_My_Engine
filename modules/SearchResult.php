<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 14.09.2017
 * Time: 7:32
 */

namespace modules;

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
        /**
         * Надо мутить поисковый запрос. Логика:
         * Ищем позиции вхождения слова в тексте и добавляем эти позиции в массив наверное с текстом
         * После проверяем максимально количество повторений слова на интервале не превышающем максимальную длину
         * Выводиим  результат с максимальным количеством совпадений на заданном интервале
         */
    }
}