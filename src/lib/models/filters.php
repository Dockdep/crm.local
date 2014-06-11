<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class filters extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function getFilters( $lang_id, $type, $subtype )
    {
        $data = $this->get(
            '
                SELECT
                    id,
                    type,
                    subtype,
                    filter_key_id,
                    filter_value_id,
                    options,
                    (
                        SELECT
                            value
                        FROM
                            filters_keys_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            filter_key_id = public.filters.filter_key_id
                    ) AS filter_key_value,
                    (
                        SELECT
                            alias
                        FROM
                            filters_keys_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            filter_key_id = public.filters.filter_key_id
                    ) AS filter_key_alias,
                    (
                        SELECT
                            value
                        FROM
                            filters_values_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            filter_value_id = public.filters.filter_value_id
                    ) AS filter_value_value,
                    (
                        SELECT
                            alias
                        FROM
                            filters_values_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            filter_value_id = public.filters.filter_value_id
                    ) AS filter_value_alias
                FROM
                    public.filters
                WHERE
                    type    = :type
                    AND
                    subtype = :subtype
            ',
            [
                'lang_id'   => $lang_id,
                'type'      => $type,
                'subtype'   => $subtype
            ],
            -1
        );

        return $data;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getFiltersByItemId( $lang_id, $type, $subtype, $item_id  )
    {
        return $this->get(
            '
                SELECT
                    id,
                    filter_key_id,
                    filter_value_id,
                    (
                        SELECT
                            value
                        FROM
                            public.filters_keys_i18n
                        WHERE
                            filter_key_id = public.filters.filter_key_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS key_value,
                    (
                        SELECT
                            value
                        FROM
                            public.filters_values_i18n
                        WHERE
                            filter_value_id = public.filters.filter_value_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS value_value
                FROM
                    public.filters
                WHERE
                    type    = :type
                    AND
                    subtype = :subtype
                    AND
                    id IN
                    (
                        SELECT
                            filter_id
                        FROM
                            filters_items
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

    public function getSeoImportantFilters( $lang_id )
    {
        $data = $this->get(
            '
                SELECT
                    id,
                    filter_key_id,
                    filter_value_id,
                    (
                        SELECT
                            alias
                        FROM
                            filters_keys_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            filter_key_id = public.filters.filter_key_id
                    ) AS filter_key_alias,
                    (
                        SELECT
                            alias
                        FROM
                            filters_values_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            filter_value_id = public.filters.filter_value_id
                    ) AS filter_value_alias
                FROM
                    public.filters
                WHERE
                    options @> \'"is_seo_important"=>"1"\'::hstore
            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );

        return $data;
    }

    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////

    public function getImages()
    {
        return $this->get(
            '
                SELECT
                    cover
                FROM
                    public.subtypes
            ',
            [

            ],
            -1
        );
    }

    public function getAvatars()
    {
        return $this->get(
            '
                SELECT
                    cover
                FROM
                    public.items_group
            ',
            [

            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function addFilters( $filters_values, $type, $subtype, $lang_id )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            foreach( $filters_values as $title => $val )
            {
                $data_keys = $this->get(
                    '
                        INSERT INTO
                            public.filters_keys_i18n
                                (
                                    value,
                                    alias,
                                    lang_id
                                )
                                VALUES
                                (
                                    :value,
                                    :alias,
                                    :lang_id
                                )
                                RETURNING
                                    filter_key_id
                    ',
                    [
                        'value'             => $val['key_value'],
                        'alias'             => $val['key_alias'],
                        'lang_id'           => $lang_id
                    ]
                );

                foreach( $val['key_values'] as $v )
                {
                    $data_values = $this->get(
                        '
                            INSERT INTO
                                public.filters_values_i18n
                                    (
                                        value,
                                        alias,
                                        lang_id
                                    )
                                    VALUES
                                    (
                                        :value,
                                        :alias,
                                        :lang_id
                                    )
                                    RETURNING
                                        filter_value_id
                        ',
                        [
                            'value'             => $v['value_value'],
                            'alias'             => $v['value_alias'],
                            'lang_id'           => $lang_id
                        ]
                    );



                    if( !empty( $data_keys ) && !empty( $data_values ) )
                    {
                        $data_filters = $this->exec(
                            '
                                INSERT INTO
                                    public.filters
                                        (
                                            type,
                                            subtype,
                                            filter_key_id,
                                            filter_value_id
                                        )
                                        VALUES
                                        (
                                            :type,
                                            :subtype,
                                            :filter_key_id,
                                            :filter_value_id
                                        )
                            ',
                            [
                                'type'              => $type,
                                'subtype'           => $subtype,
                                'filter_key_id'     => $data_keys['0']['filter_key_id'],
                                'filter_value_id'   => $data_values['0']['filter_value_id']
                            ]
                        );
                    }


                }


            }

            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $connection->rollback();
        }

        return false;
    }


    /////////////////////////////////////////////////////////////////////////////


    public function addProperties( $properties_values, $type, $subtype, $lang_id )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            foreach( $properties_values as $title => $val )
            {
                $data_keys = $this->get(
                    '
                        INSERT INTO
                            public.properties_keys_i18n
                                (
                                    value,
                                    lang_id
                                )
                                VALUES
                                (
                                    :value,
                                    :lang_id
                                )
                                RETURNING
                                    property_key_id
                    ',
                    [
                        'value'             => $title,
                        'lang_id'           => $lang_id
                    ]
                );

                foreach( $val as $v )
                {
                    $data_values = $this->get(
                        '
                            INSERT INTO
                                public.properties_values_i18n
                                    (
                                        value,
                                        lang_id
                                    )
                                    VALUES
                                    (
                                        :value,
                                        :lang_id
                                    )
                                    RETURNING
                                        property_value_id
                        ',
                        [
                            'value'             => $v,
                            'lang_id'           => $lang_id
                        ]
                    );

                    if( !empty( $data_keys ) && !empty( $data_values ) )
                    {
                        $data_filters = $this->exec(
                            '
                                INSERT INTO
                                    public.properties
                                        (
                                            type,
                                            subtype,
                                            property_key_id,
                                            property_value_id
                                        )
                                        VALUES
                                        (
                                            :type,
                                            :subtype,
                                            :property_key_id,
                                            :property_value_id
                                        )
                            ',
                            [
                                'type'              => $type,
                                'subtype'           => $subtype,
                                'property_key_id'   => $data_keys['0']['property_key_id'],
                                'property_value_id' => $data_values['0']['property_value_id']
                            ]
                        );
                    }
                }

            }

            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $connection->rollback();
        }

        return false;
    }


    /////////////////////////////////////////////////////////////////////////////

    public function getChar( $lang_id, $type, $subtype )
    {
        $data = $this->get(
            '
                SELECT
                    id,
                    type,
                    subtype,
                    property_key_id,
                    property_value_id,
                    (
                        SELECT
                            value
                        FROM
                            properties_keys_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            property_key_id = public.properties.property_key_id
                    ) AS property_key_value,
                    (
                        SELECT
                            value
                        FROM
                            properties_values_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            property_value_id = public.properties.property_value_id
                    ) AS property_value_value
                FROM
                    public.properties
                WHERE
                    type    = :type
                    AND
                    subtype = :subtype
            ',
            [
                'lang_id'   => $lang_id,
                'type'      => $type,
                'subtype'   => $subtype
            ],
            -1
        );

        return $data;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function addItems( $val )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data_group_id = $this->exec(
                    '
                        INSERT INTO
                            public.items_group
                                (
                                    status,
                                    cover,
                                    photogallery
                                )
                                VALUES
                                (
                                    :status,
                                    :cover,
                                    :photogallery
                                )
                    ',
                    [
                        'status'        => $val['status'],
                        'cover'         => $val['cover'],
                        'photogallery'  => $val['photogallery']
                    ]
                );

                if( !empty( $data_group_id ) )
                {
                    $data_items_group_alias = $this->exec(
                        '
                            INSERT INTO
                                public.items_group_alias
                                    (
                                        group_id,
                                        lang_id,
                                        type,
                                        subtype,
                                        alias
                                    )
                                    VALUES
                                    (
                                        currval(\'items_group_group_id_seq\'),
                                        :lang_id,
                                        :type,
                                        :subtype,
                                        :alias
                                    )
                        ',
                        [
                            'lang_id'               => $val['lang_id'],
                            'type'                  => $val['type'],
                            'subtype'               => $val['subtype'],
                            'alias'                 => $val['group_alias']
                        ]
                    );

                    foreach( $val['items'] as $v )
                    {
                        $this->getDi()->get('models')->getFilters()->addOneItem( $val, $v );
                    }

                }

            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $this->showException( $e );
            $connection->rollback();
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function addOneItem( $val, $v )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data_items = $this->exec(
                '
                    INSERT INTO
                        public.items
                            (
                                group_id,
                                type,
                                subtype,
                                product_id,
                                price1,
                                price2,
                                size,
                                color,
                                status
                            )
                            VALUES
                            (
                                currval(\'items_group_group_id_seq\'),
                                :type,
                                :subtype,
                                :product_id,
                                :price1,
                                :price2,
                                :size,
                                :color,
                                :status
                            )
                ',
                [
                    'type'          => $val['type'],
                    'subtype'       => $val['subtype'],
                    'product_id'    => $v['product_id'],
                    'price1'        => $v['price1'],
                    'price2'        => $v['price2'],
                    'size'          => $v['size'],
                    'color'         => $v['color'],
                    'status'        => $v['status']
                ]
            );

            $data_items_i18n = $this->get(
                '
                    INSERT INTO
                        public.items_i18n
                            (
                                item_id,
                                lang_id,
                                group_id,
                                meta_title,
                                meta_description,
                                title,
                                description,
                                content_description
                            )
                            VALUES
                            (
                                currval(\'items_id_seq\'),
                                :lang_id,
                                currval(\'items_group_group_id_seq\'),
                                :meta_title,
                                :meta_description,
                                :title,
                                :description,
                                :content_description
                            )
                ',
                [
                    'lang_id'               => $val['lang_id'],

                    'meta_title'            => $val['group_title'],
                    'meta_description'      => $val['description'],
                    'title'                 => $val['group_title'],
                    'description'           => $val['description'],
                    'content_description'   => $val['content_description']
                ]
            );

            foreach( $v['filters'] as $f )
            {
                $data_items_i18n = $this->get(
                    '
                        INSERT INTO
                            public.filters_items
                                (
                                    filter_id,
                                    type,
                                    subtype,
                                    group_id,
                                    item_id
                                )
                                VALUES
                                (
                                    :filter_id,
                                    :type,
                                    :subtype,
                                    currval(\'items_group_group_id_seq\'),
                                    currval(\'items_id_seq\')
                                )
                    ',
                    [
                        'filter_id'     => $f,
                        'type'          => $val['type'],
                        'subtype'       => $val['subtype'],
                    ]
                );
            }

            foreach( $v['char'] as $f )
            {
                $data_items_i18n = $this->get(
                    '
                        INSERT INTO
                            public.properties_items
                                (
                                    property_id,
                                    type,
                                    subtype,
                                    group_id,
                                    item_id
                                )
                                VALUES
                                (
                                    :property_id,
                                    :type,
                                    :subtype,
                                    currval(\'items_group_group_id_seq\'),
                                    currval(\'items_id_seq\')
                                )
                    ',
                    [
                        'property_id'   => $f,
                        'type'          => $val['type'],
                        'subtype'       => $val['subtype'],
                    ]
                );
            }

            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $this->showException( $e );
            $connection->rollback();
        }

        return false;

    }


    /////////////////////////////////////////////////////////////////////////////

    public function addItems2filters( $kavun )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            foreach( $kavun as $title => $val )
            {
                foreach( $val as $v )
                {
                    $data_insert = $this->exec(
                        '
                            INSERT INTO
                                public.item2filters
                                    (
                                        subtype,
                                        title,
                                        value,
                                        lang_id
                                    )
                                    VALUES
                                    (
                                        :subtype,
                                        :title,
                                        :value,
                                        :lang_id
                                    )
                        ',
                        [

                        ]
                    );
                }
            }

            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $connection->rollback();
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function addCatalog( $catalog, $lang_id )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            foreach( $catalog as $key => $value )
            {
                /*
                $data_types = $this->exec(
                    '
                        INSERT INTO
                            public.types
                                (
                                    id,
                                    status
                                )
                                VALUES
                                (
                                    :id,
                                    1
                                )
                    ',
                    [
                        'id' => $key
                    ]
                );

                $data_types_i18n = $this->exec(
                    '
                        INSERT INTO
                            public.types_i18n
                                (
                                    type,
                                    lang_id,
                                    title,
                                    alias,
                                    meta_title,
                                    meta_keywords,
                                    meta_description
                                )
                                VALUES
                                (
                                    :type,
                                    :lang_id,
                                    :title,
                                    :alias,
                                    :meta_title,
                                    :meta_keywords,
                                    :meta_description
                                )
                    ',
                    [
                        'type'              => $key,
                        'lang_id'           => $lang_id,
                        'title'             => $value['title'],
                        'alias'             => $value['alias'],
                        'meta_title'        => $value['title'],
                        'meta_keywords'     => $value['title'],
                        'meta_description'  => $value['title'],
                    ]
                );
                */

                foreach( $value['subtypes'] as $k => $v )
                {
                    //p($catalog['1']['subtypes']);
                    //p($value['subtypes'],1);

                    $data_subtypes = $this->exec(
                        '
                            INSERT INTO
                                public.subtypes
                                    (
                                        id,
                                        type,
                                        cover,
                                        status
                                    )
                                    VALUES
                                    (
                                        :id,
                                        :type,
                                        :cover,
                                        :status
                                    )
                        ',
                        [
                            'id'        => $k,
                            'type'      => $key,
                            'cover'     => isset($v['cover']) && !empty($v['cover']) ? $v['cover'] : NULL,
                            'status'    => 1
                        ]
                    );



                    $data_subtypes_i18n = $this->exec(
                        '
                            INSERT INTO
                                public.subtypes_i18n
                                    (
                                        subtype,
                                        type,
                                        lang_id,
                                        title,
                                        alias,
                                        meta_title,
                                        meta_keywords,
                                        meta_description
                                    )
                                    VALUES
                                    (
                                        :subtype,
                                        :type,
                                        :lang_id,
                                        :title,
                                        :alias,
                                        :meta_title,
                                        :meta_keywords,
                                        :meta_description
                                    )
                        ',
                        [
                            'subtype'           => $k,
                            'type'              => $key,
                            'lang_id'           => $lang_id,
                            'title'             => $v['title'],
                            'alias'             => $v['alias'],
                            'meta_title'        => $v['title'],
                            'meta_keywords'     => $v['title'],
                            'meta_description'  => $v['title'],
                        ]
                    );
                }
            }


            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $connection->rollback();
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function addDistrict( $district )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            foreach( $district as $title => $val )
            {
                foreach( $val as $k => $v )
                {
                    $data_insert = $this->exec(
                        '
                            INSERT INTO
                                public.districts
                                    (
                                        id,
                                        lang_id,
                                        title
                                    )
                                    VALUES
                                    (
                                        :id,
                                        :lang_id,
                                        :title
                                    )
                        ',
                        [
                            'id' => ($k+1),
                            'lang_id' => $title,
                            'title' => $v
                        ]
                    );
                }
            }

            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $connection->rollback();
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getDistrict()
    {
        return $this->get(
            '
                SELECT
                    id,
                    title
                FROM
                    public.districts
                WHERE
                    lang_id = 1
            ',
            [

            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function addShop( $shop )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            foreach( $shop as $title => $val )
            {
                $data = $this->get(
                    '
                        INSERT INTO
                            public.partners
                                (
                                    district_id,
                                    phone,
                                    shop_type
                                )
                                VALUES
                                (
                                    :district_id,
                                    :phone,
                                    :shop_type
                                )
                                RETURNING id
                    ',
                    [
                        'district_id' => $val['district'],
                        'phone' => $val['phone'],
                        'shop_type' => 3,
                    ],
                    -1
                );

                $data_insert = $this->get(
                    '
                        INSERT INTO
                            public.partners_i18n
                                (
                                    parthner_id,
                                    lang_id,

                                    city,
                                    address
                                )
                                VALUES
                                (
                                    :parthner_id,
                                    1,

                                    :city,
                                    :address
                                )
                    ',
                    [
                        'parthner_id' => $data['0']['id'],
                        //'title' => $val['title'],
                        'city' => 'м. Київ',
                        'address' => $val['address_'],
                    ],
                    -1
                );
            }

            $connection->commit();

            return true;
        }
        catch(\Exception $e)
        {
            $this->showException( $e );
            $connection->rollback();
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////