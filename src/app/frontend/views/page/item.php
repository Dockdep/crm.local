<div id="content" class="clearfix">
<div class="item">
<div class="breadcrumbs">
    <div class="inner">
        <div class="item_menu_shadow"></div>
        <ul class="clearfix">
            <li class="float"><a href="/" title="Головна">Головна</a></li>
            <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><a href="/#catalog" title="Каталог">Каталог</a></li>
            <li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><?= '<a href="'.$this->url->get([ 'for' => 'type', 'type' => $catalog['type_alias'] ]).'" title="'.$catalog['type_title'].'">'.$catalog['type_title'].'</a>' ?></li>
            <li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><?= '<a href="'.$this->url->get([ 'for' => 'subtype', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'] ]).'" title="'.$catalog['subtype_title'].'" class="breadcrumbs">'.$catalog['subtype_title'].'</a>' ?></li>
            <li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><?= '<a href="'.$this->url->get([ 'for' => 'item', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'], 'group_alias' => $group_alias, 'item_id' => $item_id ]).'" title="'.$item['title'].'" class="breadcrumbs_last">'.$item['title'].'</a>' ?></li>
        </ul>
    </div>
</div>
<div class="item_wrapper">
    <div class="inner clearfix">
        <div class="float item_images">
            <ul class="thumbnails">
                <?php

                $data_images = '';

                if( !empty( $item['images'] ) )
                {
                    foreach( $item['images'] as $k => $i )
                    {
                        if( $k == 0 )
                        {
                            $data_images .=
                                '<li class="float width_400">'.
                                    '<a href="'.$this->storage->getPhotoUrl( $i, 'group', '800x' ).'" title="'.$item['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $i, 'group', '128x128' ).'\'"  class="thumbnail">'.
                                        '<img src="'.$this->storage->getPhotoUrl( $i, 'group', '400x400' ).'" alt="'.$item['title'].'" class="image_400">'.
                                    '</a>'.
                                '</li>';
                        }
                        else
                        {
                            $data_images .=
                                '<li class="float width_128 '.($k%3==0 ? 'last' : '').'">'.
                                    '<a href="'.$this->storage->getPhotoUrl( $i, 'group', '800x' ).'" title="'.$item['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $i, 'group', '128x128' ).'\'"  class="thumbnail">'.
                                        '<img src="'.$this->storage->getPhotoUrl( $i, 'group', '128x128' ).'" alt="'.$item['title'].'" class="image_128">'.
                                    '</a>'.
                                '</li>';
                        }
                    }

                    $data_images .=
                        '<li class="float width_128 '.(count($item['images'])%3==0 ? 'last' : '').'">'.
                            '<a href="'.$this->storage->getPhotoUrl( $item['cover'], 'avatar', '800x' ).'" title="'.$item['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $item['cover'], 'avatar', '128x' ).'\'"  class="thumbnail">'.
                                '<img src="'.$this->storage->getPhotoUrl( $item['cover'], 'avatar', '128x' ).'" alt="'.$item['title'].'" class="image_128">'.
                            '</a>'.
                        '</li>';
                }
                elseif( !empty( $item['cover'] ) && empty( $item['images'] ) )
                {
                    $data_images .=
                        '<li class="float width_400">'.
                            '<a href="'.$this->storage->getPhotoUrl( $item['cover'], 'avatar', '400x' ).'" title="'.$item['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $item['cover'], 'avatar', '800x' ).'\'"  class="thumbnail">'.
                                '<img src="'.$this->storage->getPhotoUrl( $item['cover'], 'avatar', '400x' ).'" alt="'.$item['title'].'" class="image_400">'.
                            '</a>'.
                        '</li>';
                }
                else
                {
                    $data_images .=
                        '<li class="float width_400">
                            <img src="/images/item_main_photo.jpg" alt="" width="400" height="400">
                        </li>

                        <li class="float width_128"><img src="/images/item_photo.jpg" alt="" width="128" height="128"></li>
                        <li class="float width_128 last"><img src="/images/item_photo.jpg" alt="" width="128" height="128"></li>';
                }

                echo( $data_images );

                ?>

            </ul>
        </div>

        <div class="float item_content">
            <div class="item_title"><h2><?= $item['title'] ?></h2></div>
            <div class="item_decription"><?= $item['description'] ?></div>
            <div class="clearfix">
                <div class="float properties">Код:</div>
                <div class="float properties properties_article"><?= $item['product_id'] ?></div>
            </div>
            <div class="clearfix">
                <div class="float properties">Наявність:</div>
                <div class="float presence_status">
                    <?=  $item['status'] == 1 ? '<div class="properties properties_presence ">В наявності</div>' : '<div class="properties properties_absent">Відсутній</div>' ?>
                </div>

            </div>
            <div class="clearfix">
                <div class="float properties">Кількість:</div>
                <div class="float count minus">
                </div>
                <div class="float count count_input">
                    <input name="count_items" class="count_items" type="text" value="1" />
                </div>
                <div class="float count plus">

                </div>
            </div>
            <div class="clearfix packing">
                <div class="float properties">Фасовка:</div>
                <div class="float packing_images clearfix">
                    <?php

                    $data_sizes = '';

                    if( !empty( $sizes_colors_ ) )
                    {
                        $i = 0;
                        foreach( $sizes_colors_ as $k => $s )
                        {

                            $data_sizes .=
                                '<a href="'.$s['0']['link'].'" class="group_sizes'.($s['0']['size'] == $item['size'] ? ' active' : '').'" style="padding-top:'.($i*3).'px; width:'.(31+($i*3)).'px" data-item_id="'.$s['0']['id'].'" data-type_id="'.$catalog['type_id'].'" data-subtype_id="'.$catalog['subtype_id'].'" data-group_alias="'.$group_alias.'">'.
                                    '<span class="group_sizes_header"></span>'.
                                    '<span class="group_sizes_content">'.$s['0']['size'].'</span>'.
                                '</a>';
                            $i++;
                        }
                    }
                    else
                    {
                        foreach( $sizes as $k => $s )
                        {
                            $data_sizes .=
                                '<a href="'.$s['link'].'" class="group_sizes'.($s['size'] == $item['size'] ? ' active' : '').'" style="padding-top:'.($k*3).'px; width:'.(31+($k*3)).'px" data-item_id="'.$s['id'].'" data-type_id="'.$catalog['type_id'].'" data-subtype_id="'.$catalog['subtype_id'].'" data-group_alias="'.$group_alias.'">'.
                                    '<span class="group_sizes_header"></span>'.
                                    '<span class="group_sizes_content">'.$s['size'].'</span>'.
                                '</a>';
                        }
                    }

                    echo( $data_sizes );

                    ?>

                </div>
            </div>

            <?php

            if( !empty( $sizes_colors_ ) )
            {
                $data_colors =
                    '<div class="clearfix colors">'.
                        '<div class="float properties">Оберіть колір: </div>'.
                        '<div class="float properties" style="color:'.$item['absolute_color'].'">'.$item['color_title'].'</div>'.
                    '</div>'.

                    '<div class="sliderkit carousel-demo1 colors_images clearfix">'.
                        '<div class="sliderkit-nav">';

                            $data_colors .= '<div class="sliderkit-nav-clip"><ul>';

                            foreach( $sizes_colors__ as $k => $s )
                            {
                                $data_colors .= '<li><a href="'.$s['0']['link'].'" title="[link title]" '.( $s['0']['color_id'] == $item['color_id'] ? 'class="active" style="border-color:'.$item['absolute_color'].'"' : '' ).' ><img src="'.$s['0']['image'].'" alt="[Alternative text]" width="60" height="60" /></a></li>';
                            }

                            $data_colors .= '</ul></div>';



                            $data_colors .=
                            '<div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-prev"><a href="#" title="Previous line"><span>Previous</span></a></div>'.
                            '<div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-next"><a href="#" title="Next line"><span>Next</span></a></div>'.
                        '</div>'.
                    '</div>';

                echo $data_colors;
            }

            ?>



            <div class="change_with_size">
                <div class="clearfix buy_compare">
                    <div class="one_item_price float">ціна <span><?= $item['price2'] ?></span> грн</div>
                    <div class="one_item_buttons float">
                        <a href="<?= $this->url->get([ 'for' => 'item', 'type' => $catalog['type_alias'], 'subtype' => $catalog['subtype_alias'], 'group_alias' => $group_alias, 'item_id' => $item_id ]) ?>" title="" id="show_confirm" class="btn green">придбати</a>
                    </div>
                    <div class="one_item_compare float">
                        <?= '<input type="checkbox" id="compare_item_'.$item['id'].'" class="compare_item" value="'.$item['type'].'-'.$item['subtype'].'-'.$item['id'].'" '.(!empty($item['checked']) ? 'checked="checked"' : '').' />' ?>
                        <label for="compare_item_<?= $item['id'] ?>"><span></span>до порівняння</label>
                        <input type="hidden" class="item_id_for_basket" value="<?= $item['id'] ?>">
                    </div>
                </div>
                <div class="clearfix features">
                    <?php

                    $data_features = '';

                    foreach( $filters as $f )
                    {
                        $data_features .= '<a href="#" class="float">'.$f['value_value'].'</a>';
                    }

                    echo( $data_features );

                    ?>
                </div>
            </div>
            <div class="clearfix item_menu">
                <div class="item_menu_header_menu clearfix">
                    <div class="tabs clearfix">
                        <ul class="change_item_description">
                            <li class="float active_tab first_tab" data-change_item_description="tabs_description"><a href="#" title="">Опис</a></li>
                            <li class="float not_active" data-change_item_description="tabs_properties"><a href="#" title="">Характеристика</a></li>
                            <li class="float not_active" data-change_item_description="tabs_video"><a href="#" title="">Відео</a></li>
                            <li class="float last_tab not_active" data-change_item_description="tabs_comments"><a href="#" title="">Відгуки</a></li>
                        </ul>
                    </div>
                </div>
                <div class="item_menu_content">
                    <div class="tabs_description item_menu_content_wrapper"><?= $item['content_description'] ?></div>
                    <div class="display_none tabs_properties item_menu_content_wrapper">
                        <?php

                        $data_properties =
                            '<div class="clearfix properties_producer">'.
                                '<p class="float key_value">Виробник:</p>'.
                                '<a class="float" href="#" title="'.$item['brand'].'">'.$item['brand'].'</a>'.
                            '</div>';

                        foreach( $properties as $p )
                        {
                            $data_properties .=
                                '<div class="clearfix">'.
                                    '<p class="float key_value">'.$p['key_value'].':</p>'.
                                    '<a class="float" href="#">'.$p['value_value'].'</a>'.
                                '</div>';
                        }

                        echo( $data_properties );

                        ?>
                    </div>
                    <div class="display_none tabs_video item_menu_content_wrapper"><?= $item['content_video'] ?></div>
                    <div class="display_none tabs_comments item_menu_content_wrapper"><?= $item['content_video'] ?></div>

                </div>
            </div>
        </div>
    </div>
</div>



<div class="other_items">
    <div class="item_menu_header_menu clearfix">
        <div class="inner">
            <div class="tabs clearfix">
                <ul class="change_similar_items">
                    <li class="float active_tab first_tab">
                        <?= '<a href="#" title="Популярні товари" data-change_similar_items="popular" data-type_id="'.$catalog['type_id'].'" data-subtype_id="'.$catalog['subtype_id'].'" data-group_id="'.$item['group_id'].'">Популярні товари</a>' ?>
                     </li>
                    <li class="float not_active">
                        <?= '<a href="#" title="Схожі товари" data-change_similar_items="same" data-type_id="'.$catalog['type_id'].'" data-subtype_id="'.$catalog['subtype_id'].'" data-group_id="'.$item['group_id'].'">Схожі товари</a>' ?>
                    </li>
                    <li class="float not_active">
                        <?= '<a href="#" title="Супутні товари" data-change_similar_items="buy_with" data-type_id="'.$catalog['type_id'].'" data-subtype_id="'.$catalog['subtype_id'].'" data-group_id="'.$item['group_id'].'">Супутні товари</a>' ?>
                    </li>
                    <li class="float last_tab not_active">
                        <?= '<a href="#" title="Переглянуті" data-change_similar_items="viewed" data-type_id="'.$catalog['type_id'].'" data-subtype_id="'.$catalog['subtype_id'].'" data-group_id="'.$item['group_id'].'">Переглянуті</a>' ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="items inner clearfix">
        <?php

        if( !empty( $popular_groups ) )
        {
            $data_popular_groups = '';

            foreach( $popular_groups as $k => $p )
            {
                $data_popular_groups .=
                    '<div class="one_item float'.($k == 4 ? ' last' : '').'">'.
                        '<div class="new_top clearfix">'.
                            ( isset( $p['is_new'] ) && !empty( $p['is_new'] )
                            ?
                                '<div class="float">'.
                                    '<img src="/images/new.png" alt="Новинки" width="47" height="14" />'.
                                '</div>'
                            :
                                '').
                            ( isset( $p['is_top'] ) && !empty( $p['is_top'] )
                            ?
                                '<div class="float">'.
                                    '<img src="/images/top.png" alt="Топ продаж" width="63" height="14" />'.
                                '</div>'
                            :
                                '').
                        '</div>'.
                        '<div class="one_item_image">'.
                            '<a href="'.$p['alias'].'" title="'.$p['title'].'">'.
                                '<img src="'.$p['cover'].'" alt="'.$p['title'].'" width="126" height="200" />'.
                            '</a>'.
                        '</div>'.
                        '<div class="one_item_title">'.
                            '<a href="'.$p['alias'].'" title="'.$p['title'].'">'.
                                '<h3>'.$p['title'].'</h3>'.
                            '</a>'.
                        '</div>'.
                        '<div class="one_item_description">'.
                            '<p>'.$p['description'].'</p>'.
                        '</div>'.
                        '<div class="align_bottom">'.
                            '<div class="one_item_price">ціна від <span>'.$p['price'].'</span> грн</div>'.
                            '<div class="one_item_buttons">'.
                                '<a href="'.$p['alias'].'" title="'.$p['title'].'" class="btn green">детальніше</a>'.
                            '</div>'.
                            '<div class="one_item_compare">'.
                                '<input type="checkbox" id="compare_item_'.$p['id'].'" value="'.$p['type_id'].'-'.$p['subtype_id'].'-'.$p['id'].'" '.( !empty($p['checked']) ? 'checked="checked"' : '' ).' />'.
                                '<label for="compare_item_'.$p['id'].'"><span></span>до порівняння</label>'.
                            '</div>'.
                        '</div>'.
                    '</div>';
            }

            echo($data_popular_groups);
        }

        ?>

    </div>
</div>

<?php

if( !empty( $news ) )
{
    $data_news =
        '<div class="news_wrapper">'.
            '<div class="inner clearfix">';

    foreach( $news as $k => $n )
    {
        $data_news .=
            '<div class="one_news float'.( ($k+1)%2==0 ? ' last' : '' ).'">'.
                '<div class="one_news_img float">'.
                    ( !empty( $n['cover'] )
                    ?
                        '<a href="'.$n['link'].'" title="'.$n['title'].'">'.
                            '<img src="'.$n['image'].'" alt="" width="180" height="120" />'.
                        '</a>'
                    :
                        '').
                '</div>'.
                '<div class="one_news_content float'.( empty( $n['cover'] ) ? ' full_width' : '').'">'.
                    '<a href="'.$n['link'].'" title="'.$n['title'].'">'.
                        '<h2>'.$n['title'].'</h2>'.
                    '</a>'.
                    '<p>'.$this->common->shortenString( $n['content'], 230 ).'</p>'.
                    '<a href="'.$n['link'].'" title="'.$n['title'].'" class="news_more">Докладніше</a>'.
                '</div>'.
            '</div>';
    }

    $data_news .= '</div></div>';

    echo( $data_news );
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
 