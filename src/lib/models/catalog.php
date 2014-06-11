<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class catalog extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function getTypes( $lang_id )
    {
        return $this->get(
            '
                SELECT
                    type,
                    title,
                    alias,
                    meta_title,
                    meta_keywords,
                    meta_description,
                    (
                        SELECT
                            parent_id
                        FROM
                            public.types
                        WHERE
                            type = public.types.id
                    ) AS parent_id
                FROM
                    public.types_i18n
                WHERE
                    lang_id = :lang_id
                    AND
                    type IN
                    (
                        SELECT
                            id
                        FROM
                            public.types
                        WHERE
                            status = 1
                    )
                ORDER BY
                  type ASC
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getSubtypes( $lang_id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    type,
                    cover,
                    (
                        SELECT
                            title
                        FROM
                            public.subtypes_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            subtype = public.subtypes.id
                            AND
                            type = public.subtypes.type
                        LIMIT
                            1
                    ) AS title,
                    (
                        SELECT
                            alias
                        FROM
                            public.subtypes_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            subtype = public.subtypes.id
                            AND
                            type = public.subtypes.type
                        LIMIT
                            1
                    ) AS alias,
                    (
                        SELECT
                            meta_title
                        FROM
                            public.subtypes_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            subtype = public.subtypes.id
                            AND
                            type = public.subtypes.type
                        LIMIT
                            1
                    ) AS meta_title,
                    (
                        SELECT
                            meta_keywords
                        FROM
                            public.subtypes_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            subtype = public.subtypes.id
                            AND
                            type = public.subtypes.type
                        LIMIT
                            1
                    ) AS meta_keywords,
                    (
                        SELECT
                            meta_description
                        FROM
                            public.subtypes_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            subtype = public.subtypes.id
                            AND
                            type = public.subtypes.type
                        LIMIT
                            1
                    ) AS meta_description
                FROM
                    public.subtypes
                WHERE
                    status = 1
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTitles( $type_ids, $subtype_ids, $lang_id )
    {
        return $this->get(
            '
                SELECT
                    title,
                    subtype,
                    type
                FROM
                    public.subtypes_i18n
                WHERE
                    lang_id = :lang_id
                    AND
                    type IN ('.join( ',', $type_ids ).')
                    AND
                    subtype IN ('.join( ',', $subtype_ids ).')
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////