<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class news extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function getNews( $lang_id, $page = 1 )
    {
        return $this->get(
            '
                SELECT
                    id,
                    title,
                    alias,
                    content,
                    cover
                FROM
                    public.news
                WHERE
                    lang_id = :lang_id
                    AND
                    status = 1
                    AND
                    options @> \'"is_news"=>"1"\'::hstore
                ORDER BY
                    publish_date DESC
                LIMIT
                    '.\config::get( 'limits/news' ).'
                OFFSET
                    '.($page-1)*(\config::get( 'limits/news' ))
            ,
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getNewsFor1Page( $lang_id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    title,
                    alias,
                    content,
                    cover
                FROM
                    public.news
                WHERE
                    lang_id = :lang_id
                    AND
                    status = 1
                    AND
                    options @> \'"is_tips"=>"1"\'::hstore
                ORDER BY
                    publish_date DESC
                LIMIT
                    4
            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getOneNews( $lang_id, $news_id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    title,
                    alias,
                    meta_title,
                    meta_keywords,
                    meta_description,
                    content,
                    cover,
                    photogallery,
                    group_id
                FROM
                    public.news
                WHERE
                    lang_id = :lang_id
                    AND
                    id      = :news_id
                    AND
                    status = 1
                LIMIT 1
            ',
            [
                'lang_id' => $lang_id,
                'news_id' => $news_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getGroupsIdsByNewsId( $news_id )
    {
        return $this->get(
            '
                SELECT
                    group_id
                FROM
                    public.news
                WHERE
                    id      = :id
            ',
            [
                'id' => $news_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getNewsByGroupId( $lang_id, $group_id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    title,
                    alias,
                    content,
                    cover
                FROM
                    public.news
                WHERE
                    intset('.$group_id.') <@ group_id
                    AND
                    lang_id = :lang_id

            ',
            [
                'lang_id' => $lang_id,
                //'group_id' => $group_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTotalNews( $lang_id )
    {
        return $this->get(
            '
                SELECT
                    COUNT(id) as count
                FROM
                    public.news
                WHERE
                    lang_id = :lang_id
                    AND
                    status = 1
                    AND
                    options @> \'"is_news"=>"1"\'::hstore
                LIMIT
                    1
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTips( $lang_id, $page = 1 )
    {
        return $this->get(
            '
                SELECT
                    id,
                    title,
                    alias,
                    content,
                    cover
                FROM
                    public.news
                WHERE
                    lang_id = :lang_id
                    AND
                    status = 1
                    AND
                    options @> \'"is_tips"=>"1"\'::hstore
                ORDER BY
                    publish_date DESC
                LIMIT
                    '.\config::get( 'limits/news' ).'
                OFFSET
                    '.($page-1)*(\config::get( 'limits/news' ))
            ,
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTotalTips( $lang_id )
    {
        return $this->get(
            '
                SELECT
                    COUNT(id) as count
                FROM
                    public.news
                WHERE
                    lang_id = :lang_id
                    AND
                    status = 1
                    AND
                    options @> \'"is_tips"=>"1"\'::hstore
                LIMIT
                    1
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getVideos( $lang_id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    title,
                    alias,
                    video,
                    options
                FROM
                    public.news
                WHERE
                    lang_id = :lang_id
                    AND
                    video IS NOT NULL
                ORDER BY
                    publish_date DESC
            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////