<div id="content" class="clearfix">
    <div class="compare_items">
        <div class="breadcrumbs">
            <div class="inner">
                <div class="order_menu_shadow"></div>
                <ul class="clearfix">
                    <li class="float"><a href="/" title="Головна">Головна</a></li>
                    <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
                    <li class="float"><a href="#" title="Порівняння товарів" class="breadcrumbs_last">Порівняння товарів</a></li>
                </ul>
            </div>
        </div>

        <div class="inner clearfix">
            <h2>Порівняння товарів</h2>

            <div class="compare_items_table">
                <table class="" cellpadding="0" cellspacing="0">
                    <tr>
                        <th></th>

                        <?php

                        if( !empty( $items ) )
                        {
                            $data_items = '';

                            foreach( $items as $i )
                            {
                                $data_items .=
                                    '<th valign="top">'.
                                        '<div class="compare_one_item">'.
                                            '<div class="compare_item_delete"><a href="'.$i['alias_del'].'" title="'.$i['title'].'" data-item_id="'.$i['type'].'-'.$i['subtype'].'-'.$i['id'].'"></a></div>'.
                                            '<div class="compare_item_image"><a href="'.$i['alias'].'" title="'.$i['title'].'"><img src="'.$i['cover'].'" alt="'.$i['title'].'" height="100" /></a></div>'.
                                            '<div class="compare_item_title"><a href="'.$i['alias'].'" title="'.$i['title'].'">'.$i['title'].'</a></div>'.
                                            '<div class="align_bottom">'.
                                                '<div class="compare_item_price">ціна від <span>'.$i['price2'].'</span> грн</div>'.
                                                '<div class="one_item_buttons"><a href="'.$i['alias'].'" title="'.$i['title'].'" class="btn green">придбати</a></div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</th>';
                            }

                            echo($data_items);
                        }

                        ?>
                    </tr>

                    <?php

                    if( !empty( $properties_for_items ) )
                    {
                        $data   = '';
                        $j      = 0;
                        $i      = 0;

                        foreach( $properties_for_items as $key => $val )
                        {
                            $j++;
                            $data .= '<tr class="'.( ($j%2==0) ? 'odd' : 'even' ).'" >';
                            $data .= '<td class="compare_item_property_name">'.$key.'</td>';

                            for($i = 0; $i < $count; $i++)
                            {
                                $data .= '<td>'.(!empty($val[$i]) ? $val[$i] : '-').'</td>';
                            }

                            $data .= '</tr>';
                        }

                        echo($data);
                    }

                    ?>



                </table>
            </div>
        </div>


    </div>
</div>