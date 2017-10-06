<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 06.10.2017
 * Time: 6:41
 */

namespace library;


use library\config\Config;
use objects\SefDB;

class SEF
{
    public static function replaceSef($link, $address = '')
    {
        if(strpos($link, '//') && !strpos($link, Config::ADDRESS))
        {
            return $link;
        }

        $link = (strpos($link, Config::ADDRESS) === 0) ? substr($link, mb_strlen(Config::ADDRESS)) : $link;

        if($link === '/')
        {
            return $address . $link;
        }

        if(preg_match('/^\/\?page=(\d*)$/i', $link, $matches))
        {
            return Config::ADDRESS . '/page-' . $matches[1];
        }

        $alias = SefDB::getAliasOnLink($link);

        if($alias)
        {
            $link = $address . '/' . $alias . Config::SEF_SUFFIX;
        }
        else
        {
            $data = parse_url($link);
            $alias = SefDB::getAliasOnLink($data['path']);

            if($alias)
            {
                $link = $address . '/' . $alias . Config::SEF_SUFFIX . '?' . $data['query'];
            }
        }

        return $link;
    }

    public static function getRequest($uri)
    {
        $uri = strpos($uri, Config::ADDRESS) ? substr($uri, mb_strlen(Config::ADDRESS)) : $uri;

        if($uri === '/')
        {
            return $uri;
        }

        $uri = substr($uri, 1);
        $uri = str_replace(Config::SEF_SUFFIX, '', $uri);

        if(preg_match('/^page-(\d+)/i', $uri, $matches))
        {
            return '/?page=' . $matches[1];
        }

        $link = SefDB::getLinkOnAlias($uri);

        if(!$link)
        {
            $uri = substr($uri, 0, strpos($uri, '?'));
            $link = SefDB::getLinkOnAlias($uri);
        }

        if($link)
        {
            return $link;
        }

        return false;
    }
}