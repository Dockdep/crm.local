<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace controllers;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class MenuController extends \Phalcon\Mvc\Controller
{
    ///////////////////////////////////////////////////////////////////////////

    public function orderAction()
    {
        $lang_id        = 1;
        $in_cart        = $this->session->get('in_cart', []);
        $customer_id    = $this->session->get('id');
        $customer_email = $this->session->get('customer_email');
        $customer       = !empty( $customer_id ) ? $this->models->getCustomers()->getCustomer( $customer_id ) : [];


        $this->session->set( 'return_url', 'basket' ); // для redirect после авторизации на соц сетях
        //p($in_cart,1);

        $items          = [];
        $total_price    = 0;

        $cities_ = $this->novaposhta->city();

        foreach( $cities_->city as $c )
        {
            //p($c);
            $cities[strval($c->id)] = strval($c->nameUkr);
        }

        //p($cities,1);

        if( !empty( $in_cart ) )
        {
            $item_ids   = $this->common->array_column( $in_cart, 'item_id' );
            $items      = $this->models->getItems()->getItemsByIds( $lang_id, $item_ids );

            foreach( $in_cart as $c )
            {
                $count_item[$c['item_id']] = $c['count_items'];
            }

            foreach( $items as &$i )
            {
                $i['cover']         = !empty( $i['group_cover'] ) ? $this->storage->getPhotoUrl( $i['group_cover'], 'avatar', '200x' ) : '/images/packet.jpg';
                $i['alias']         = $this->url->get([ 'for' => 'item', 'type' => $i['type_alias'], 'subtype' => $i['subtype_alias'], 'group_alias' => $i['group_alias'], 'item_id' => $i['id'] ]);
                $i['count']         = $count_item[$i['id']];
                $i['total_price']   = $count_item[$i['id']]*$i['price2'];
                $total_price        += $count_item[$i['id']]*$i['price2'];
                $items_[$i['id']]   = $i;
            }
        }

        if( $this->request->isPost() )
        {
            $order['email']             = $this->request->getPost('login_email', 'string', NULL );
            $order['passwd']            = $this->request->getPost('login_passwd', 'string', NULL );

            $order_items                = $this->request->getPost('count_items', NULL, [] );
            $order['total_sum']         = 0;

            foreach( $order_items as $key => $val )
            {
                $items_[$key]['count']          = $val;
                $items_[$key]['total_price']    = $val*$items_[$key]['price2'];
                $order['items'][]               = $items_[$key];
                $order['total_sum']             += $items_[$key]['total_price'];

                $item_id_in_cart                = $this->common->array_column( $in_cart, 'item_id' );

                if( in_array( $key, $item_id_in_cart ) )
                {
                    foreach( $in_cart as &$c )
                    {
                        if( $c['item_id'] == $key )
                        {
                            $c['count_items'] = $val;
                        }
                    }
                }
            }

            //p($order,1);

            $this->session->set( 'in_cart', $in_cart );

            //p($in_cart,1);

            if( !empty( $order['email'] ) && !empty( $order['passwd'] ) )
            {
                $order['passwd'] = $this->common->hashPasswd( $order['passwd'] );

                switch( $this->models->getCustomers()->customerLogin( $order ) )
                {
                    case 1:
                        // OK
                        // redirect

                        $this->session->set( 'customer_email',  NULL );
                        $this->session->remove('customer_email');
                        return $this->response->redirect([ 'for' => 'basket' ]);
                        break;

                    case -1:
                        $this->flash->error('Невірний логін або пароль');
                        $this->session->set( 'customer_email', $order['email'] );
                        return $this->response->redirect([ 'for' => 'basket' ]);
                        break;

                    case 2: // user with status 0
                    default:
                        $this->flash->success('Будь ласка, змінить пароль');
                        $this->session->set( 'customer_email', $order['email'] );
                        return $this->response->redirect([ 'for' => 'finish_registration' ]);
                        break;

                }
            }

            unset($order['email']);
            unset($order['passwd']);

            $order['name']              = $this->request->getPost('order_name', 'string', NULL );
            $order['phone']             = $this->request->getPost('order_phone', 'string', NULL );
            $order['delivery']          = $this->request->getPost('order_delivery', 'string', NULL );
            $order['pay']               = $this->request->getPost('order_pay', 'string', NULL );

            foreach( $order as $o )
            {
                if( empty($o) )
                {
                    $err = 1;
                }
            }

            //p($order,1);

            if( empty( $err ) )
            {
                $order['city']              =
                    ( $order['delivery'] == 3 || $order['delivery'] == 4 )
                    ?
                        $this->request->getPost('order_city_novaposhta', 'string', NULL )
                    :
                        $this->request->getPost('order_city', 'string', NULL );

                $order['city_ref'] = $this->request->getPost('order_city_ref', 'string', NULL );

                $order['store_address']     = $this->request->getPost('store_address', 'string', NULL );
                $order['store_ref']         = $this->request->getPost('order_store_address_ref', 'string', NULL );

                $address                    = $this->request->getPost('order_address', 'string', NULL );

                $order['address']           = !empty( $address ) ? $address : $order['store_address'];
                $order['email']             = $this->request->getPost('order_email', 'string', NULL );
                $order['email']             = $order['email'] ? $order['email'] : NULL;
                $order_get_info             = $this->request->getPost('order_get_info', 'string', NULL );
                $order['subscribed']        = empty( $order_get_info ) ? 0 : 1;
                $order['comments']          = $this->request->getPost('order_comments', 'string', NULL );
                $passwd_                    = $this->common->generatePasswd(10);
                $order['passwd']            = $this->common->hashPasswd( $passwd_ );
                $proposal_number            = $this->models->getOrders()->addOrder($order);
                $order['proposal_number']   = $proposal_number['proposal_number'];
                $order['confirmed']         = $proposal_number['confirmed'];
                $order['customer_new']      = $proposal_number['new'];
                $order['novaposhta_tnn']    = $proposal_number['novaposhta_tnn'];

                //p($this->getDi()->get('novaposhta')->ttn_ref( 10, $order['city_ref'], $order['store_ref'], NULL, $order['name'], $order['phone'], '10', $order['total_sum'] ),1);

                //p($proposal_number,1);


                if( !empty( $order['email'] ) )
                {
                    $this->sendmail->addCustomer( 1, $order );

                    if( empty( $order['confirmed'] ) && !empty( $order['customer_new'] ) ) // new customer
                    {
                        $this->sendmail->addCustomer( 3, $order );
                    }
                }

                if( !empty( $order['proposal_number'] ) )
                {
                    $this->sendmail->addCustomer( 2, $order );

                    setcookie("order", '1', time()+3600);
                    $this->session->set( 'in_cart', []);
                    return $this->response->redirect([ 'for' => 'homepage' ]);
                }
                else
                {
                    $this->flash->error( 'Произошла ошибка при отправке сообщения. Попробуйте позже, пожалуйста.' );
                    return $this->response->redirect([ 'for' => 'basket' ]);
                }
            }
            else
            {
                //p($err,1);
            }
        }

        $static_page_alias = '/basket';

        $meta_title         = 'Кошик | '.\config::get( 'global#title' );

        //p($lang_id,1);

        $this->view->setVars([
            'items'              => $items,
            'lang_id'            => $lang_id,
            'total_price'        => $total_price,
            'static_page_alias'  => $static_page_alias,
            'meta_title'         => $meta_title,
            'customer'           => $customer['0'],
            'customer_email'     => $customer_email,
            'cities'             => $cities,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function getCitiesAction( $lang_id = '1' )
    {

        header('Content-Type: application/json; charset=utf8');

        $term       = $this->request->getPost('term', 'string', '' );
        $length     = strlen($term);
        $cities_    = $this->novaposhta->city();

        foreach( $cities_->city as $c )
        {
            $cities[strval($c->id)] = strval($c->nameUkr);

            if( mb_strtolower( substr( strval($c->nameUkr), 0, $length ), 'utf-8' ) == mb_strtolower( $term, 'utf-8' ) )
            {
                $selected_cities[] =
                    [
                        'label' => strval($c->nameUkr),
                        'value' => strval($c->nameUkr),
                        'id'    => strval($c->id),
                        'ref'   => strval($c->ref)
                    ];
            }
        }

        die( json_encode( $selected_cities ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function getOfficesAction( $lang_id = '1' )
    {
        header('Content-Type: application/json; charset=utf8');

        $city       = $this->request->getPost('city', 'string', '' );
        $offices_   = $this->novaposhta->warenhouse( $city );

        foreach( $offices_->warenhouse as $c )
        {
            //$offices[strval($c->number)] = strval($c->address);

            $offices[] =
                [
                    'number'    => strval($c->number),
                    'address'   => strval($c->address),
                    'store_ref' => strval($c->ref)
                ];
        }

        die( json_encode( $offices ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function addToBasketAction()
    {
        $count = 0;

        if( $this->request->isAjax() && $this->request->isPost() )
        {
            $item_id        = $this->request->getPost( 'item_id', 'int', '' );
            $count_items    = $this->request->getPost( 'count_items', 'int', '' );

            $in_cart         = $this->session->get('in_cart', []);
            $item_id_in_cart = $this->common->array_column( $in_cart, 'item_id' );

            if( in_array( $item_id, $item_id_in_cart ) )
            {
                die( json_encode( 0 ) );
            }

            $in_cart[]       =
                [
                    'item_id'       => $item_id,
                    'count_items'   => $count_items
                ];
            $this->session->set( 'in_cart', $in_cart );

            $count = count($in_cart);
        }

        die( json_encode( $count ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function deleteFromBasketAction()
    {
        $count = 0;

        if( $this->request->isAjax() && $this->request->isPost() )
        {
            $item_id = $this->request->getPost( 'item_id', 'int', '' );
            $in_cart = $this->session->get('in_cart', []);

            foreach( $in_cart as $key => $value )
            {
                if( $value['item_id'] == $item_id )
                {
                    unset( $in_cart[$key] );
                }
            }

            $this->session->set( 'in_cart', $in_cart );

            $count = count($in_cart);
        }

        die( json_encode( $count ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function staticPageAction( $page_alias, $page_id, $lang_id = '1' )
    {
        $page = $this->models->getPages()->getPage( $page_id, $lang_id );

        $meta_title         = $page['0']['meta_title'].' | '.\config::get( 'global#title' );
        $meta_keywords      = $page['0']['meta_keywords'];
        $meta_description   = $page['0']['meta_description'];

        $this->view->setVars([
            'page' => $page['0'],
            'meta_title'        => $meta_title,
            'meta_keywords'     => $meta_keywords,
            'meta_description'  => $meta_description,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function newsAction( $page = 1, $lang_id = '1' )
    {
        $news = $this->models->getNews()->getNews( $lang_id, $page );

        foreach( $news as $k => $n )
        {
            $news[$k]['image']  = !empty( $n['cover'] ) ? $this->storage->getPhotoUrl( $n['cover'], 'news', '180x120' ) : '';
            $news[$k]['link']   = $this->url->get([ 'for' => 'one_news', 'news_id' => $n['id'], 'news_alias' => $n['alias'] ]);
        }

        $total = $this->models->getNews()->getTotalNews( $lang_id );

        $meta_title         = 'Новини/Акції | '.\config::get( 'global#title' );

        //p($news,1);

        $this->view->setVars([
            'news'          => $news,
            'page'          => $page,
            'total'         => $total['0']['count'],
            'meta_title'    => $meta_title
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function profTipsAction( $page = 1, $lang_id = '1' )
    {
        $tips = $this->models->getNews()->getTips( $lang_id, $page );

        foreach( $tips as $k => $n )
        {
            $tips[$k]['image']  = !empty( $n['cover'] ) ? $this->storage->getPhotoUrl( $n['cover'], 'news', '180x120' ) : '';
            $tips[$k]['link']   = $this->url->get([ 'for' => 'one_tips', 'tips_id' => $n['id'], 'tips_alias' => $n['alias'] ]);
        }

        $total = $this->models->getNews()->getTotalTips( $lang_id );

        $meta_title         = 'Поради професіоналів | '.\config::get( 'global#title' );

        //p($news,1);

        $this->view->setVars([
            'tips'          => $tips,
            'page'          => $page,
            'total'         => $total['0']['count'],
            'meta_title'    => $meta_title
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function oneNewsAction( $news_alias, $news_id, $lang_id = '1' )
    {
        $one_news                   = $this->models->getNews()->getOneNews( $lang_id, $news_id );
        $one_news['0']['link']      = $this->url->get([ 'for' => 'one_news', 'news_id' => $one_news['0']['id'], 'news_alias' => $one_news['0']['alias'] ]);
        $one_news['0']['image']     = $this->storage->getPhotoUrl( $one_news['0']['cover'], 'news', '400x265' );

        //p($one_news,1);

        $news2groups_ids_           = $this->etc->int2arr($one_news['0']['group_id']);
        $news2groups                = [];

        if( !empty( $news2groups_ids_ ) )
        {
            $news2groups_ids            = array_chunk( $news2groups_ids_, \config::get( 'limits/groups2news' ) );
            //p($news2groups_ids,1);
            $news2groups_               = $this->models->getItems()->getNews2Groups( $lang_id, $news2groups_ids['0'] );
            $news2groups                = $this->common->getGroups( $lang_id, $news2groups_ );

            $total_groups               = count($news2groups_ids);
            $pages_news2groups          =
                    $total_groups%\config::get( 'limits/groups2news' )==0
                    ?
                        $total_groups/\config::get( 'limits/groups2news' )
                    :
                        floor( $total_groups/\config::get( 'limits/groups2news' ) )+1;
        }

        if( !empty( $one_news['0']['photogallery'] ) )
        {
            $one_news['0']['photos'] = $this->etc->int2arr($one_news['0']['photogallery']);
        }

        $meta_title         = $one_news['0']['meta_title'].' | Новини/Акції'.' | '.\config::get( 'global#title' );
        $meta_keywords      = $one_news['0']['meta_keywords'];
        $meta_description   = $one_news['0']['meta_description'];

        //p($news2groups,1);

        $this->view->setVars([
            'one_news'          => $one_news['0'],
            'meta_title'        => $meta_title,
            'meta_keywords'     => $meta_keywords,
            'meta_description'  => $meta_description,
            'news2groups'       => $news2groups,
            'pages_news2groups' => $pages_news2groups,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function oneTipsAction( $tips_alias, $tips_id, $lang_id = '1' )
    {
        $one_news                   = $this->models->getNews()->getOneNews( $lang_id, $tips_id );
        $one_news['0']['link']      = $this->url->get([ 'for' => 'one_tips', 'tips_id' => $one_news['0']['id'], 'tips_alias' => $one_news['0']['alias'] ]);
        $one_news['0']['image']     = $this->storage->getPhotoUrl( $one_news['0']['cover'], 'news', '400x265' );

        //p($one_news,1);

        $news2groups_ids_           = $this->etc->int2arr($one_news['0']['group_id']);
        $news2groups                = [];

        if( !empty( $news2groups_ids_ ) )
        {
            $news2groups_ids            = array_chunk( $news2groups_ids_, \config::get( 'limits/groups2news' ) );
            //p($news2groups_ids,1);
            $news2groups_               = $this->models->getItems()->getNews2Groups( $lang_id, $news2groups_ids['0'] );
            $news2groups                = $this->common->getGroups( $lang_id, $news2groups_ );

            $total_groups               = count($news2groups_ids);
            $pages_news2groups          =
                $total_groups%\config::get( 'limits/groups2news' )==0
                    ?
                    $total_groups/\config::get( 'limits/groups2news' )
                    :
                    floor( $total_groups/\config::get( 'limits/groups2news' ) )+1;
        }

        if( !empty( $one_news['0']['photogallery'] ) )
        {
            $one_news['0']['photos'] = $this->etc->int2arr($one_news['0']['photogallery']);
        }

        $meta_title         = $one_news['0']['meta_title'].' | Поради професіоналів | '.\config::get( 'global#title' );
        $meta_keywords      = $one_news['0']['meta_keywords'];
        $meta_description   = $one_news['0']['meta_description'];

        //p($news2groups,1);

        $this->view->setVars([
            'one_news'          => $one_news['0'],
            'meta_title'        => $meta_title,
            'meta_keywords'     => $meta_keywords,
            'meta_description'  => $meta_description,
            'news2groups'       => $news2groups,
            'pages_news2groups' => $pages_news2groups,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function partnersAction( $lang_id = '1' )
    {
        $partners = $this->models->getPartners()->getPartners( $lang_id );

        foreach( $partners as $p )
        {
            $partners_[$p['shop_type']][] = $p;
        }

        $internet_shops = $partners_['1'];

        foreach( $partners_['2'] as $p )
        {
               $dillers[$p['district']][] = $p;
        }

        //p($dillers,1);

        $this->view->setVars([
            'internet_shops'    => $internet_shops,
            'dillers'           => $dillers,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function contactsAction( $lang_id = '1' )
    {

        $shops = $this->models->getPartners()->getContactsShops( $lang_id );

        //p($shops,1);

        $this->view->setVars([
            'shops' => $shops
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function callbackAction( $lang_id = '1' )
    {
        $callback['id'] = $this->session->get('id');
        $callback['id'] = !empty( $callback['id'] ) ? $callback['id'] : NULL;
        $customer       = [];

        if( !empty( $callback['id'] ) )
        {
            $customer   = $this->models->getCustomers()->getCustomer($callback['id']);
        }

        if( $this->request->isPost() )
        {
            $callback['name']               = $this->request->getPost('name', 'string', NULL );
            $email                          = $this->request->getPost('email', 'string', NULL );
            $callback['comments']           = $this->request->getPost('comments', 'string', NULL );
            $callback['email']              = filter_var( $email, FILTER_VALIDATE_EMAIL );
            $callback['phone']              = empty( $callback['email'] ) ? $email : NULL;
            $callback['email']              = !empty( $callback['email'] ) ? $callback['email'] : NULL;

            if( !empty( $callback['name'] ) && !empty( $callback['comments'] ) && ( !empty( $callback['email'] ) || !empty( $callback['phone'] ) ) )
            {
                if( $callback_id = $this->models->getCallback()->addCallback($callback) )
                {
                    $callback['callback_id'] = $callback_id['0']['id'];

                    if( !empty( $callback['email'] ) )
                    {
                        $this->sendmail->addCustomer( 8, $callback );
                    }

                    $this->sendmail->addCustomer( 7, $callback );

                    setcookie("callback", '1', time()+3600);

                    return $this->response->redirect([ 'for' => 'homepage' ]);
                }
            }
            else
            {
                $this->session->set( 'callback', $callback );
                $this->flash->error( 'Будь ласка, заповніть всі необхідні поля' );
                return $this->response->redirect([ 'for' => 'callback_errors' ]);
            }
        }

        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT);

        $this->view->setVars([
            'lang_id'   => $lang_id,
            'customer'  => $customer,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function callbackErrorsAction( $lang_id = '1' )
    {
        $callback_session   = $this->session->get( 'callback' );
        $callback['id']     = $this->session->get('id');
        $customer           = [];

        if( !empty( $callback['id'] ) )
        {
            $customer       = $this->models->getCustomers()->getCustomer($callback['id']);
        }

        //p($callback_session,1);

        if( $this->request->isPost() )
        {
            $callback['name']               = $this->request->getPost('name', 'string', NULL );
            $email                          = $this->request->getPost('email', 'string', NULL );
            $callback['comments']           = $this->request->getPost('comments', 'string', NULL );
            $callback['email']              = filter_var( $email, FILTER_VALIDATE_EMAIL );
            $callback['phone']              = empty( $callback['email'] ) ? $email : NULL;
            $callback['email']              = !empty( $callback['email'] ) ? $callback['email'] : NULL;

            if( !empty( $callback['name'] ) && !empty( $callback['comments'] ) && ( !empty( $callback['email'] ) || !empty( $callback['phone'] ) ) )
            {
                if( $callback_id = $this->models->getCallback()->addCallback($callback) )
                {
                    $callback['callback_id'] = $callback_id['0']['id'];

                    if( !empty( $callback['email'] ) )
                    {
                        $this->sendmail->addCustomer( 8, $callback );
                    }

                    $this->sendmail->addCustomer( 7, $callback );

                    setcookie("callback", '1', time()+3600);

                    return $this->response->redirect([ 'for' => 'homepage' ]);
                }
            }
        }

        $this->view->setVars([
            'lang_id'           => $lang_id,
            'callback_session'  => $callback_session,
            'customer'          => $customer,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////
}