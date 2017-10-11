<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13.09.2017
 * Time: 6:28
 */

namespace modules;

/**
 * Класс для модуля PollResult
 *
 * Class PollResult
 * @package modules
 */
class PollResult extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();
        $this->addProperty('title');
        $this->addProperty('hornav');
        $this->addProperty('message');
        $this->addProperty('data', null, true);
    }

	/**
	 * Подсчитывает процент пользователей проголосовавших за определенный вариант ответа
	 */
    public function preRender()
    {
        $countVoters = 0;

        foreach ($this->data as $data)
        {
            $countVoters += $data->voters;
        }

        $this->addProperty('countVoters', $countVoters);

        foreach ($this->data as $data)
        {
            if($countVoters != 0)
            {
                $data->percent = ($data->voters / $countVoters) * 100;
            }
            else
            {
                $data->percent = 0;
            }
        }
    }

	/**
	 * Возвращает название шаблона
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
        return 'poll_result';
    }
}