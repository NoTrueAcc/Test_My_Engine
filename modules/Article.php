<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 26.09.2017
 * Time: 6:41
 */

namespace modules;


class Article extends AbstractModule
{
    public function __construct()
    {
        parent::__construct();

        $this->addProperty('hornav');
        $this->addProperty('authUser');
        $this->addProperty('article');
        $this->addProperty('prevArticle');
        $this->addProperty('nextArticle');
        $this->addProperty('linkRegister');
        $this->addProperty('comments');
    }

    public function getTemplateFile()
    {
        return 'article';
    }

    protected function preRender()
    {
        $this->addProperty('childrens');
        $childrens = array();

        foreach ($comments as $comment)
        {
            $childrens[$comment->id] = $comment->parentId;
        }

        $this->childrens = $childrens;
    }
}