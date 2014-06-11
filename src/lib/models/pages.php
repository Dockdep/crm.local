<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class pages extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function getPages( $lang_id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    content_title,
                    alias
                FROM
                    public.pages
                WHERE
                    lang_id = :lang_id
                    AND
                    status = 1
                ORDER BY
                    id ASC
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getPage( $page_id, $lang_id )
    {
        return $this->get(
            '
                SELECT
                    meta_title,
                    meta_keywords,
                    meta_description,
                    alias,
                    content_title,
                    content_text
                FROM
                    public.pages
                WHERE
                    lang_id = :lang_id
                    AND
                    id      = :page_id
                    AND
                    status = 1
                ORDER BY
                    id ASC
            ',
            [
                'lang_id' => $lang_id,
                'page_id' => $page_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////