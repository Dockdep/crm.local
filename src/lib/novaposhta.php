<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace
{
    /**
     * novaposhta
     *
     * @author      Jane Bezmaternykh
     * @version     0.1.20140506
     */
    class novaposhta extends \core
    {
        /////////////////////////////////////////////////////////////////////////////

        protected $_out_city                = 'Київ';
        protected $_sender_city_ref         = 'Київ';
        protected $_out_company             = 'Професійне насіння';
        protected $_out_warehouse           = '1';
        protected $_sender_warehouse_ref    = '1';
        protected $_out_name                = 'Петров Иван Иваныч';
        protected $_out_phone               = '0671234567';
        protected $_api_key                 = 'f77cc4bcaa985eb481ed289b89e6fb9e';
        protected $_description             = 'Насіння';
        protected $_pack                    = 'Пакет';
        protected $_rcpt_name               = 'Приватна особа';

        /////////////////////////////////////////////////////////////////////////////

        /**
         * Функция отправки запроса на сервер Новой почты
        $xml — запрос
         */
        public function send($xml)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://orders.novaposhta.ua/xml.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * Запрос на расчёт стоимости доставки
        $to_city — город получатель
        $weight — вес
        $pub_price — заявленная стоимость
        $height — высота коробки
        $width — ширина коробки
        $depth — длинна коробки
         */

        public function price( $to_city, $pub_price, $weight, $height = 50, $width = 20, $depth = 20 )
        {
            $xml=
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                <auth>'.$this->_api_key.'</auth>
                <countPrice>
                    <senderCity>'.$this->_out_city.'</senderCity>
                    <recipientCity>'.$to_city.'</recipientCity>
                    <mass>'.$weight.'</mass>
                    <height>'.$height.'</height>
                    <width>'.$width.'</width>
                    <depth>'.$depth.'</depth>
                    <publicPrice>'.$pub_price.'</publicPrice>
                    <deliveryType_id>4</deliveryType_id>
                    <floor_count>0</floor_count>
                    <date>'.date( 'd.m.Y' ).'</date>
                </countPrice>
                </file>';

            //p($xml,1);

            $xml = simplexml_load_string($this->send($xml));

            return strval($xml->cost);
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * Запрос на создание декларации на отправку
        $order_id — номер заказа на вашем сайте (для вашего удобства)
        $city — город получения
        $warehouse — номер склада получения
        $name — имя получателя
        $phone — телефон получателя
        $weight — вес посылки
        $pub_price — заявленная стоимость
        $date — дата отправки
        $payer — плательщик (1 — получатель, 0 — отправитель, 2 — третья сторона)
         */

        /////////////////////////////////////////////////////////////////////////////

        public function ttn( $order_id, $city, $warehouse, $street_name, $name, $phone, $weight, $pub_price, $payer=0)
        {
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                <auth>'.$this->_api_key.'</auth>
                <order
                    order_id="'.$order_id.'"

                    sender_city="'.$this->_out_city.'"
                    sender_company="'.$this->_out_company.'"
                    sender_address="'.$this->_out_warehouse.'"
                    sender_contact="'.$this->_out_name.'"
                    sender_phone="'.$this->_out_phone.'"
                    rcpt_name="'.$this->_rcpt_name.'"
                    rcpt_city_name="'.$city.'" '.
                    (
                        !empty( $warehouse )
                        ?
                            'rcpt_warehouse="'.$warehouse.'" '
                        :
                            'rcpt_street_name="'.$street_name.'" '
                    ).
                    'rcpt_contact="'.$name.'"
                    rcpt_phone_num="'.$phone.'"

                    pack_type="'.$this->_pack.'"
                    description="'.$this->_description.'"

                    pay_type="1"
                    payer="'.$payer.'"

                    cost="'.$pub_price.'"
                    date="'.date( 'd.m.Y' ).'"
                    weight="'.$weight.'">
                    <order_cont
                        cont_description="'.$this->_description.'" />
                    </order>
                </file>';

            $xml = simplexml_load_string($this->send($xml));
            //return array('oid'=>$order_id,'ttn'=>trim($xml->order->attributes()->np_id));
            return strval($xml->order->attributes()->np_id);
        }

        /////////////////////////////////////////////////////////////////////////////

        public function ttn_ref( $order_id, $city_ref, $warehouse_ref, $street_name, $name, $phone, $weight, $pub_price, $payer=0)
        {
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                <auth>'.$this->_api_key.'</auth>
                <order
                    order_id="'.$order_id.'"
                    sender_city_ref="'.$this->_sender_city_ref.'"
                    sender_company="'.$this->_out_company.'"
                    sender_warehouse_ref="'.$this->_sender_warehouse_ref.'"
                    sender_contact="'.$this->_out_name.'"
                    sender_phone="'.$this->_out_phone.'"
                    rcpt_name="'.$this->_rcpt_name.'"
                    rcpt_city_ref="'.$city_ref.'" '.
                    (
                    !empty( $warehouse_ref )
                        ?
                        'rcpt_warehouse_ref="'.$warehouse_ref.'" '
                        :
                        'rcpt_street_name="'.$street_name.'" '
                    ).
                    'rcpt_contact="'.$name.'"
                    rcpt_phone_num="'.$phone.'"

                    pack_type="'.$this->_pack.'"
                    description="'.$this->_description.'"

                    pay_type="1"
                    payer="'.$payer.'"

                    cost="'.$pub_price.'"
                    date="'.date( 'd.m.Y' ).'"
                    weight="'.$weight.'">
                    <order_cont
                        cont_description="'.$this->_description.'" />
                    </order>
                </file>';

            $xml = simplexml_load_string($this->send($xml));
            //return array('oid'=>$order_id,'ttn'=>trim($xml->order->attributes()->np_id));
            return strval($xml->order->attributes()->np_id);
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * Запрос на удаление декларации из базы Новой почты
        $ttn — номер декларации, которую нужно удалить
         */
        public function remove($ttn)
        {
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                <auth>'.$this->_api_key.'</auth>
                <close>'.$ttn.'</close>
                </file>';

            $xml = simplexml_load_string($this->send($xml));

            return $xml;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * Запрос на печать маркировок для декларации (производит перенаправление на страницу печати)
        $ttn — номер декларации, которую нужно напечатать
         */
        public static function printit($ttn){
            header('location: http://orders.novaposhta.ua/pformn.php?o='.$ttn.'&num_copy=4&token='.NP::$api_key);
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * Запрос на получение списка складов Новой почты для определённого города (или полный список, если город не указан)
        $filter — город, по которому нужно отфильтровать список складов Новой почты
         */
        public function warenhouse($filter=false)
        {
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                <auth>'.$this->_api_key.'</auth>
                <warenhouse/>';
                if($filter)
                {
                    $xml.='<filter>'.$filter.'</filter>';
                }
                $xml.='</file>';

            $xml = simplexml_load_string($this->send($xml));

            return($xml->result->whs);
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * Запрос на получение списка населённых пунктов, в которых есть склады Новой почты
         */
        public function city()
        {
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                <auth>'.$this->_api_key.'</auth>
                <city/>
                </file>';

            $xml = simplexml_load_string( $this->send($xml) );

            return($xml->result->cities);
        }

        /////////////////////////////////////////////////////////////////////////////

        public function street( $filter )
        {
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                    <auth>'.$this->_api_key.'</auth>
                    <streets get_version="0"/>
                    <filter>c5f70e4c-4bd0-11dd-9198-001d60451983</filter>
                </file>';

            $xml = simplexml_load_string( $this->send($xml) );

            return($xml);
        }

        /////////////////////////////////////////////////////////////////////////////

        public function track( $en )
        {
            $xml =
                '<?xml version="1.0" encoding="utf-8"?>
                <file>
                    <auth>'.$this->_api_key.'</auth>
                    <track>
                        <en>'.$en.'</en>
                    </track>
                </file>';

            $xml = simplexml_load_string( $this->send($xml) );

            return($xml);
        }

        /////////////////////////////////////////////////////////////////////////////
    }

}
 