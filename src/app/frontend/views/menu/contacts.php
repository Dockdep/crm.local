<div id="content">
    <div class="contacts">
        <div class="breadcrumbs">
            <div class="inner">
                <div class="order_menu_shadow"></div>
                <ul class="clearfix">
                    <li class="float"><a href="/" title="Головна">Головна</a></li>
                    <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
                    <li class="float"><a href="/contacts" title="Контакти" class="breadcrumbs_last">Контакти</a></li>
                </ul>
            </div>
        </div>

        <div class="inner contacts_wrapper">
            <h2>Контакти</h2>

            <div class="clearfix">
                <div class="float contacts_wrapper_map">
                    <div class="map_description">
                        <p class="map_description_name">Центральний офіс </p>
                        <p>м. Київ вул.Садова 95</p>
                        <p>(Дачний масив Осокорки)</p>
                        <p>ТМ «Професійне насіння»</p>
                        <p>м. Київ, 02002 а/с 115</p>
                        <p>/044/ 451 48 59 </p>
                        <p>/044/ 581 67 15</p>
                        <p>/067/ 464 48 59 для абонентів КиївСтар </p>
                        <p>/050/ 464 48 59 для абонентів МТС </p>
                        <p><a href="mailto:info@hs.kiev.ua">info@hs.kiev.ua</a></p>
                    </div>
                    <div class="map">
                        <div id="google-map-contacts1" style="width: 100%; height: 300px;"></div>
                    </div>
                    <div class="map_description_after_map">
                        <p>GPS координати: </p>
                        <p>Широта: 50°21'39.63"N (50.361007) </p>
                        <p>Довгота: 30°36'27.35"Е (30.607597)</p>
                    </div>
                </div>


                <div class="float contacts_wrapper_map last">
                    <div class="map_description">
                        <p class="map_description_name">Оптовий Склад </p>
                        <p>м.Київ, вул.Віскозна 17/а </p>
                        <p>т. /044/ 454 12 15</p>
                    </div>
                    <div class="map">
                        <div id="google-map-contacts2" style="width: 100%; height: 300px;"></div>
                    </div>
                    <div class="map_description_after_map">
                        <p>GPS координати: </p>
                        <p>Широта: 50°21'39.63"N (50.361007) </p>
                        <p>Довгота: 30°36'27.35"Е (30.607597)</p>
                    </div>
                </div>
            </div>

            <div class="contacts_email_address">
                <p class="contacts_email_address_name">Поштова адреса:</p>
                <p>02002</p>
                <p>м. Київ</p>
                <p>а/с 115</p>
            </div>

            <div class="contacts_email_address clearfix">
                <p class="contacts_email_address_name">Адреси роздрібних магазинів ТМ ‎"Професійне насіння"</p>

                <table class="contacts_list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th>Місто</th>
                        <th>Адреса</th>
                        <th>Телефон</th>
                        <th>Карта проїзду</th>
                    </tr>

                    <?php

                    if( !empty( $shops ) )
                    {
                        $data_shops = '';

                        foreach( $shops as $s )
                        {
                            $data_shops .=
                                '<tr>'.
                                    '<td class="contacts_list_phone">'.$s['city'].'</td>'.
                                    '<td>'.$s['address'].'</td>'.
                                    '<td class="contacts_list_phone">'.$s['phone'].'</td>'.
                                    '<td class="contacts_list_phone">'.(!empty($s['map']) ? '<a href="'.$s['map'].'" title="Карта проїзду" target="_blank" rel="no-follow">Карта проїзду</a>' : '').'</td>'.
                                '</tr>';
                        }

                        echo( $data_shops );
                    }

                    ?>

                </table>

            </div>

            <?= $this->partial('partial/share'); ?>

        </div>



    </div>
</div>