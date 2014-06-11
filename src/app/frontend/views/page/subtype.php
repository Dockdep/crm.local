
<div id="content" class="clearfix">
<div class="subcategory">
<div class="catalog_slider">
    <div class="inner">
        <div class="catalog_description logo<?= $catalog['type_id'] ?>">
            <div class="catalog_description_image float">
                <?= '<a href="/'.$catalog['type_alias'].'" title="'.$catalog['type_title'].'"><img src="/images/types_logo/'.$catalog['type_id'].'.jpg" alt="'.$catalog['type_title'].'" width="99" height="99" /></a>' ?>
            </div>
            <div class="catalog_description_content float">
                <h2 class="catalog_description_title">
                    <?= '<a href="/'.$catalog['type_alias'].'" title="'.$catalog['type_title'].'">'.$catalog['type_title'].'</a>' ?>
                </h2>
                <p>
                    На відміну від поширеної думки Lorem Ipsum не є випадковим набором літер. Він походить з уривку.
                </p>
            </div>
        </div>
    </div>
</div>
<div class="breadcrumbs">
    <div class="inner">
        <ul class="clearfix">
            <li class="float"><a href="/" title="Головна">Головна</a></li>
            <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><a href="/#catalog" title="Каталог">Каталог</a></li>
            <li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><?= '<a href="'.$this->url->get([ 'for' => 'type', 'type' => $catalog['type_alias'] ]).'" title="'.$catalog['type_title'].'">'.$catalog['type_title'].'</a>' ?></li>
            <li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><?= '<a href="'.$this->url->get([ 'for' => 'subtype', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'] ]).'" title="'.$catalog['subtype_title'].'" class="breadcrumbs_last">'.$catalog['subtype_title'].'</a>' ?></li>
        </ul>
    </div>
</div>

<div class="sidebar_content_wrapper">
<div class="inner clearfix">
<div id="sidebar" class="float">
    <div class="subcategory_sidebar_title">
        <h3><?= $catalog['subtype_title'] ?></h3>
        <p>Знайдено товарів за фільтрами <?= $total ?></p>
    </div>
    <?php

    if( !empty( $filters ) )
    {
        $data_filters = '<ul id="subcategory_menu">';

        foreach( $filters as $key => $val )
        {
            $data_filters .=
                '<li>'.
                    '<div class="main clearfix">'.
                        '<p class="float">'.$key.'</p>'.
                        '<p class="float dropdown"></p>'.
                    '</div>'.
                    '<ul>';

                    foreach( $val as $v )
                    {
                        $data_filters .=
                            '<li>'.
                                '<a href="'.$v['alias'].'" title="" onClick="document.location=\''.$v['alias'].'\';">'.
                                    '<input type="checkbox" id="'.$v['filter_value_id'].'" value="'.$v['filter_value_id'].'" '.(!empty( $v['checked'] ) ? 'checked="checked"' : '').' />'.
                                    '<label for="'.$v['id'].'"><span></span>'.$v['filter_value_value'].'</label>'.
                                '</a>'.
                            '</li>';
                    }


             $data_filters .=
                    '</ul>'.
                '</li>';
        }

        $data_filters .=
                '<li class="subcategory_menu_last_child">
                    <div class="main subcategory_menu_price clearfix">
                        <p class="float">Ціна</p>
                    </div>
                    <div class="price_slider_container">
                        <div class="border_for_slider">
                            <div id="slider"></div>
                        </div>
                        <div>
                            <label for="price_from" class="float">від</label>
                            <input type="text" class="float" name="price_from" value="'.( isset($price_array) && !empty($price_array) ? $price_array['0'] : $max_min_price['min_price'] ).'" id="price_from" />
                            <label for="price_from" class="float">до</label>
                            <input type="text" class="float" name="price_to" value="'.( isset($price_array) && !empty($price_array) ? $price_array['1'] : $max_min_price['max_price'] ).'" id="price_to" />
                            <a href="'.$current_url.'" class="price_ok"><img src="/images/price_ok.png" width="7" height="7" alt="Ok" /></a>
                            <input type="hidden" value="'.$current_url_without_price.'" class="current_url">
                            <input type="hidden" value="'.$max_min_price['min_price'].'" class="min_price">
                            <input type="hidden" value="'.$max_min_price['max_price'].'" class="max_price">
                            <input type="hidden" value="'.( !empty($sort) ? join('-', $sort) : '' ).'" class="sort_params">
                        </div>
                    </div>
                </li>'.
            '</ul>';

        echo( $data_filters );
    }

    ?>

</div>
<div id="content_wrapper" class="float">


<?php

if( !empty( $groups ) )
{
    $data_items =
        '<div class="content_wrapper_header">'.
            '<div class="content_wrapper_header_filters clearfix">';

                if( !empty( $filters_applied ) )
                {
                    foreach( $filters_applied as $f )
                    {
                        $data_items .= '<div class="float"><a href="'.$f['alias'].'" title="">'.$f['filter_value_value'].'</a></div>';
                    }

                    $data_items .= '<div class="float empty_filters"><a href="'.$this->url->get([ 'for' => 'subtype', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'] ]).'" title="'.$catalog['subtype_title'].'" title="">Скинути всі фільтри</a></div>';
                }

            $data_items .=
            '</div>
            <div class="content_wrapper_header_menu change_sort clearfix">
                <div class="tabs float">
                    <ul>
                        <li class="tabs_all_items float '.( in_array( 1, $sort ) ? 'previous' : '' ).' '.( in_array( 0, $sort ) || empty( $sort ) ? 'active_tab' : 'not_active' ).' first_tab" onClick="document.location=\''.$this->url->get(['for' => 'subtype_sorted', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'], 'sort' => '0-'.$sort_default_2]).'\'">'.
                            '<a href="'.$this->url->get($page_url_for_sort['0']).'" title="">Всі</a>'.
                        '</li>

                        <li class="tabs_new_items float '.( in_array( 2, $sort ) ? 'previous' : '' ).' '.( in_array( 1, $sort ) ? 'active_tab' : 'not_active' ).'" onClick="document.location=\''.$this->url->get(['for' => 'subtype_sorted', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'], 'sort' => '1-'.$sort_default_2]).'\'">'.
                            '<a href="'.$this->url->get($page_url_for_sort['1']).'" title="">Новинки</a>'.
                        '</li>

                        <li class="tabs_top_items float  '.( in_array( 2, $sort ) ? 'active_tab' : 'not_active' ).' last_tab" onClick="document.location=\''.$this->url->get(['for' => 'subtype_sorted', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'], 'sort' => '2-'.$sort_default_2]).'\'">'.
                            '<a href="'.$this->url->get($page_url_for_sort['2']).'" title="">Топ продаж</a>'.
                        '</li>
                    </ul>
                </div>
                <div class="thumbs active float padding_60">
                    <a href="#" title=""></a>
                </div>
                <div class="lists float">
                    <a href="#" title="" class="float"></a>
                </div>
                <div class="sort_price float padding_60">
                    <span>Сортувати:</span>
                </div>
                <div class="sort_price float last">
                    <a href="#" title="">'.( in_array( 3, $sort ) ? 'від дешевих до дорогих' : 'від дорогих до дешевих' ).'</a>
                    <div class="sort_price_dropdown display_none">
                        <ul>
                            <li><a href="'.$this->url->get($page_url_for_sort['3']).'" title="">від дешевих до дорогих</a></li>
                            <li><a href="'.$this->url->get($page_url_for_sort['4']).'" title="">від дорогих до дешевих</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        ';


    $data_items .= '<div class="items clearfix">';

    foreach( $groups as $k => $i )
    {
        $data_items .=
            '<div class="one_item float '.( ($k+1)%3==0 ? 'last' : '' ).'">'.
                '<div class="new_top clearfix">'.
                    ( isset( $i['is_new'] ) && !empty( $i['is_new'] )
                    ?
                        '<div class="float">'.
                            '<img src="/images/new.png" alt="Новинки" width="47" height="14" />'.
                        '</div>'
                    :
                        '').
                    ( isset( $i['is_top'] ) && !empty( $i['is_top'] )
                    ?
                        '<div class="float">'.
                            '<img src="/images/top.png" alt="Топ продаж" width="63" height="14" />'.
                        '</div>'
                    :
                        '').
                '</div>'.
                '<div class="one_item_image">'.
                    '<a href="'.$i['alias'].'" title="'.$i['title'].'">'.
                        '<img src="'.$i['cover'].'" alt="'.$i['title'].'" width="126" height="200" />'.
                    '</a>'.
                '</div>'.
                '<div class="one_item_title">'.
                    '<a href="'.$i['alias'].'" title="'.$i['title'].'">'.
                        '<h3>'.$i['title'].'</h3>'.
                    '</a>'.
                '</div>'.
                '<div class="one_item_description">'.
                    '<p>'.$this->common->shortenString($i['description'], 200).'</p>'.
                '</div>'.
                '<div class="one_item_content_description">'.
                    '<p>'.strip_tags($this->common->shortenString($i['content_description'], 700)).'</p>'.
                '</div>'.
                '<div class="align_bottom clearfix">'.
                    '<div class="one_item_price">ціна від <span>'.$i['price'].'</span> грн</div>'.
                    '<div class="one_item_buttons">'.
                        '<a href="'.$i['alias'].'" title="" class="btn green">детальніше</a>'.
                    '</div>'.
                    '<div class="one_item_compare">'.
                        '<input type="checkbox" id="compare_item_'.$i['id'].'" value="'.$i['type_id'].'-'.$i['subtype_id'].'-'.$i['id'].'" '.( !empty($i['checked']) ? 'checked="checked"' : '' ).' />'.
                        '<label for="compare_item_'.$i['id'].'"><span></span>до порівняння</label>'.
                    '</div>'.
                '</div>'.
            '</div>';
    }

    $data_items .= '</div>';

    echo($data_items);
}

?>


</div>
</div>
</div>

<?php

if( $total > \config::get( 'limits/items') )
{
    echo('<div class="inner"><div class="paginate">');
    $this->common->paginate(
        [
            'page'              => $page,
            'items_per_page'    => \config::get( 'limits/items', 5),
            'total_items'       => $total,
            'url_for'           => isset( $page_url_for_filter ) ? $page_url_for_filter : [ 'for' => 'subtype_paged', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'], 'page' => $page ],
        ]
    );
    echo('</div></div>');
}

?>





<div class="content_accost">
    <div class="shadow_to_down"></div>
    <div class="inner">
        <div class="content_accost_title">SEO текст</div>
        <div class="content_accost_content">
            <p>
                На відміну від поширеної думки Lorem Ipsum не є випадковим набором літер. Він походить з уривку класичної латинської літератури 45 року до н.е., тобто має більш як 2000-річну історію. Річард Макклінток, професор латини з коледжу Хемпдін-Сидні, що у Вірджінії, вивчав одне з найменш зрозумілих латинських слів - consectetur - з уривку Lorem Ipsum, і у пошуку цього слова в класичній літературі знайшов безсумнівне джерело. Lorem Ipsum походить з розділів 1.10.32 та 1.10.33 цицеронівського "de Finibus Bonorum et Malorum" ("Про межі добра і зла"), написаного у 45 році до н.е. Цей трактат з теорії етики був дуже популярним в епоху Відродження. Перший рядок Lorem Ipsum, "Lorem ipsum dolor sit amet..." походить з одного з рядків розділу 1.10.32.
            </p>
            <p>
                На відміну від поширеної думки Lorem Ipsum не є випадковим набором літер.
            </p>
        </div>
    </div>
</div><!-- content_accost -->

<div class="content_blog">
    <div class="inner">

        <div class="links clearfix">

            <div class="float fb">
                <div id="fb-root"></div>

                <div class="fb-like" data-href="#" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
            </div>
            <div class="float ok">
                <div id="ok_shareWidget"></div>
            </div>
            <div class="float vk">
                <script type="text/javascript"><!--
                    document.write(VK.Share.button(false,{type: "round", text: "Нравится"}));
                    -->
                </script>
            </div>

            <div class="float share">
                <p class="share_title float">Поделиться:</p>

                <div class="pluso float" data-background="#ebebeb" data-options="small,square,line,horizontal,nocounter,theme=04" data-services="facebook,google,livejournal,moimir,odnoklassniki,vkontakte,twitter"></div>
            </div>
        </div>
    </div>

</div><!-- content_blog -->
</div><!-- catalog -->
</div>
