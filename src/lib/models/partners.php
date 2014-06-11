<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace models;

class partners extends \db
{
    /////////////////////////////////////////////////////////////////////////////

    public function getPartners( $lang_id )
    {
        $data = $this->get(
            '
                SELECT
                    id,
                    lat,
                    lng,
                    email,
                    website,
                    district_id,
                    (
                        SELECT
                            title
                        FROM
                            public.districts
                        WHERE
                            lang_id = :lang_id
                            AND
                            id = public.partners.district_id
                    ) as district,
                    phone,
                    shop_type,
                    (
                        SELECT
                            title
                        FROM
                            public.partners_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            parthner_id = public.partners.id
                    ) as title,
                    (
                        SELECT
                            city
                        FROM
                            public.partners_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            parthner_id = public.partners.id
                    ) as city,
                    (
                        SELECT
                            address
                        FROM
                            public.partners_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            parthner_id = public.partners.id
                    ) as address
                FROM
                    public.partners
                WHERE
                    shop_type IN (1,2)

            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );

        return $data;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function getContactsShops( $lang_id )
    {
        $data = $this->get(
            '
                SELECT
                    id,
                    phone,
                    map,
                    (
                        SELECT
                            city
                        FROM
                            public.partners_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            parthner_id = public.partners.id
                    ) as city,
                    (
                        SELECT
                            address
                        FROM
                            public.partners_i18n
                        WHERE
                            lang_id = :lang_id
                            AND
                            parthner_id = public.partners.id
                    ) as address
                FROM
                    public.partners
                WHERE
                    shop_type = 3
                    AND
                    status = 1

            ',
            [
                'lang_id'   => $lang_id
            ],
            -1
        );

        return $data;
    }

    /////////////////////////////////////////////////////////////////////////////
}