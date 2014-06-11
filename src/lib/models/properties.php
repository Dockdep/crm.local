<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class properties extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function getPropertiesByItemId( $lang_id, $type, $subtype, $item_id  )
    {
        return $this->get(
            '
                SELECT
                    id,
                    property_key_id,
                    property_value_id,
                    (
                        SELECT
                            value
                        FROM
                            public.properties_keys_i18n
                        WHERE
                            property_key_id = public.properties.property_key_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS key_value,
                    (
                        SELECT
                            value
                        FROM
                            public.properties_values_i18n
                        WHERE
                            property_value_id = public.properties.property_value_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS value_value
                FROM
                    public.properties
                WHERE
                    type    = :type
                    AND
                    subtype = :subtype
                    AND
                    id IN
                    (
                        SELECT
                            property_id
                        FROM
                            properties_items
                        WHERE
                            item_id = :item_id
                    )
            ',
            [
                'lang_id'   => $lang_id,
                'type'      => $type,
                'subtype'   => $subtype,
                'item_id'   => $item_id,
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getPropertiesByTypeSubtype( $lang_id, $type, $subtype  )
    {
        return $this->get(
            '
                SELECT
                    id,
                    property_key_id,
                    property_value_id,
                    (
                        SELECT
                            value
                        FROM
                            public.properties_keys_i18n
                        WHERE
                            property_key_id = public.properties.property_key_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS key_value,
                    (
                        SELECT
                            value
                        FROM
                            public.properties_values_i18n
                        WHERE
                            property_value_id = public.properties.property_value_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS value_value
                FROM
                    public.properties
                WHERE
                    type    =
                        (
                            SELECT
                                type
                            FROM
                                public.types_i18n
                            WHERE
                                alias = :type_alias
                            LIMIT 1
                        )
                    AND
                    subtype =
                        (
                            SELECT
                                subtype
                            FROM
                                subtypes_i18n
                            WHERE
                                alias = :subtype_alias
                            LIMIT 1
                        )

            ',
            [
                'lang_id'       => $lang_id,
                'type_alias'    => $type,
                'subtype_alias' => $subtype,
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getPropertiesForItems( $items_ids  )
    {
        return $this->get(
            '
                SELECT
                    property_id,
                    item_id
                FROM
                    public.properties_items
                WHERE
                    item_id IN ('.join( ',', $items_ids ).')

            ',
            [

            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////