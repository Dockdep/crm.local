<div id="content" class="clearfix">
<div class="static_page">
<div class="breadcrumbs">
    <div class="inner">
        <div class="item_menu_shadow"></div>
        <ul class="clearfix">
            <li class="float"><a href="/" title="Головна">Головна</a></li>
            <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
            <?= '<li class="float"><a href="'.$page['alias'].'" title="'.$page['content_title'].'">'.$page['content_title'].'</a></li>' ?>
        </ul>
    </div>
</div>
<div class="static_page_wrapper">
    <div class="inner clearfix">
        <?= $page['content_text'] ?>
    </div>

    <?= $this->partial('partial/share'); ?>
</div>



</div>
</div>
