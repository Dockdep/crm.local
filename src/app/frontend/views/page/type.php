<div id="content" class="clearfix">
    <div class="catalog">
        <div class="catalog_slider">
            <div class="inner">
                <div class="catalog_description logo<?= $catalog['type_id'] ?>">
                    <div class="catalog_description_image float">
                        <?= '<a href="/'.$catalog['alias'].'" title="'.$catalog['title'].'"><img src="/images/types_logo/'.$catalog['type_id'].'.jpg" alt="'.$catalog['title'].'" width="99" height="99" /></a>' ?>
                    </div>
                    <div class="catalog_description_content float">
                        <h2 class="catalog_description_title">
                            <?= '<a href="/'.$catalog['alias'].'" title="'.$catalog['title'].'">'.$catalog['title'].'</a>' ?>
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
                    <?php

                    if( !empty( $type_child ) )
                    {
                        $data_breadcrumbs =
                            '<li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>'.
                            '<li class="float"><a href="'.$this->url->get([ 'for' => 'type', 'type' => $catalog['type_alias'] ]).'" title="'.$catalog['type_title'].'">'.$catalog['type_title'].'</a></li>'.
                            '<li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>'.
                            '<li class="float"><a href="'.$this->url->get([ 'for' => 'type_with_child', 'type' => $catalog['type_alias'], 'type_child' => '--'.$catalog['type_children_']['alias'] ]).'" title="'.$catalog['type_children_']['title'].'" class="breadcrumbs_last">'.$catalog['type_children_']['title'].'</a></li>';
                    }
                    else
                    {
                        $data_breadcrumbs =
                            '<li class="float"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>'.
                            '<li class="float"><a href="'.$this->url->get([ 'for' => 'type', 'type' => $catalog['type_alias'] ]).'" title="'.$catalog['type_title'].'" class="breadcrumbs_last">'.$catalog['type_title'].'</a></li>';
                    }

                    echo( $data_breadcrumbs );

                    ?>
                </ul>
            </div>
        </div>
        <div class="sidebar_content_wrapper">
            <div class="inner clearfix">
                <div id="sidebar" class="float">
                    <ul>
                        <?php

                        $data_subtypes_list = '';

                        if( !empty( $catalog['type_children']  ) )
                        {
                            foreach( $catalog['type_children'] as $s )
                            {
                                $data_subtypes_list .= '<li><a href="/'.$catalog['alias'].'--'.$s['alias'].'" title="'.$s['title'].'">'.$s['title'].'</a></li>';
                            }
                        }
                        else
                        {
                            foreach( $catalog['subtypes'] as $s )
                            {
                                $data_subtypes_list .= '<li><a href="/'.$catalog['alias'].'/'.$s['alias'].'" title="'.$s['title'].'">'.$s['title'].'</a></li>';
                            }
                        }

                        echo($data_subtypes_list);

                        ?>
                    </ul>
                </div>
                <div id="content_wrapper" class="float">
                    <ul>
                        <?php

                        $data_subtypes = '';
                        $i = 0;

                        if( !empty( $catalog['type_children'] ) )
                        {

                            foreach( $catalog['type_children'] as $k => $s )
                            {
                                $data_subtypes .=
                                    '<li class="float '.( ($k)%4==0 ? 'last' : '' ).'">'.
                                    '<a href="/'.$catalog['alias'].'--'.$s['alias'].'" title="'.$s['title'].'">'.
                                    ( (!empty( $s['cover']) )
                                        ?
                                        '<img src="'.$this->storage->getPhotoUrl( $s['cover'], 'subtype', '165x120' ).'" alt="" width="165" height="120" />'
                                        :
                                        '<img src="/images/catalog1.jpg" alt="" width="165" height="120" />' ).
                                    '</a>'.
                                    '<a href="/'.$catalog['alias'].'--'.$s['alias'].'" title="'.$s['title'].'">'.$s['title'].'</a>'.
                                    '</li>';
                            }
                        }
                        else
                        {
                            foreach( $catalog['subtypes'] as $k => $s )
                            {
                                $i++;
                                $data_subtypes .=
                                    '<li class="float '.( ($i)%4==0 ? 'last' : '' ).'">'.
                                        '<a href="/'.$catalog['alias'].'/'.$s['alias'].'" title="'.$s['title'].'">'.
                                            ( (!empty( $s['cover']) )
                                            ?
                                                '<img src="'.$this->storage->getPhotoUrl( $s['cover'], 'subtype', '165x120' ).'" alt="" width="165" height="120" />'
                                            :
                                                '<img src="/images/catalog1.jpg" alt="" width="165" height="120" />' ).
                                        '</a>'.
                                        '<a href="/'.$catalog['alias'].'/'.$s['alias'].'" title="'.$s['title'].'">'.$s['title'].'</a>'.
                                    '</li>';
                            }
                        }

                        echo($data_subtypes);

                        ?>

                    </ul>
                </div>
            </div>
        </div>

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