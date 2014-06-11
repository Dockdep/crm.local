<div id="content" class="clearfix">
    <div class="partners">
        <div class="breadcrumbs">
            <div class="inner">
                <div class="order_menu_shadow"></div>
                <ul class="clearfix">
                    <li class="float"><a href="/" title="Головна">Головна</a></li>
                    <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
                    <li class="float"><a href="/news-actions" title="Партнери/диллери" class="breadcrumbs_last">Партнери/диллери</a></li>
                </ul>
            </div>
        </div>

        <div class="inner clearfix">
            <h2>Партнери/диллери</h2>

            <p class="partners_name_title_internet_shops">
                Інтернет партнери
            </p>
            <?php

            if( !empty( $internet_shops ) )
            {
                $data_internet_shops = '<ul class="internet_shops_list">';

                foreach( $internet_shops as $p )
                {
                    $data_internet_shops .=
                        '<li class="clearfix">'.
                        '<span class="float">Інтернет магазин</span>'.
                        '<span class="float">&nbsp-&nbsp</span>'.
                        '<a class="float" href="#" rel="no-follow" target="_blank" title="">'.$p['website'].'</a>'.
                        '<span class="float">&nbsp</span>'.
                        '<span class="float">'.$p['title'].',&nbsp</span>'.
                        '<span class="float">'.$p['city'].',&nbsp</span>'.
                        '<span class="float">'.$p['phone'].'</span>'.
                        '</li>';
                }

                $data_internet_shops .= '</ul>';

                echo($data_internet_shops);
            }

            ?>

            <p class="partners_name_title_dillers">
                Диллери
            </p>

            <?php

            if( !empty( $dillers ) )
            {
                $data_dillers =
                    '<table>'.
                        '<tr>'.
                            '<th>Назва магазину/організації</th>'.
                            '<th>Область</th>'.
                            '<th>Місто</th>'.
                            '<th>Телефон</th>'.
                        '</tr>';

                foreach( $dillers as $k => $p )
                {
                    $data_dillers .=
                        '<tr class="dillers_district">'.
                            '<td colspan="4">'.$k.'</td>'.
                        '</tr>';


                    foreach( $p as $v )
                    {
                        $data_dillers .=
                            '<tr>'.
                                '<td>'.$v['title'].'</td>'.
                                '<td>'.$v['district'].'</td>'.
                                '<td>'.$v['city'].'</td>'.
                                '<td>'.$v['phone'].'</td>'.
                            '</tr>';
                    }
                }

                $data_dillers .= '</table>';

                echo($data_dillers);
            }

            ?>

        </div>


    </div>
</div>