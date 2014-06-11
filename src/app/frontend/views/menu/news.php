
<div id="content" class="clearfix">
<div class="news">
<div class="breadcrumbs">
    <div class="inner">
        <div class="order_menu_shadow"></div>
        <ul class="clearfix">
            <li class="float"><a href="/" title="Головна">Головна</a></li>
            <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <li class="float"><a href="/news-actions" title="Новини/Акції" class="breadcrumbs_last">Новини/Акції</a></li>
        </ul>
    </div>
</div>

<div class="news_wrapper clearfix">
    <div class="inner clearfix">

        <?php

        $data_news = '';

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

        echo( $data_news );

        ?>

    </div>
    <?= $this->partial('partial/share'); ?>
</div>

<?php

if( $total > \config::get( 'limits/news') )
{
    echo('<div class="inner"><div class="paginate">');
    $this->common->paginate(
        [
            'page'              => $page,
            'items_per_page'    => \config::get( 'limits/news', 5),
            'total_items'       => $total,
            'url_for'           => [ 'for' => 'news_paginate', 'page' => $page ],
        ]
    );
    echo('</div></div>');
}

?>





</div>
</div>