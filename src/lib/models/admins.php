<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class admins extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function adminLogin( $registration )
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
                        public.admins
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
                            public.admins
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
                    $this->getDi()->get('session')->set( 'isAdminAuth',      true );
                    $this->getDi()->get('session')->set( 'adminId',          $data['0']['id'] );
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
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////