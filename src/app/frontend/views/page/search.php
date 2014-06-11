<div id="content" class="clearfix">
<div class="search">
<div class="breadcrumbs">
    <div class="inner">
        <div class="order_menu_shadow"></div>
        <ul class="clearfix">
            <li class="float"><a href="/" title="Головна">Головна</a></li>
            <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><a href="#" title="Результати пошуку" class="breadcrumbs_last">Результати пошуку</a></li>
        </ul>
    </div>
</div>
<div class="sidebar_content_wrapper">
    <div class="inner clearfix">
        <div id="sidebar" class="float">

            <?php

            if( !empty( $type_subtype ) )
            {
                $data_types =
                    '<div class="subcategory_sidebar_title">
                        <p>Знайдено в категоріях:</p>
                    </div>
                    <ul>';

                foreach( $type_subtype as $s )
                {
                    $data_types .=
                        '<li>'.
                            '<a href="'.$this->url->get([ 'for' => 'type', 'type' => $s['type_alias'] ]).'" title="'.$s['type_title'].'">'.$s['type_title'].'</a>';
                    foreach( $s['subtype'] as $val )
                    {
                        $data_types .=
                            '<ul>'.
                                '<li><a href="'.$this->url->get([ 'for' => 'subtype', 'type' => $s['type_alias'], 'subtype' => $val['subtype_alias'] ]).'" title="'.$val['subtype_title'].'">'.$val['subtype_title'].'</a></li>'.
                            '</ul>';
                    }

                    $data_types .= '</li>';
                }
            }
            else
            {
                $data_types =
                    '<div class="subcategory_sidebar_title">
                        <p>За данним запитом нічого не знайдено</p>
                    </div>';
            }

            echo($data_types);

            ?>
            </ul>

        </div>
        <div id="content_wrapper" class="float">


            <?php

            if( !empty( $groups ) )
            {
                $data_items = '<div class="items clearfix">';

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
                                '<p>'.$i['description'].'</p>'.
                            '</div>'.
                            '<div class="one_item_content_description">'.
                                '<p>'.$i['content_description'].'</p>'.
                            '</div>'.
                            '<div class="align_bottom clearfix">'.
                                '<div class="one_item_price">ціна від <span>'.$i['price2'].'</span> грн</div>'.
                                '<div class="one_item_buttons">'.
                                    '<a href="'.$i['alias'].'" title="" class="btn green">придбати</a>'.
                                '</div>'.
                                '<div class="one_item_compare">'.
                                    '<input type="checkbox" id="compare_item_'.$i['group_id'].'" />'.
                                    '<label for="compare_item_'.$i['group_id'].'"><span></span>до порівняння</label>'.
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
            'url_for'           => [ 'for' => 'search_items_paged', 'search' => $search, 'page' => $page ],
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
 