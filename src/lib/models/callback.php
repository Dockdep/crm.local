<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class callback extends \db
{

    /////////////////////////////////////////////////////////////////////////////

    public function addCallback( $callback )
    {
        return $this->get(
            '
                INSERT INTO
                    public.callback
                        (
                            name,
                            customer_id,
                            email,
                            phone,
                            comments
                        )
                        VALUES
                        (
                            :name,
                            :customer_id,
                            :email,
                            :phone,
                            :comments
                        )
                        RETURNING id
            ',
            [
                'name'          => $callback['name'],
                'customer_id'   => $callback['id'],
                'email'         => $callback['email'],
                'phone'         => $callback['phone'],
                'comments'      => $callback['comments']
            ],
            -1
        );
    }

    /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////