<div id="content" class="clearfix">
    <div class="cabinet">
        <div class="breadcrumbs">
            <div class="inner">
                <div class="order_menu_shadow"></div>
                <ul class="clearfix">
                    <li class="float"><a href="/" title="Головна">Головна</a></li>
                    <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
                    <li class="float"><a href="/basket" title="Особистий кабінет">Особистий кабінет</a></li>
                    <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
                    <li class="float"><a href="/cabinet" title="Профіль" class="breadcrumbs_last">Профіль</a></li>
                </ul>
            </div>
        </div>

        <div class="inner"><?= $this->flash->output(); ?></div>

        <div class="sidebar_content_wrapper">
            <div class="inner clearfix">
                <div id="sidebar" class="float">
                    <div class="subcategory_sidebar_title">
                        <h3>Особистий кабінет</h3>
                        <a href="<?= $this->url->get([ 'for' => 'cabinet' ]) ?>" title="Профіль" class="">Профіль</a>
                        <a href="#" title="Мої замовлення" class="my_orders active">Мої замовлення</a>
                        <ul class="toggle display_block">
                            <?php

                            $data_orders = '';

                            foreach( $orders as $o )
                            {
                                $data_orders .= '<li><a class="'.($order_id == $o['id'] ? 'active' : '').'" href="'.$this->url->get([ 'for' => 'list_orders', 'order_id' => $o['id'] ]).'" title="">№'.$o['id'].' ('.date( 'd.m.Y', strtotime($o['created_date']) ).')</a></li>';
                            }

                            echo( $data_orders );

                            ?>

                        </ul>
                        <a href="<?= $this->url->get([ 'for' => 'customer_logout' ]) ?>" title="Вихід">Вихід</a>
                    </div>
                </div>
                <div id="content_wrapper" class="float">
                    <div class="subcategory_content_wrapper_title">
                        <h3>Мої замовлення</h3>
                        <p class="order_number">Замовлення №<?= $order_id.' від '.$orders_with_id['date'] ?></p>
                    </div>
                    <div class="subcategory_content_wrapper_container my_order_items clearfix">
                        <?php

                        $data_items = '';

                        foreach( $order as $o )
                        {
                            $data_items .=
                                '<div class="float my_order_item">'.
                                    '<div class="my_order_item_image">'.
                                        '<a href="'.$o['link'].'" title="'.$o['title'].'">'.
                                            '<img src="'.$o['image'].'" alt="'.$o['title'].'" height="100">'.
                                        '</a>'.
                                    '</div>'.
                                    '<div class="my_order_item_content">'.
                                        '<h2><a href="'.$o['link'].'" title="'.$o['title'].'">'.$o['title'].'</a></h2>'.
                                        '<p class="my_order_item_size">Фасовка: '.$o['size'].'</p>'.
                                        '<p class="my_order_item_price"><span class="price">'.$o['price2'].'</span><span> грн</span></p>'.
                                        '<p class="my_order_item_count">Кількість: <span class="price">'.$o['item_count'].'</span></p>'.
                                        '<p class="my_order_item_count_total">Вартість: <span class="price">'.$o['total_count'].'</span><span> грн</span></p>'.
                                    '</div>'.
                                '</div>';
                        }

                        echo($data_items);

                        ?>
                    </div>

                    <div class="my_order_items_description">
                        <p class="my_order_total">Усього: <span class="price"><?= $total_count ?></span><span> грн</span></p>
                        <p class="my_order_delivery">Доставка: <span><?= $orders_with_id['delivery'] ?></span></p>
                        <p class="my_order_status">Статус: <span><?= $orders_with_id['status'] ?></span></p>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>