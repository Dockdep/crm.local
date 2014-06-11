<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class orders extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function addOrder( $order )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            if( !empty( $order['email'] ) )
            {
                $data_customer_isset = $this->get(
                    '
                        SELECT
                            id,
                            status
                        FROM
                            public.customers
                        WHERE
                            email = :email
                        LIMIT
                            1
                    ',
                    [
                        'email' => $order['email']
                    ],
                    -1
                );
            }
            else
            {
                $data_customer_isset = $this->get(
                    '
                        SELECT
                            id,
                            status
                        FROM
                            public.customers
                        WHERE
                            phone = :phone
                        LIMIT
                            1
                    ',
                    [
                        'phone' => $order['phone']
                    ],
                    -1
                );
            }

            if( empty( $data_customer_isset ) ) // new customer
            {
                $data_confirm_key   = $this->getDi()->get('models')->getCustomers()->addNewCustomer( $order );
                $confirmed          = 0;
                $new                = 1;
                $customer_id        = $data_confirm_key['0']['id'];
            }
            elseif( !empty( $data_customer_isset ) and $data_customer_isset['0']['status'] == 0 ) // the customer do not finish registration
            {
                $confirmed          = 0;
                $new                = 0;
                $customer_id        = $data_customer_isset['0']['id'];
            }
            elseif( !empty( $data_customer_isset ) and $data_customer_isset['0']['status'] == 1 ) // the customer finish registration
            {
                $confirmed          = 1;
                $new                = 0;
                $customer_id        = $data_customer_isset['0']['id'];
            }

            $data_orders = $this->get(
                '
                    INSERT INTO
                        public.orders
                            (
                                customer_id,
                                name,
                                phone,
                                city,
                                address,
                                delivery,
                                pay,
                                email,
                                comments
                            )
                            VALUES
                            (
                                :customer_id,
                                :name,
                                :phone,
                                :city,
                                :address,
                                :delivery,
                                :pay,
                                :email,
                                :comments
                            )
                            RETURNING id
                ',
                [
                    'customer_id'   => $customer_id,
                    'name'          => $order['name'],
                    'phone'         => $order['phone'],
                    'city'          => $order['city'],
                    'address'       => $order['address'],
                    'delivery'      => $order['delivery'],
                    'pay'           => $order['pay'],
                    'email'         => $order['email'],
                    'comments'      => $order['comments']
                ],
                -1
            );


            foreach( $order['items'] as $i )
            {
                $data_orders2items = $this->get(
                    '
                        INSERT INTO
                            public.orders2items
                                (
                                    order_id,
                                    item_id,
                                    item_count,
                                    price
                                )
                                VALUES
                                (
                                    :order_id,
                                    :item_id,
                                    :item_count,
                                    :price
                                )
                    ',
                    [
                        'order_id'      => $data_orders['0']['id'],
                        'item_id'       => $i['id'],
                        'item_count'    => $i['count'],
                        'price'         => $i['price2'],
                    ]
                );
            }

            if( $order['delivery'] == 3 || $order['delivery'] == 4 )
            {
                switch( $order['delivery'] )
                {
                    case 3:
                    default:
                        $order['rcpt_warehouse'] = substr( $order['store_address'], 0, strpos( $order['store_address'], '-' ) );
                        $order['novaposhta_tnn'] = $this->getDi()->get('novaposhta')->ttn( $data_orders['0']['id'], $order['city'], $order['rcpt_warehouse'], NULL, $order['name'], $order['phone'], '10', $order['total_sum'] );
                        //$order['novaposhta_tnn'] = $this->getDi()->get('novaposhta')->ttn_ref( $data_orders['0']['id'], $order['city_ref'], $order['store_ref'], NULL, $order['name'], $order['phone'], '10', $order['total_sum'] );
                    break;

                    case 4:
                        $order['novaposhta_tnn'] = $this->getDi()->get('novaposhta')->ttn( $data_orders['0']['id'], $order['city'], NULL, $order['address'], $order['name'], $order['phone'], '10', $order['total_sum'] );
                        break;

                }

                //p($order['novaposhta_tnn'],1);
                //$order['rcpt_warehouse'] = substr( $order['store_address'], 0, strpos( $order['store_address'], '-' ) );

                //$order['novaposhta_tnn'] = $this->getDi()->get('novaposhta')->ttn( $data_orders['0']['id'], $order['city'], $order['rcpt_warehouse'], NULL, $order['name'], $order['phone'], '10', $order['total_sum'] );
                //p($this->getDi()->get('novaposhta')->ttn( $data_orders['0']['id'], $order['city'], $order['rcpt_warehouse'], $order['name'], $order['phone'], '0,1', $order['total_sum'] ), 1);

                if( !empty( $order['novaposhta_tnn'] ) && !empty( $data_orders['0']['id'] ) )
                {
                    $this->addNovaposhtaTnn( $order['novaposhta_tnn'], $data_orders['0']['id'] );
                }
            }

            $data =
                [
                    'proposal_number'   => $data_orders['0']['id'],
                    'novaposhta_tnn'    => $order['novaposhta_tnn'],
                    'confirmed'         => $confirmed,
                    'new'               => $new,
                ];

            $connection->commit();

            return $data;
        }
        catch(\Exception $e)
        {
            $connection->rollback();
            //p($e->getMessage());
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function addNovaposhtaTnn( $tnn, $order_id )
    {
        return $this->exec(
            '
                UPDATE
                    public.orders
                SET
                    novaposhta_tnn  = :novaposhta_tnn
                WHERE
                    id     = :id
            ',
            [
                'novaposhta_tnn'    => $tnn,
                'id'                => $order_id
            ]
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getOrdersByCustomerId( $customer_id )
    {
        return $this->get(
            '
                SELECT
                    id,
                    created_date,
                    status,
                    delivery
                FROM
                    public.orders
                WHERE
                    customer_id = :customer_id
            ',
            [
                'customer_id' => $customer_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getOrdersByOrderId( $order_id, $lang_id )
    {
        return $this->get(
            '
                SELECT
                    item_id,
                    item_count,
                    (
                        SELECT
                            price2
                        FROM
                            public.items
                        WHERE
                            id = public.orders2items.item_id
                        LIMIT 1
                    ) AS price2,
                    (
                        SELECT
                            size
                        FROM
                            public.items
                        WHERE
                            id = public.orders2items.item_id
                        LIMIT 1
                    ) AS size,
                    (
                        SELECT
                            title
                        FROM
                            public.items_i18n
                        WHERE
                            item_id = public.orders2items.item_id
                            AND
                            lang_id = :lang_id
                        LIMIT 1
                    ) AS title,
                    (
                        SELECT
                            alias
                        FROM
                            public.items_group_alias
                        WHERE
                            group_id =
                            (
                                SELECT
                                    group_id
                                FROM
                                    public.items
                                WHERE
                                    id = public.orders2items.item_id
                                LIMIT 1
                            )
                            LIMIT 1
                    ) AS group_alias,
                    (
                        SELECT
                            cover
                        FROM
                            public.items_group
                        WHERE
                            group_id =
                            (
                                SELECT
                                    group_id
                                FROM
                                    public.items
                                WHERE
                                    id = public.orders2items.item_id
                                LIMIT 1
                            )
                            LIMIT 1
                    ) as cover,
                    (
                        SELECT
                            alias
                        FROM
                            public.types_i18n
                        WHERE
                            type =
                            (
                                SELECT
                                    type
                                FROM
                                    public.items
                                WHERE
                                    id = public.orders2items.item_id
                                LIMIT 1
                            )
                            AND
                            lang_id = :lang_id
                        LIMIT 1
                    ) AS type_alias,
                    (
                        SELECT
                            alias
                        FROM
                            public.subtypes_i18n
                        WHERE
                            subtype =
                            (
                                SELECT
                                    subtype
                                FROM
                                    public.items
                                WHERE
                                    id = public.orders2items.item_id
                                LIMIT 1
                            )
                            AND
                            type =
                            (
                                SELECT
                                    type
                                FROM
                                    public.items
                                WHERE
                                    id = public.orders2items.item_id
                                LIMIT 1
                            )
                            AND
                            lang_id = :lang_id
                    ) AS subtype_alias
                FROM
                    public.orders2items
                WHERE
                    order_id = :order_id
            ',
            [
                'order_id'  => $order_id,
                'lang_id'   => $lang_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////


}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////