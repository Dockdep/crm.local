
<div id="content" class="clearfix">
<div class="news">
<div class="breadcrumbs">
    <div class="inner">
        <div class="order_menu_shadow"></div>
        <ul class="clearfix">
            <li class="float"><a href="/" title="Головна">Головна</a></li>
            <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><a href="/news-actions" title="Новини/Акції">Новини/Акції</a></li>
            <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <?= '<li class="float"><a href="'.$one_news['link'].'" title="'.$one_news['title'].'" class="breadcrumbs_last">'.$one_news['title'].'</a></li>' ?>
        </ul>
    </div>
</div>

<div class="news_wrapper">
    <div class="inner clearfix">


        <?php

        $data_one_news = '';

        if( !empty( $one_news['cover'] ) && !empty( $one_news['photos'] ) )
        {
            $data_one_news .=
                '<div class="news_img_holder">'.
                    '<a href="'.$this->storage->getPhotoUrl( $one_news['cover'], 'news', '800x' ).'" title="'.$one_news['title'].' data-options="thumbnail: \''.$this->storage->getPhotoUrl( $one_news['cover'], 'news', '180x120' ).'\'"  class="thumbnail news_cover">'.
                        '<img src="'.$one_news['image'].'" alt="'.$one_news['title'].'" width="400" height="265">'.
                    '</a>';
            foreach( $one_news['photos'] as $k => $n )
            {
                $data_one_news .=
                    '<a href="'.$this->storage->getPhotoUrl( $n, 'news', '800x' ).'" title="" data-options="thumbnail: \''.$this->storage->getPhotoUrl( $n, 'news', '180x120' ).'\'" class="thumbnail news_photogallery float'.( ($k+1)%2==0 ? ' last' : '' ).'">'.
                        '<img src="'.$this->storage->getPhotoUrl( $n, 'news', '180x120' ).'" alt="" width="180" height="120" />'.
                    '</a>';
            }

            $data_one_news .=  '</div>';
        }
        elseif( empty( $one_news['cover'] ) && !empty( $one_news['photos'] ) )
        {
            $data_one_news .= '<div class="news_img_holder">';

            foreach( $one_news['photos'] as $k => $n )
            {
                $data_one_news .=
                    '<a href="'.$this->storage->getPhotoUrl( $n, 'news', '800x' ).'" title="" data-options="thumbnail: \''.$this->storage->getPhotoUrl( $n, 'news', '180x120' ).'\'" class="thumbnail news_photogallery float'.( ($k+1)%2==0 ? ' last' : '' ).'">'.
                        '<img src="'.$this->storage->getPhotoUrl( $n, 'news', '180x120' ).'" alt="" width="180" height="120" />'.
                    '</a>';
            }

            $data_one_news .=  '</div>';
        }
        elseif( !empty( $one_news['cover'] ) && empty( $one_news['photos'] ) )
        {
            $data_one_news .=
                '<div class="news_img_holder">'.
                    '<a href="'.$this->storage->getPhotoUrl( $one_news['cover'], 'news', '800x' ).'" title="'.$one_news['title'].' data-options="thumbnail: \''.$this->storage->getPhotoUrl( $one_news['cover'], 'news', '180x120' ).'\'"  class="thumbnail">'.
                        '<img src="'.$one_news['image'].'" alt="'.$one_news['title'].'" width="400" height="265">'.
                    '</a>'.
                '</div>';
        }

        echo( $data_one_news );

        ?>




        <div class="news_content">
            <h2><?= $one_news['title'] ?></h2>
            <p><?= $one_news['content'] ?></p>

        </div>

    </div>

    <?php

    if( !empty( $news2groups ) )
    {
    ?>
        <div class="content_items clearfix">
            <div class="inner">
                <div class="recomended_groups clearfix" data-class="recomended_groups" data-news_id="<?=  $one_news['id'] ?>">

                    <div class="title clearfix">
                        <div class="float"><span class="items_title">Рекомендуємо</span></div>
                        <div class="float">
                            <a class="float content_arrow_left" href="#" title=""></a>
                            <span class="float content_items_page page_number">1</span>
                            <span class="float content_items_page">/</span>
                            <span class="float content_items_page max_page"><?= $pages_news2groups ?></span>
                            <a class="float content_arrow_right" href="#" title=""></a>
                        </div>
                    </div>
                    <div class="items clearfix">
                    <?php

                    $data_recommended_items = '';

                    foreach( $news2groups as $k => $i )
                    {
                        $data_recommended_items .=
                            '<div class="one_item float '.( ($k+1)%5 == 0 ? 'last' : '' ).'">'.
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
                                '<div class="align_bottom">'.
                                    '<div class="one_item_price">ціна від <span>'.$i['price'].'</span> грн</div>'.
                                    '<div class="one_item_buttons">'.
                                        '<a href="'.$i['alias'].'" title="" class="btn green">придбати</a>'.

                                    '</div>'.
                                    '<div class="one_item_compare">'.
                                        '<input type="checkbox" id="compare_item_'.$i['id'].'" value="'.$i['type_id'].'-'.$i['subtype_id'].'-'.$i['id'].'" '.( !empty($i['checked']) ? 'checked="checked"' : '' ).' />'.
                                        '<label for="compare_item_'.$i['id'].'"><span></span>до порівняння</label>'.
                                    '</div>'.
                                '</div>'.
                            '</div>';
                    }

                    echo($data_recommended_items);

                    ?>
                    </div>

                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>



<?= $this->partial('partial/share'); ?>

</div>
</div>