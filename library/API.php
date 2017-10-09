<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 02.10.2017
 * Time: 17:36
 */

namespace library;


use core\database\AbstractObjectDB;
use core\File;
use library\mail\Mail;
use objects\CommentDB;
use objects\UserDB;

/**
 * Класс для обработки данных через js запросы
 *
 * Class API
 * @package library
 */
class API
{
    private $mail;
    private $authUser;

    public function __construct()
    {
        $this->mail = new Mail();

        try
        {
            $this->authUser = UserDB::authUser();
        }
        catch (\Exception $e)
        {
            $this->authUser = null;
        }
    }

    /**
	 * Редактирование записи
	 *
     * @param string $obj название объекта БД для обработки
     * @param string $value значение для записи в свойство объекта
     * @param string $name имя для изменения (должно передаваться в виде name_id, например section_13)
	 * @param string $type тип переданных данных, для последующей обработки
     */
    public function edit($obj, $value, $name, $type)
    {
		$obj = ucfirst(mb_strtolower($obj));
        $class = 'objects\\' . $obj . 'DB';
        $obj = new $class;
        preg_match_all('/(.+?)_(\d+)/i', $name, $matches);

        if((count($matches[1]) > 0) && (count($matches[2]) > 0))
        {
            $field = $matches[1][0];
            $id = $matches[2][0];
            $obj->loadOnId($id);

            if($obj->accessEdit($this->authUser, $field))
            {
              if(($type == 'date') && !$value)
              {
                  $value = $obj->getDate();
              }
              elseif($value == 'null')
              {
                  $value = null;
              }

              if($obj->{$field} != $value)
              {
                  $obj->{$field} = $value;
              }
              else
              {
                  return $value;
              }

              try
              {
                  if(!$obj->save())
                  {
                      throw new \Exception();
                  }

                  return $obj->{$field};
              }
              catch (\Exception $e)
              {
                  return false;
              }
            }
        }

        return false;
    }

	/**
	 * Удаление записи
	 *
	 * @param string $obj название объекта БД для обработки
	 * @param string|int $id идентификатор
	 * @return bool
	 */
    public function delete($obj, $id)
    {
		$obj = ucfirst(mb_strtolower($obj));
        $class = 'objects\\' . $obj . 'DB';
        $obj = new $class;
        $obj->loadOnId($id);

        if($obj->accessDelete($this->authUser))
        {
            try
            {
                if(!$obj->delete())
                {
                    throw new \Exception();
                }

                return true;
            }
            catch (\Exception $e)
            {
                return false;
            }
        }

        return false;
    }

	/**
	 * Добавление комментария
	 *
	 * @param string|int $parentId идентификатор родительского комментария
	 * @param string|int $articleId идентификатор статьи
	 * @param string $text текст комментария
	 * @return bool|string
	 */
	public function addComment($parentId, $articleId, $text)
    {
        if(!$this->authUser)
        {
            return false;
        }

        $comment = new CommentDB();
        $comment->userId = $this->authUser->id;
        $comment->parentId = $parentId;
        $comment->articleId = $articleId;
        $comment->text = $text;

        try
        {
			$commentId = $comment->save();

            if(!$commentId)
            {
                throw new \Exception();
            }

            $commentParent = new CommentDB();
            $commentParent->loadOnId($parentId);

            if($commentParent->isSaved() && ($commentParent->userId != $this->authUser->id))
            {
                $user = new UserDB();
                $user->loadOnId($commentParent->userId);
                $this->mail->send($user->email, array('user' => $user, 'link' => $commentParent->link), 'comment_subscribe');
            }

            return json_encode(array('id' => $commentId,
                                'parentId' => $comment->parentId,
                                'userId' => $this->authUser->id,
                                'name' => $this->authUser->name,
                                'avatar' => $this->authUser->avatar,
                                'text' => $comment->text,
                                'date' => $comment->date));
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    public function addChatMessage($message)
	{
		if(!$this->authUser)
		{
			return false;
		}

		try
		{
			$chatMessageData = File::saveChatMessage($message, $this->authUser->name);

			return json_encode($chatMessageData);
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	public function getChatMessages()
	{
		try
		{
			$chatMessageData = File::getChatMessages();

			return json_encode($chatMessageData);
		}
		catch (\Exception $e)
		{
			return false;
		}
	}
}