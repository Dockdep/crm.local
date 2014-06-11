<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class customers extends \db
{

    /////////////////////////////////////////////////////////////////////////////

    public function addNewCustomer( $order )
    {
        return $this->get(
            '
                INSERT INTO
                    public.customers
                        (
                            name,
                            email,
                            passwd,
                            phone,
                            city,
                            address,
                            delivery,
                            pay,
                            subscribed,
                            comments
                        )
                        VALUES
                        (
                            :name,
                            :email,
                            :passwd,
                            :phone,
                            :city,
                            :address,
                            :delivery,
                            :pay,
                            :subscribed,
                            :comments
                        )
                        RETURNING id
            ',
            [
                'name'          => $order['name'],
                'email'         => $order['email'],
                'passwd'        => $order['passwd'],
                'phone'         => $order['phone'],
                'city'          => $order['city'],
                'address'       => $order['address'],
                'delivery'      => $order['delivery'],
                'pay'           => $order['pay'],
                'subscribed'    => $order['subscribed'],
                'comments'      => $order['comments']
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function finishRegistration( $registration )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data_status = $this->exec(
                '
                    UPDATE
                        public.customers
                    SET
                        passwd          = :passwd,
                        status          = :status,
                        lastlogin_date  = :lastlogin_date
                    WHERE
                        email           = :email
                ',
                [
                    'status'            => 1,
                    'passwd'            => $registration['passwd'],
                    'email'             => $registration['email'],
                    'lastlogin_date'    => date( 'Y-m-d H:i' )
                ]
            );

            $data_id =  $this->get(
                '
                    SELECT
                        id
                    FROM
                        public.customers
                    WHERE
                        email = :email
                    LIMIT
                        1
                ',
                [
                    'email' => $registration['email']
                ],
                -1
            );

            $this->getDi()->get('session')->set( 'isAuth',          true );
            $this->getDi()->get('session')->set( 'id',              $data_id['0']['id'] );
            $this->getDi()->get('session')->set( 'customer_email',  NULL );
            $this->getDi()->get('session')->remove('customer_email');

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

    public function resetPasswd( $registration )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data_customer_id = $this->get(
                '
                    SELECT
                        customer_id
                    FROM
                        public.customers_confirm
                    WHERE
                        confirm_key = :confirm_key
                    LIMIT
                        1
                ',
                [
                    'confirm_key' => $registration['confirm_key']
                ],
                -1
            );

            if( !empty( $data_customer_id ) )
            {

                $data_status = $this->exec(
                    '
                        UPDATE
                            public.customers
                        SET
                            passwd          = :passwd,
                            status          = :status,
                            lastlogin_date  = :lastlogin_date
                        WHERE
                            id              = :id
                    ',
                    [
                        'status'            => 1,
                        'passwd'            => $registration['passwd'],
                        'id'                => $data_customer_id['0']['customer_id'],
                        'lastlogin_date'    => date( 'Y-m-d H:i' )
                    ]
                );

                $data_delete = $this->exec(
                    '
                        DELETE
                        FROM
                            public.customers_confirm
                        WHERE
                            confirm_key  = :confirm_key
                    ',
                    [
                        'confirm_key'     => $registration['confirm_key']
                    ]
                );

                $this->getDi()->get('session')->set( 'isAuth',          true );
                $this->getDi()->get('session')->set( 'id',              $data_customer_id['0']['customer_id'] );
                //$this->getDi()->get('session')->set( 'customer_email',  NULL );
                //$this->getDi()->get('session')->remove('customer_email');

                $result = 1;

            }
            else
            {
                $result = 0;
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

    public function customerLogin( $registration )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data =  $this->get(
                '
                    SELECT
                        id,
                        status
                    FROM
                        public.customers
                    WHERE
                        email = :email
                        AND
                        passwd = :passwd
                    LIMIT
                        1
                ',
                [
                    'email' => $registration['email'],
                    'passwd' => $registration['passwd'],
                ],
                -1
            );

            $result = 0;

            if( !empty($data) )
            {
                $this->exec(
                    '
                        UPDATE
                            public.customers
                        SET
                            lastlogin_date  = :lastlogin_date
                        WHERE
                            id     = :id
                    ',
                    [
                        'id'                => $data['0']['id'],
                        'lastlogin_date'    => date( 'Y-m-d H:i' )
                    ]
                );

                if( $data['0']['status'] == 1 )
                {
                    $result = 1;

                    // auth user
                    $this->getDi()->get('session')->set( 'isAuth',      true );
                    $this->getDi()->get('session')->set( 'id',          $data['0']['id'] );
                }
                else
                {
                    $result = 2; // user with status 0
                }

                unset($data);
            }
            else
            {
                $result = -1;
            }

            $connection->commit();

            return $result;

        }
        catch(\Exception $e)
        {
            $connection->rollback();
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getCustomer( $customer_id )
    {
        return $this->get(
            '
                SELECT
                    name,
                    email,
                    birth_date,
                    phone,
                    city,
                    address,
                    delivery,
                    pay,
                    subscribed,
                    comments
                FROM
                    public.customers
                WHERE
                    id = :id
                LIMIT
                    1
            ',
            [
                'id' => $customer_id
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getCustomerName( $customer_id )
    {
        $data =  $this->get(
            '
                SELECT
                    name
                FROM
                    public.customers
                WHERE
                    id = :id
                LIMIT
                    1
            ',
            [
                'id' => $customer_id
            ],
            -1
        );

        return $data['0']['name'];
    }

    /////////////////////////////////////////////////////////////////////////////

    public function editCustomer( $customer_edit )
    {
        return $this->exec(
            '
                UPDATE
                    public.customers
                SET
                    name        = :name,
                    birth_date  = :birth_date,
                    phone       = :phone,
                    city        = :city,
                    address     =:address,
                    delivery    =:delivery,
                    pay         = :pay,
                    subscribed  = :subscribed,
                    comments    = :comments
                WHERE
                    id     = :id
            ',
            [
                'id'            => $customer_edit['id'],
                'name'          => $customer_edit['name'],
                'birth_date'    => $customer_edit['birth_date'],
                'phone'         => $customer_edit['phone'],
                'city'          => $customer_edit['city'],
                'address'       => $customer_edit['address'],
                'delivery'      => $customer_edit['delivery'],
                'pay'           => $customer_edit['pay'],
                'subscribed'    => $customer_edit['subscribed'],
                'comments'      => $customer_edit['comments'],
            ]
        );
    }

    /////////////////////////////////////////////////////////////////////////////

    public function editCustomerPasswd( $customer_edit_passwd )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data =  $this->get(
                '
                    SELECT
                        passwd
                    FROM
                        public.customers
                    WHERE
                        id = :id
                    LIMIT
                        1
                ',
                [
                    'id' => $customer_edit_passwd['id']
                ],
                -1
            );

            if( $data['0']['passwd'] == $customer_edit_passwd['previous_passwd'] )
            {
                $data_change = $this->exec(
                    '
                        UPDATE
                            public.customers
                        SET
                            passwd  = :passwd
                        WHERE
                            id      = :id
                    ',
                    [
                        'id'                => $customer_edit_passwd['id'],
                        'passwd'            => $customer_edit_passwd['passwd']
                    ]
                );

                $result = $data_change ? 1 : -1;
            }
            else
            {
                $result = -1;
            }

            unset($data);

            $connection->commit();

            return $result;

        }
        catch(\Exception $e)
        {
            $connection->rollback();
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function registrateCustomer( $registration )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data =  $this->get(
                '
                    SELECT
                        status
                    FROM
                        public.customers
                    WHERE
                        email = :email
                    LIMIT
                        1
                ',
                [
                    'email' => $registration['email']
                ],
                -1
            );

            if( empty( $data ) ) // new customer
            {
                $data_add_customer =  $this->get(
                    '
                        INSERT INTO
                            public.customers
                                (
                                    name,
                                    email,
                                    passwd,
                                    status
                                )
                                VALUES
                                (
                                    :name,
                                    :email,
                                    :passwd,
                                    0
                                )
                                RETURNING id
                    ',
                    [
                        'name'          => $registration['name'],
                        'email'         => $registration['email'],
                        'passwd'        => $registration['passwd'],
                    ],
                    -1
                );

                $confirm_key = $this->get(
                    '
                            INSERT INTO
                                public.customers_confirm
                                (
                                    customer_id,
                                    confirm_key
                                )
                                VALUES
                                (
                                    :customer_id,
                                    :confirm_key
                                )
                            RETURNING
                                confirm_key
                        ',
                    [
                        'customer_id' => $data_add_customer['0']['id'],
                        'confirm_key' => md5( rand() )
                    ],
                    -1
                );
            }
            else
            {
                $confirm_key['0']['confirm_key'] = false;
            }

            $connection->commit();

            return $confirm_key['0']['confirm_key'];

        }
        catch(\Exception $e)
        {
            $connection->rollback();
            //p($e->getMessage());
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function checkCustomerByConfirmKey( $confirm_key )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data =  $this->get(
                '
                    SELECT
                        customer_id
                    FROM
                        public.customers_confirm
                    WHERE
                        confirm_key = :confirm_key
                    LIMIT
                        1
                ',
                [
                    'confirm_key' => $confirm_key
                ],
                -1
            );

            if( !empty( $data ) ) // customer isset
            {
                $data_delete = $this->exec(
                    '
                        DELETE
                        FROM
                            public.customers_confirm
                        WHERE
                            confirm_key  = :confirm_key
                    ',
                    [
                        'confirm_key'     => $confirm_key
                    ]
                );

                $data_update = $this->exec(
                    '
                        UPDATE
                            public.customers
                        SET
                            status          = 1
                        WHERE
                            id              = :id
                    ',
                    [
                        'id'                => $data['0']['customer_id'],
                    ]
                );

                $this->getDi()->get('session')->set( 'isAuth',          true );
                $this->getDi()->get('session')->set( 'id',              $data['0']['customer_id'] );
                $this->getDi()->get('session')->set( 'customer_email',  NULL );
                $this->getDi()->get('session')->remove('customer_email');

                $result = 1;
            }
            else
            {
                $result = 0;
            }

            $connection->commit();

            return $result;

        }
        catch(\Exception $e)
        {
            $connection->rollback();
            //p($e->getMessage());
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function restorePasswd( $email )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data =  $this->get(
                '
                    SELECT
                        id,
                        name
                    FROM
                        public.customers
                    WHERE
                        email = :email
                    LIMIT
                        1
                ',
                [
                    'email' => $email
                ],
                -1
            );

            if( !empty( $data ) ) // customer isset
            {
                $confirm_key = $this->get(
                    '
                            INSERT INTO
                                public.customers_confirm
                                (
                                    customer_id,
                                    confirm_key
                                )
                                VALUES
                                (
                                    :customer_id,
                                    :confirm_key
                                )
                            RETURNING
                                confirm_key
                        ',
                    [
                        'customer_id' => $data['0']['id'],
                        'confirm_key' => md5( rand() )
                    ],
                    -1
                );

                $result =
                    [
                        'name'          =>  $data['0']['name'],
                        'confirm_key'   =>  $confirm_key['0']['confirm_key']
                    ];
            }
            else
            {
                $result = 0;
            }

            $connection->commit();

            return $result;

        }
        catch(\Exception $e)
        {
            $connection->rollback();
            p($e->getMessage());
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function LoginOrRegisterSocial( $registration )
    {
        $connection = $this->database;

        try
        {
            $connection->begin();

            $data =  $this->get(
                '
                    SELECT
                        name,
                        id
                    FROM
                        public.customers
                    WHERE
                        email = :email
                    LIMIT
                        1
                ',
                [
                    'email' => $registration['email']
                ],
                -1
            );

            if( empty( $data ) ) // new customer
            {
                $data_add_customer =  $this->get(
                    '
                        INSERT INTO
                            public.customers
                                (
                                    name,
                                    email,
                                    passwd,
                                    birth_date,
                                    status
                                )
                                VALUES
                                (
                                    :name,
                                    :email,
                                    :passwd,
                                    :birth_date,
                                    1
                                )
                                RETURNING id
                    ',
                    [
                        'name'          => $registration['name'],
                        'email'         => $registration['email'],
                        'birth_date'    => $registration['bithday'],
                        'passwd'        => $registration['passwd'],
                    ],
                    -1
                );

                $id = $data_add_customer['0']['id'];
            }
            else
            {
                $id = $data['0']['id'];
            }

            $this->getDi()->get('session')->set( 'isAuth',      true );
            $this->getDi()->get('session')->set( 'id',          $id );

            $connection->commit();

            return true;

        }
        catch(\Exception $e)
        {
            $connection->rollback();
            //p($e->getMessage());
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////