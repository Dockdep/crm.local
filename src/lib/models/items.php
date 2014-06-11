<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class items extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function getTopGroups( $lang_id = '1', $page = '1' )
    {
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                            )
                            AND
                            group_id = public.items_group.group_id
                        LIMIT
                            1
                    ) as id
                FROM
                    public.items_group
                WHERE
                    options @> \'"is_top"=>"1"\'::hstore
                LIMIT
                    '.\config::get( 'limits/top_items' ).'
                OFFSET
                    '.($page-1)*(\config::get( 'limits/top_items' ))
            ,
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getRecommendedGroups( $lang_id = '1', $page = '1' )
    {
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                            )
                            AND
                            group_id = public.items_group.group_id
                        LIMIT
                            1
                    ) as id
                FROM
                    public.items_group
                WHERE
                    options @> \'"is_recommended"=>"1"\'::hstore
                LIMIT
                    '.\config::get( 'limits/top_items' ).'
                OFFSET
                    '.($page-1)*(\config::get( 'limits/top_items' ))
            ,
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getStockGroups( $lang_id = '1', $page = '1' )
    {
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                            )
                            AND
                            group_id = public.items_group.group_id
                        LIMIT
                            1
                    ) as id
                FROM
                    public.items_group
                WHERE
                    options @> \'"is_stock"=>"1"\'::hstore
                LIMIT
                    '.\config::get( 'limits/top_items' ).'
                OFFSET
                    '.($page-1)*(\config::get( 'limits/top_items' ))
            ,
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getGroupsBySubtype( $lang_id = '1', $type, $subtype, $page, $sort )
    {
        $sql = 'group_id DESC';

        if( in_array( 1, $sort ) )
        {
            $sql = 'new ASC';
        }
        if( in_array( 2, $sort ) )
        {
            $sql = 'top ASC';
        }
        if( in_array( 3, $sort ) )
        {
            $sql .= ',min_price ASC';
        }
        if( in_array( 4, $sort ) )
        {
            $sql .= ',min_price DESC';
        }
        if( in_array( 3, $sort ) && in_array( 0, $sort ) )
        {
            $sql = 'min_price ASC';
        }
        if( in_array( 4, $sort ) && in_array( 0, $sort ) )
        {
            $sql = 'min_price DESC';
        }

        //p($sql,1);

        return $this->get(
        '
            SELECT
                group_id,
                options->\'is_new\' AS new,
                options->\'is_top\' AS top,
                cover,
                options,
                (
                    SELECT
                        alias
                    FROM
                        items_group_alias
                    WHERE
                        group_id = public.items_group.group_id
                        AND
                        type = :type
                        AND
                        subtype = :subtype
                        AND
                        lang_id = :lang_id
                ) AS alias,
                (
                    SELECT
                        id
                    FROM
                        public.items
                    WHERE
                        price2 IN
                        (
                            SELECT
                                MIN(price2)
                            FROM
                                public.items
                            WHERE
                                group_id = public.items_group.group_id
                        )
                        AND
                        group_id = public.items_group.group_id
                    LIMIT 1
                ) as id,
                (
                    SELECT
                        price2
                    FROM
                        public.items
                    WHERE
                        group_id = public.items_group.group_id
                        AND
                        price2 IN
                        (
                            SELECT
                                MIN(price2)
                            FROM
                                public.items
                            WHERE
                                group_id = public.items_group.group_id
                        )
                    LIMIT 1
                ) AS min_price
            FROM
                public.items_group
            WHERE
                status = 1
                AND
                group_id IN
                (
                    SELECT
                        group_id
                    FROM
                        items_group_alias
                    WHERE
                        type = :type
                        AND
                        subtype = :subtype
                        AND
                        lang_id = :lang_id
                )
            ORDER BY
                '.$sql.'
            LIMIT
                '.\config::get( 'limits/items' ).'
            OFFSET
                '.($page-1)*(\config::get( 'limits/items' ))
        ,
        [
            'lang_id'   => $lang_id,
            'type'      => $type,
            'subtype'   => $subtype,
        ],
        -1
    );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getItemsWithMinPrice( $lang_id = '1', $item_ids )
    {
        return $this->get(
            '
                SELECT
                    id,
                    price2,
                    (
                        SELECT
                            title
                        FROM
                            items_i18n
                        WHERE
                            item_id = public.items.id
                            AND
                            lang_id = :lang_id
                    ) AS title,
                    (
                        SELECT
                            description
                        FROM
                            items_i18n
                        WHERE
                            item_id = public.items.id
                            AND
                            lang_id = :lang_id
                    ) AS description,
                    (
                        SELECT
                            content_description
                        FROM
                            items_i18n
                        WHERE
                            item_id = public.items.id
                            AND
                            lang_id = :lang_id
                    ) AS content_description
                FROM
                    public.items
                WHERE
                    id IN ('.$item_ids.')
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getAllItems( $lang_id = '1', $type, $subtype )
    {
        return $this->get(
            '
                SELECT
                    COUNT(group_id) as items
                FROM
                    public.items_group
                WHERE
                    status = 1
                    AND
                    group_id IN
                    (
                        SELECT
                            group_id
                        FROM
                            items_group_alias
                        WHERE
                            type = :type
                            AND
                            subtype = :subtype
                            AND
                            lang_id = :lang_id
                    )
            ',
            [
                'lang_id' => $lang_id,
                'type' => $type,
                'subtype' => $subtype,
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getMaxMinPrice( $type, $subtype )
    {
        return $this->get(
            '
                SELECT
                    MIN(price2) as min_price,
                    MAX(price2) as max_price
                FROM
                    public.items

                WHERE
                    type    = :type
                    AND
                    subtype = :subtype
                    AND
                    status = 1
            ',
            [
                'subtype'           => $subtype,
                'type'              => $type
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getOneItem( $lang_id, $type, $subtype, $id )
    {
        return $this->get(
            '
                SELECT
                    i.id,
                    i.group_id,
                    i.type,
                    i.subtype,
                    i.product_id,
                    i.price2,
                    i.size,
                    i.color_id,
                    i.status,
                    i.photogallery,
                    i.cover,
                    i18n.meta_title,
                    i18n.meta_description,
                    i18n.title,
                    i18n.content_description,
                    i18n.description,
                    i18n.content_video,
                    (
                        SELECT
                            alias
                        FROM
                            public.types_i18n
                        WHERE
                            type = i.type
                            AND
                            lang_id = :lang_id
                    ) AS type_alias,
                    (
                        SELECT
                            alias
                        FROM
                            public.subtypes_i18n
                        WHERE
                            type = i.type
                            AND
                            subtype = i.subtype
                            AND
                            lang_id = :lang_id
                    ) AS subtype_alias
                FROM
                    public.items as i
                JOIN
                    public.items_i18n as i18n
                    ON ( i.id =  i18n.item_id )
                WHERE
                    id = :id
                    AND
                    lang_id = :lang_id
                LIMIT
                    1
            ',
            [
                'id'      => $id,
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getSizes( $lang_id, $type, $subtype, $group_alias )
    {
        return $this->get(
            '
                SELECT
                    id,
                    size,
                    color_id,
                    cover,
                    (
                        SELECT
                            absolute_color
                        FROM
                            public.colors
                        WHERE
                            id = public.items.color_id
                        LIMIT 1
                    ) AS absolute_color,
                    (
                        SELECT
                            title
                        FROM
                            public.colors_i18n
                        WHERE
                            color_id = public.items.color_id
                            AND
                            lang_id = :lang_id
                        LIMIT 1
                    ) AS color_title
                FROM
                    public.items
                WHERE
                    group_id =
                    (
                        SELECT
                            group_id
                        FROM
                            items_group_alias
                        WHERE
                            type    = :type
                            AND
                            subtype = :subtype
                            AND
                            lang_id = :lang_id
                            AND
                            alias   = :alias
                    )
                ORDER BY
                  price2 ASC
            ',
            [
                'lang_id'       => $lang_id,
                'type'          => $type,
                'subtype'       => $subtype,
                'alias'         => $group_alias
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getColorsInfoByColorId( $lang_id, $id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    absolute_color,
                    (
                        SELECT
                            title
                        FROM
                            public.colors_i18n
                        WHERE
                            color_id = public.colors.id
                            AND
                            lang_id = :lang_id
                        LIMIT 1
                    ) AS color_title
                FROM
                    public.colors
                WHERE
                    id = :id
            ',
            [
                'lang_id'       => $lang_id,
                'id'            => $id,
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getPopularItems( $lang_id = '1' )
    {
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                            )
                            AND
                            group_id = public.items_group.group_id
                        LIMIT 1
                    ) as id

                FROM
                    public.items_group
                WHERE
                    status = 1
                ORDER BY
                    (view_count + (10*add2cart_count)) DESC
                LIMIT
                    5
            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getBuyWithItems( $lang_id, $group_id )
    {
        $data_groups = [];

        $data_group_ids_by_with = $this->get(
            '
                SELECT
                    group_id_buy_with
                FROM
                    public.items_group_buy_with
                WHERE
                    group_id = :group_id
            ',
            [
                'group_id'      => $group_id
            ],
            -1
        );

        if( !empty( $data_group_ids_by_with ) )
        {
            $group_ids_by_with_ = $this->getDi()->get('etc')->int2arr($data_group_ids_by_with['0']['group_id_buy_with']);
            $group_ids_by_with = join( ',', $group_ids_by_with_ );

            $data_groups = $this->get(
                '
                    SELECT
                        group_id,
                        cover,
                        options,
                        (
                            SELECT
                                alias
                            FROM
                                items_group_alias
                            WHERE
                                group_id = public.items_group.group_id
                                AND
                                lang_id = :lang_id
                        ) AS alias,
                        (
                            SELECT
                                type
                            FROM
                                items_group_alias
                            WHERE
                                group_id = public.items_group.group_id
                                AND
                                lang_id = :lang_id
                            LIMIT
                                1
                        ) AS type_id,
                        (
                            SELECT
                                subtype
                            FROM
                                items_group_alias
                            WHERE
                                group_id = public.items_group.group_id
                                AND
                                lang_id = :lang_id
                            LIMIT
                                1
                        ) AS subtype_id,
                        (
                            SELECT
                                id
                            FROM
                                public.items
                            WHERE
                                price2 IN
                                (
                                    SELECT
                                        MIN(price2)
                                    FROM
                                        public.items
                                    WHERE
                                        group_id = public.items_group.group_id
                                )
                                AND
                                group_id = public.items_group.group_id
                        ) as id

                    FROM
                        public.items_group
                    WHERE
                        status = 1
                        AND
                        group_id IN ('.$group_ids_by_with.')
                    LIMIT
                        5
                ',
                [
                    'lang_id'       => $lang_id
                ],
                -1
            );
        }

        return $data_groups;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getSameItems( $lang_id = '1', $type, $subtype )
    {
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            type = :type
                            AND
                            subtype = :subtype
                            AND
                            lang_id = :lang_id
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                            )
                            AND
                            group_id = public.items_group.group_id
                    ) as id
                FROM
                    public.items_group
                WHERE
                    status = 1
                    AND
                    group_id IN
                    (
                        SELECT
                            group_id
                        FROM
                            items_group_alias
                        WHERE
                            type = :type
                            AND
                            subtype = :subtype
                            AND
                            lang_id = :lang_id
                    )
                ORDER BY
                    group_id DESC
                LIMIT
                    5
            ',
            [
                'lang_id'   => $lang_id,
                'type'      => $type,
                'subtype'   => $subtype,
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getLookedGroups( $lang_id, $looked )
    {
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                            )
                            AND
                            group_id = public.items_group.group_id
                    ) as id
                FROM
                    public.items_group
                WHERE
                    status = 1
                    AND
                    group_id IN
                    (
                        SELECT
                            group_id
                        FROM
                            items
                        WHERE
                            id IN ('.join( ',', $looked ).')
                    )
                LIMIT
                    5
            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getNews2Groups( $lang_id, $groups_ids )
    {
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                            )
                            AND
                            group_id = public.items_group.group_id
                    ) as id
                FROM
                    public.items_group
                WHERE
                    status = 1
                    AND
                    group_id IN ('.join( ',', $groups_ids ).')
                LIMIT
                    5
            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTotalTopItems()
    {
        return $this->get(
            '
                SELECT
                    COUNT(group_id) as total
                FROM
                    public.items_group
                WHERE
                    options @> \'"is_top"=>"1"\'::hstore
            ',
            [

            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTotalRecommendedItems()
    {
        return $this->get(
            '
                SELECT
                    COUNT(group_id) as total
                FROM
                    public.items_group
                WHERE
                    options @> \'"is_recommended"=>"1"\'::hstore
            ',
            [

            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getStockTopItems()
    {
        return $this->get(
            '
                SELECT
                    COUNT(group_id) as total
                FROM
                    public.items_group
                WHERE
                    options @> \'"is_stock"=>"1"\'::hstore
            ',
            [

            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getGroupsByFilters(  $filter_applied, $price_array, $type, $subtype )
    {
        if( !empty( $filter_applied ) && empty( $price_array ) )
        {
            $sql = 'filter_id IN ('.join(',',$filter_applied).')';
        }
        elseif( empty( $filter_applied ) && !empty( $price_array ) )
        {
            $sql =
                'item_id IN
                (
                    SELECT
                        id
                    FROM
                        public.items
                    WHERE
                        price2 >= '.$price_array['0'].'
                        AND
                        price2 <= '.$price_array['1'].'
                )';
        }
        else
        {
            $sql =
                'filter_id IN ('.join(',',$filter_applied).')
                AND
                item_id IN
                (
                    SELECT
                        id
                    FROM
                        public.items
                    WHERE
                        price2 >= '.$price_array['0'].'
                        AND
                        price2 <= '.$price_array['1'].'
                )';
        }

        return $this->get(
            '
                SELECT
                    group_id,
                    (
                        SELECT
                            filter_key_id
                        FROM
                            public.filters
                        WHERE
                            id = public.filters_items.filter_id
                        LIMIT
                            1
                    ) as key_id
                FROM
                    public.filters_items
                WHERE
                    '.$sql.'
                    AND
                    type = :type
                    AND
                    subtype = :subtype
                ORDER BY
                    group_id DESC'
            ,
            [
                'type'      => $type,
                'subtype'   => $subtype,
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getResultGroups( $lang_id, $result_groups, $filter_applied, $price_array, $sort, $page )
    {
        if( !empty( $filter_applied ) && empty( $price_array ) )
        {
            $sql =
                'id IN
                (
                    SELECT
                        item_id
                    FROM
                        filters_items
                    WHERE
                        filter_id in ('.join(',',$filter_applied).')
                )';
        }
        elseif( empty( $filter_applied ) && !empty( $price_array ) )
        {
            $sql =
                'price2 >= '.$price_array['0'].'
                AND
                price2 <= '.$price_array['1'];
        }
        else
        {
            $sql =
                'id IN
                (
                    SELECT
                        item_id
                    FROM
                        filters_items
                    WHERE
                        filter_id in ('.join(',',$filter_applied).')
                )
                AND
                price2 >= '.$price_array['0'].'
                AND
                price2 <= '.$price_array['1'];
        }

        $order = '';

        if( in_array( 1, $sort ) )
        {
            $order = 'new ASC';
        }
        if( in_array( 2, $sort ) )
        {
            $order = 'top ASC';
        }
        if( in_array( 3, $sort ) )
        {
            $order .= ',min_price ASC';
        }
        if( in_array( 4, $sort ) )
        {
            $order .= ',min_price DESC';
        }
        if( in_array( 3, $sort ) && in_array( 0, $sort ) )
        {
            $order = 'min_price ASC';
        }
        if( in_array( 4, $sort ) && in_array( 0, $sort ) )
        {
            $order = 'min_price DESC';
        }
//p($sql,1);
        return $this->get(
            '
                SELECT
                    group_id,
                    cover,
                    options,
                    options->\'is_new\' AS new,
                    options->\'is_top\' AS top,
                    (
                        SELECT
                            alias
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                    ) AS alias,
                    (
                        SELECT
                            type
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS type_id,
                    (
                        SELECT
                            subtype
                        FROM
                            items_group_alias
                        WHERE
                            group_id = public.items_group.group_id
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) AS subtype_id,
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_group.group_id
                                    AND
                                    '.$sql.'
                                LIMIT 1
                            )
                            AND
                            group_id = public.items_group.group_id
                            AND '.$sql.'
                        LIMIT 1
                    ) as id,
                    (
                    SELECT
                        price2
                    FROM
                        public.items
                    WHERE
                        group_id = public.items_group.group_id
                        AND
                        price2 IN
                        (
                            SELECT
                                MIN(price2)
                            FROM
                                public.items
                            WHERE
                                group_id = public.items_group.group_id
                            LIMIT 1
                        )
                        LIMIT 1
                ) AS min_price
                FROM
                    public.items_group
                WHERE
                    status = 1
                    AND
                    group_id IN ('.join( ',', $result_groups ).')

                ORDER BY
                    '.$order.'
                LIMIT
                    '.\config::get( 'limits/items' ).'
                OFFSET
                    '.($page-1)*(\config::get( 'limits/items' ))
            ,
            [
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getItemsByIds( $lang_id, $item_ids )
    {
        return $this->get(
            '
                SELECT
                    id,
                    price2,
                    size,
                    type,
                    subtype,
                    group_id,
                    (
                        SELECT
                            cover
                        FROM
                            public.items_group
                        WHERE
                            group_id = public.items.group_id
                    ) AS group_cover,
                    (
                        SELECT
                            options
                        FROM
                            public.items_group
                        WHERE
                            group_id = public.items.group_id
                    ) AS options,
                    (
                        SELECT
                            alias
                        FROM
                            public.items_group_alias
                        WHERE
                            group_id = public.items.group_id
                            AND
                            lang_id = :lang_id
                    ) AS group_alias,
                    (
                        SELECT
                            title
                        FROM
                            public.items_i18n
                        WHERE
                            item_id = public.items.id
                            AND
                            lang_id = :lang_id
                    ) AS title,
                    (
                        SELECT
                            description
                        FROM
                            public.items_i18n
                        WHERE
                            item_id = public.items.id
                            AND
                            lang_id = :lang_id
                    ) AS description,
                    (
                        SELECT
                            content_description
                        FROM
                            public.items_i18n
                        WHERE
                            item_id = public.items.id
                            AND
                            lang_id = :lang_id
                    ) AS content_description,
                    (
                        SELECT
                            alias
                        FROM
                            public.types_i18n
                        WHERE
                            type = public.items.type
                            AND
                            lang_id = :lang_id
                    ) AS type_alias,
                    (
                        SELECT
                            alias
                        FROM
                            public.subtypes_i18n
                        WHERE
                            type = public.items.type
                            AND
                            subtype = public.items.subtype
                            AND
                            lang_id = :lang_id
                    ) AS subtype_alias
                FROM
                    public.items
                WHERE
                    id IN ('.join( ',', $item_ids ).')
            ',
            [
                'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getItemsByTerm( $term, $search_for, $page = 1 )
    {
        if( is_numeric( $term ) )
        {
            $sql =
                'item_id::text ILIKE \'%'.$term.'%\'';
        }
        else
        {
            $sql =
                '(
                    title::text ILIKE \'%'.$term.'%\'
                    OR
                    content_description::text ILIKE \'%'.$term.'%\'
                )
                AND
                item_id IN
                (
                    SELECT
                        id
                    FROM
                        public.items
                    WHERE
                        price2 IN
                        (
                            SELECT
                                MIN(price2)
                            FROM
                                public.items
                            WHERE
                                group_id = public.items_i18n.group_id
                        )
                        AND
                        status = 1
                )';
        }

        $data = $this->get(
            '
                SELECT
                    item_id
                FROM
                    public.items_i18n
                WHERE
                    '.$sql.'
                LIMIT
                    '.\config::get( 'limits/'.$search_for ).'
                OFFSET
                    '.($page-1)*(\config::get( 'limits/'.$search_for ))

            ,
            [
                //'lang_id' => $lang_id
            ],
            -1
        );

        return $data;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTotalItemsByTerm( $term )
    {
        return $this->get(
            '
                SELECT
                    COUNT(item_id) as total
                FROM
                    public.items_i18n
                WHERE
                    (
                        item_id::text ILIKE \'%'.$term.'%\'
                        OR
                        title::text ILIKE \'%'.$term.'%\'
                        OR
                        content_description::text ILIKE \'%'.$term.'%\'
                    )
                    AND
                    item_id IN
                    (
                        SELECT
                            id
                        FROM
                            public.items
                        WHERE
                            price2 IN
                            (
                                SELECT
                                    MIN(price2)
                                FROM
                                    public.items
                                WHERE
                                    group_id = public.items_i18n.group_id
                            )
                            AND
                            status = 1
                    )
                ',
            [
                //'lang_id' => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getTypeSubtypeByTerm( $term, $lang_id )
    {
        return $this->get(
            '
                SELECT
                    type,
                    (
                        SELECT
                            title
                        FROM
                            public.types_i18n
                        WHERE
                            type = public.items.type
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) as type_title,
                    (
                        SELECT
                            alias
                        FROM
                            public.types_i18n
                        WHERE
                            type = public.items.type
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) as type_alias,
                    subtype,
                    (
                        SELECT
                            title
                        FROM
                            public.subtypes_i18n
                        WHERE
                            type = public.items.type
                            AND
                            subtype = public.items.subtype
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) as subtype_title,
                    (
                        SELECT
                            alias
                        FROM
                            public.subtypes_i18n
                        WHERE
                            type = public.items.type
                            AND
                            subtype = public.items.subtype
                            AND
                            lang_id = :lang_id
                        LIMIT
                            1
                    ) as subtype_alias
                FROM
                    public.items
                WHERE
                    id IN
                    (
                        SELECT
                            item_id
                        FROM
                            public.items_i18n
                        WHERE
                            item_id::text ILIKE \'%'.$term.'%\'
                            OR
                            title::text ILIKE \'%'.$term.'%\'
                            OR
                            content_description::text ILIKE \'%'.$term.'%\'
                    )
                    AND
                    status = 1
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