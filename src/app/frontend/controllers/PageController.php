<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace controllers;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class PageController extends \Phalcon\Mvc\Controller
{
    ///////////////////////////////////////////////////////////////////////////

    public function indexAction()
    {
        $this->view->setVars([
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function typeAction( $type, $type_child = '', $lang_id = '1' )
    {
        $params         = $this->dispatcher->getParams();
        $type           = $params['type'];
        $type_child     = !empty($params['type_child']) ? trim( $params['type_child'], '-' ) : NULL;

        $catalog        = $this->common->getTypeSubtype( $type, $type_child, NULL, $lang_id );

        //p($catalog,1);

        $this->view->setVars([
            'catalog'       => $catalog,
            'type_child'    => $type_child,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function subtypeAction( $type, $subtype, $sort = 0, $page = '', $lang_id = '1' )
    {
        $params         = $this->dispatcher->getParams();
        $page           = !empty( $params['page']  ) ? $params['page']                  : 1;
        $sort           = !empty( $params['sort']  ) ? explode( '-', $params['sort'] )  : [];
        sort($sort);

        //p($params,1);
        $catalog        = $this->common->getTypeSubtype( $type, NULL, $subtype, $lang_id );
//p($catalog,1);
        $current_url    = '/'.$catalog['type_alias'].'/'.$catalog['subtype_alias'];

        $sort_default_1 = 0;
        $sort_default_2 = 3;

        if( !empty( $sort ) )
        {
            if( count( $sort ) == 1 )
            {
                if( in_array($sort, [0,1,3]) )
                {
                    $sort_default_1 = $sort['0'];
                }
                else
                {
                    $sort_default_2 = $sort['0'];
                }
            }
            elseif( count( $sort ) == 2 )
            {
                $sort_default_1 = $sort['0'];
                $sort_default_2 = $sort['1'];
            }
        }

        $current_url_without_price = $current_url.'/';

        $filters_ = $this->models->getFilters()->getFilters( $lang_id, $catalog['type_id'], $catalog['subtype_id'] );

        $filters = [];

        if( !empty( $filters_ ) )
        {
            $filters_with_urls = $this->common->seo_important( $filters_, [], $current_url, NULL, $sort );

            foreach( $filters_with_urls as &$f )
            {
                $f['options_']          = !empty( $f['options'] ) ? $this->etc->hstore2arr($f['options']) : '';
                $f['is_seo_important']  = !empty( $f['options'] ) ? $f['options_']['is_seo_important'] : '';

                unset($f['options']);
                unset($f['options_']);
                $filters[$f['filter_key_value']][] = $f;
            }
        }

        $groups = $this->models->getItems()->getGroupsBySubtype( $lang_id, $catalog['type_id'], $catalog['subtype_id'], $page, $sort );

        foreach( $groups as $k => $g )
        {
            $groups[$k]['type_id']      = $catalog['type_id'];
            $groups[$k]['subtype_id']   = $catalog['subtype_id'];
        }

        //p($groups,1);

        if( !empty( $groups ) )
        {
            $groups_ = $this->common->getGroups( $lang_id, $groups );
        }
        //p($groups_,1);
        $page_url_for_sort   = $this->common->getUrlForSort( $params, $sort_default_1, $sort_default_2 );
        $max_min_price = $this->models->getItems()->getMaxMinPrice( $catalog['type_id'], $catalog['subtype_id'] );

        $total = $this->models->getItems()->getAllItems( $lang_id, $catalog['type_id'], $catalog['subtype_id'] );

        $this->view->setVars([
            'catalog'                   => $catalog,
            'groups'                    => $groups_,
            'filters'                   => $filters,
            'max_min_price'             => $max_min_price['0'],
            'total'                     => $total['0']['items'],
            'page'                      => $page,
            'current_url'               => $current_url,
            'current_url_without_price' => $current_url_without_price,
            'page_url_for_sort'         => $page_url_for_sort,
            'sort_default_1'            => $sort_default_1,
            'sort_default_2'            => $sort_default_2,
            'sort'                      => $sort,
            'filters_with_urls'         => $filters_with_urls
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function itemAction( $type, $subtype, $group_alias, $item_id, $lang_id = '1' )
    {
        //p($item_id,1);
        $looked         = $this->session->get('looking_items', []);
        $looked[]       = $item_id;
        $looked         = array_unique( $looked );
        $looked         = array_reverse( $looked );
        $looked         = array_chunk( $looked, 5 );
        $this->session->set( 'looking_items', array_reverse($looked['0']) );

        $catalog    = $this->common->getTypeSubtype( $type, NULL, $subtype, $lang_id );

        $item           = $this->models->getItems()->getOneItem( $lang_id, $catalog['type_id'], $catalog['subtype_id'], $item_id );
        $properties     = $this->models->getProperties()->getPropertiesByItemId( $lang_id, $catalog['type_id'], $catalog['subtype_id'], $item_id );
        $filters        = $this->models->getFilters()->getFiltersByItemId( $lang_id, $catalog['type_id'], $catalog['subtype_id'], $item_id );
        $colors_info    = $this->models->getItems()->getColorsInfoByColorId( $lang_id, $item['0']['color_id'] );

        $item['0']['images']            = $this->etc->int2arr( $item['0']['photogallery'] );
        $item['0']['color_title']       = $colors_info['0']['color_title'];
        $item['0']['absolute_color']    = $colors_info['0']['absolute_color'];
        $properties['0']['value_value'] = nl2br($properties['0']['value_value']);

        //p($colors_info,1);

        foreach( $filters as $f )
        {
            if( $f['key_value'] == 'Виробник' )
            {
                $item['0']['brand'] = $f['value_value'];
            }
        }

        $sizes          = $this->models->getItems()->getSizes( $lang_id, $catalog['type_id'], $catalog['subtype_id'], $group_alias );
        $sizes_colors   = [];
        $sizes_colors_   = [];

        foreach( $sizes as $k => &$s )
        {
            $s['link'] = $this->url->get([ 'for' => 'item', 'type' => $type, 'subtype' => $subtype, 'group_alias' => $group_alias, 'item_id' => $s['id'] ]);
            $s['image'] = !empty( $s['cover'] ) ? $this->storage->getPhotoUrl( $s['cover'], 'avatar', 'color' ) : '';

            if(!empty($s['color_id']))
            {
                //p('hello',1);
                $sizes_colors['sizes'][]        = $s['size'];
                $sizes_colors['colors'][]       = $s['color_id'];

                $sizes_colors_[$s['size']][]    = $s;
                $sizes_colors__[$s['color_id']][]  = $s;
                //$sizes_colors_[$s['size']]['id'][]    = $s['id'];

            }
        }

        if( !empty( $sizes_colors['sizes'] ) )
        {
            $sizes_colors['sizes'] = array_unique( $sizes_colors['sizes'] );
        }
        if( !empty( $sizes_colors['sizes'] ) )
        {
            $sizes_colors['colors'] = array_unique( $sizes_colors['colors'] );
        }

        //p($sizes_colors__,1);
        // get news

        $news = $this->models->getNews()->getNewsByGroupId( $lang_id, $item['0']['group_id'] );

        foreach( $news as $k => $n )
        {
            $news[$k]['image']  = !empty( $n['cover'] ) ? $this->storage->getPhotoUrl( $n['cover'], 'news', '180x120' ) : '';
            $news[$k]['link']   = $this->url->get([ 'for' => 'one_news', 'news_id' => $n['id'], 'news_alias' => $n['alias'] ]);
        }

        //p($news,1);

        // get popular items_groups

        $popular_groups     = $this->models->getItems()->getPopularItems( $lang_id );
        $popular_groups_    = $this->common->getGroups( $lang_id, $popular_groups );

        $this->view->setVars([
            'catalog'           => $catalog,
            'item'              => $item['0'],
            'group_alias'       => $group_alias,
            'item_id'           => $item_id,
            'properties'        => $properties,
            'filters'           => $filters,
            'sizes'             => $sizes,
            'sizes_colors'      => $sizes_colors,
            'sizes_colors_'     => $sizes_colors_,
            'sizes_colors__'    => $sizes_colors__,
            'popular_groups'    => $popular_groups_,
            'news'              => $news
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function topItemsAction()
    {
        $lang_id = 1;

        if( $this->request->isAjax() && $this->request->isPost() )
        {
            $block_class    = $this->request->getPost( 'block_class', 'string', '' );
            $page           = $this->request->getPost( 'next_page', 'string', '' );
            $news_id        = $this->request->getPost( 'news_id', 'string', '' );
        }

        switch( $block_class )
        {
            case 'top_items':
            default:
                $groups = $this->models->getItems()->getTopGroups( $lang_id, $page );
                break;

            case 'recomended_items':
                $groups = $this->models->getItems()->getRecommendedGroups( $lang_id, $page );
                break;

            case 'stock_items':
                $groups = $this->models->getItems()->getStockGroups( $lang_id, $page );
                break;

            case 'recomended_groups':
                $groups_ids = $this->models->getNews()->getGroupsIdsByNewsId( $news_id );

                if( !empty( $groups_ids ) )
                {
                    $news2groups_ids_   = $this->etc->int2arr($groups_ids['0']['group_id']);

                    $news2groups_ids    = array_chunk( $news2groups_ids_, \config::get( 'limits/groups2news' ) );

                    $groups             = $this->models->getItems()->getNews2Groups( $lang_id, $news2groups_ids[$page-1] );
                }
                break;
        }

        //p($groups,1);

        $groups_         = $this->common->getGroups( $lang_id, $groups );

        //p($groups_,1);

        die( json_encode( $groups_ ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function changeWithSizeAction()
    {
        $lang_id = 1;

        if( $this->request->isAjax() && $this->request->isPost() )
        {
            $item_id        = $this->request->getPost( 'item_id', 'int', '' );
            $type_id        = $this->request->getPost( 'type_id', 'int', '' );
            $subtype_id     = $this->request->getPost( 'subtype_id', 'int', '' );
            $group_alias    = $this->request->getPost( 'group_alias', 'string', '' );

            $item           = $this->models->getItems()->getOneItem( $lang_id, $type_id, $subtype_id, $item_id );
            $filters        = $this->models->getFilters()->getFiltersByItemId( $lang_id, $type_id, $subtype_id, $item_id );

            $colors_info    = $this->models->getItems()->getColorsInfoByColorId( $lang_id, $item['0']['color_id'] );

            $item['0']['color_title']       = $colors_info['0']['color_title'];
            $item['0']['absolute_color']    = $colors_info['0']['absolute_color'];


            $item['0']['color']             =
                '<div class="float properties">Оберіть колір: </div>'.
                '<div class="float properties" style="color:'.$colors_info['0']['absolute_color'].'">'.$colors_info['0']['color_title'].'</div>';

            $item['0']['alias']             = $this->url->get([ 'for' => 'item', 'type' => $item['0']['type_alias'], 'subtype' => $item['0']['subtype_alias'], 'group_alias' => $group_alias, 'item_id' => $item_id ]);
            $item['0']['filters']           = $filters;
            $item['0']['images']            = $this->etc->int2arr( $item['0']['photogallery'] );
            $item['0']['status']            = $item['0']['status'] == 1 ? '<div class="properties properties_presence ">В наявності</div>' : '<div class="properties properties_absent">Відсутній</div>';

            $item['0']['image']             = '';

            if( empty( $item['0']['images'] ) && !empty( $item['0']['cover'] ) )
            {
                $item['0']['image'] .=
                    '<li class="float width_400">'.
                        '<a href="'.$this->storage->getPhotoUrl( $item['0']['cover'], 'avatar', '400x' ).'" title="'.$item['0']['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $item['0']['cover'], 'avatar', '800x' ).'\'"  class="thumbnail">'.
                            '<img src="'.$this->storage->getPhotoUrl( $item['0']['cover'], 'avatar', '400x' ).'" alt="'.$item['0']['title'].'" class="image_400">'.
                        '</a>'.
                    '</li>';
            }
            elseif( !empty( $item['0']['images'] ) && !empty( $item['0']['cover'] ) )
            {
                foreach( $item['0']['images'] as $k => $i )
                {
                    if( $k == 0 )
                    {
                        $item['0']['image'] .=
                            '<li class="float width_400">'.
                                '<a href="'.$this->storage->getPhotoUrl( $i, 'group', '800x' ).'" title="'.$item['0']['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $i, 'group', '128x128' ).'\'"  class="thumbnail">'.
                                    '<img src="'.$this->storage->getPhotoUrl( $i, 'group', '400x400' ).'" alt="'.$item['0']['title'].'" class="image_400">'.
                                '</a>'.
                            '</li>';
                    }
                    else
                    {
                        $item['0']['image'] .=
                            '<li class="float width_128 '.($k%3==0 ? 'last' : '').'">'.
                                '<a href="'.$this->storage->getPhotoUrl( $i, 'group', '800x' ).'" title="'.$item['0']['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $i, 'group', '128x128' ).'\'"  class="thumbnail">'.
                                    '<img src="'.$this->storage->getPhotoUrl( $i, 'group', '128x128' ).'" alt="'.$item['0']['title'].'" class="image_128">'.
                                '</a>'.
                            '</li>';
                    }
                }

                $item['0']['image']  .=
                    '<li class="float width_128 '.(count($item['0']['images'])%3==0 ? 'last' : '').'">'.
                        '<a href="'.$this->storage->getPhotoUrl( $item['0']['cover'], 'avatar', '800x' ).'" title="'.$item['0']['title'].'"  data-options="thumbnail: \''.$this->storage->getPhotoUrl( $item['0']['cover'], 'avatar', '128x' ).'\'"  class="thumbnail">'.
                            '<img src="'.$this->storage->getPhotoUrl( $item['0']['cover'], 'avatar', '128x' ).'" alt="'.$item['0']['title'].'" class="image_128">'.
                        '</a>'.
                    '</li>';
            }

        }

        die( json_encode( $item ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function changeImageWithSizeAction()
    {
        $lang_id = 1;

        if( $this->request->isAjax() && $this->request->isPost() )
        {
            $item_id        = $this->request->getPost( 'item_id', 'int', '' );
            $type_id        = $this->request->getPost( 'type_id', 'int', '' );
            $subtype_id     = $this->request->getPost( 'subtype_id', 'int', '' );
            $group_alias    = $this->request->getPost( 'group_alias', 'string', '' );

            $item           = $this->models->getItems()->getOneItem( $lang_id, $type_id, $subtype_id, $item_id );
            $filters        = $this->models->getFilters()->getFiltersByItemId( $lang_id, $type_id, $subtype_id, $item_id );

            $item['0']['alias'] = $this->url->get([ 'for' => 'item', 'type' => $item['0']['type_alias'], 'subtype' => $item['0']['subtype_alias'], 'group_alias' => $group_alias, 'item_id' => $item_id ]);
            $item['0']['filters'] = $filters;

        }

        die( json_encode( $item ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function changeSimilarItemsAction()
    {
        $groups     = [];
        $lang_id    = 1;

        if( $this->request->isAjax() && $this->request->isPost() )
        {
            $type_id        = $this->request->getPost( 'type_id', 'int', '' );
            $subtype_id     = $this->request->getPost( 'subtype_id', 'int', '' );
            $group_id       = $this->request->getPost( 'group_id', 'int', '' );
            $similar        = $this->request->getPost( 'similar', 'string', '' );

            switch( $similar )
            {
                case 'popular':
                default:
                    $groups = $this->models->getItems()->getPopularItems( $lang_id );
                    break;

                case 'same':
                    $groups = $this->models->getItems()->getSameItems( $lang_id, $type_id, $subtype_id );
                    break;

                case 'buy_with':
                    $groups = $this->models->getItems()->getBuyWithItems( $lang_id, $group_id );
                    break;

                case 'viewed':
                    $looked     = $this->session->get('looking_items', []);

                    if( !empty( $looked ) )
                    {
                        $groups_looked   = $this->models->getItems()->getLookedGroups( $lang_id, $looked );

                        foreach( $looked as $l )
                        {
                            foreach( $groups_looked as &$g )
                            {
                                if( $l == $g['id'] )
                                {
                                    $groups_temp[$l] = $g;
                                }
                            }
                        }

                        foreach( $groups_temp as $g )
                        {
                            $groups[] = $g;
                        }
                    }

                    break;
            }

            $groups_         = $this->common->getGroups( $lang_id, $groups );
        }
        //p($groups,1);

        die( json_encode( $groups_ ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function filtersAction( $type, $subtype, $filter_ids = '', $filter_alias = '', $price = '', $sort = '', $page = 1, $lang_id = 1 )
    {
        $params         = $this->dispatcher->getParams();
        $filter_ids     = isset( $params['filter_ids'] )    && !empty( $params['filter_ids'] )      ? $params['filter_ids']             : '';
        $filter_alias   = isset( $params['filter_alias'] )  && !empty( $params['filter_alias'] )    ? $params['filter_alias']           : '';
        $price          = isset( $params['price'] )         && !empty( $params['price'] )           ? $params['price']                  : '';
        $page           = !empty( $params['page']  )                                                ? $params['page']                   : 1;
        $sort           = !empty( $params['sort']  )                                                ? explode( '-', $params['sort'] )   : [0,3];
        sort($sort);
        $price_array    = !empty( $price ) ? explode( '-', $price ) : [];

        $sort_default_1 = 0;
        $sort_default_2 = 3;

        if( !empty( $sort ) )
        {
            if( count( $sort ) == 1 )
            {
                if( in_array($sort, [0,1,3]) )
                {
                    $sort_default_1 = $sort['0'];
                }
                else
                {
                    $sort_default_2 = $sort['0'];
                }
            }
            elseif( count( $sort ) == 2 )
            {
                $sort_default_1 = $sort['0'];
                $sort_default_2 = $sort['1'];
            }
        }

        //p($params,1);

        $catalog        = $this->common->getTypeSubtype( $type, NULL, $subtype, $lang_id );

        $current_url_without_price  = trim('/'.$catalog['type_alias'].'/'.$catalog['subtype_alias'].'/'.$filter_ids.$filter_alias, '--');
        $current_url_without_sort   = trim('/'.$catalog['type_alias'].'/'.$catalog['subtype_alias'].'/'.trim($filter_ids.$filter_alias.(!empty($price) ? '--price-'.$price : ''), '--') );
        $current_url                = trim('/'.$catalog['type_alias'].'/'.$catalog['subtype_alias'].'/'.trim($filter_ids.$filter_alias.(!empty($price) ? '--price-'.$price : ''), '--'), '--').( !empty($sort) ? '/sort-'.join('-', $sort) : '' );
//p($current_url_without_sort,1);
        $filter_ids_                = trim( $filter_ids, '-' );
        $filter_applied_ids_array   = !empty( $filter_ids ) ? explode( '-', $filter_ids_ ) : [];

        $filters_                   = $this->models->getFilters()->getFilters( $lang_id, $catalog['type_id'], $catalog['subtype_id'] );
        $filter_all_ids             = $this->common->array_column( $filters_, 'id' );

        $filters                    = [];
        $filters_applied            = [];

        if( !empty( $filters_ ) )
        {
            $url = '/'.$catalog['type_alias'].'/'.$catalog['subtype_alias'];

            $filters_with_urls = $this->common->seo_important( $filters_, $filter_applied_ids_array, $url, $price, $sort );

            foreach( $filters_with_urls as &$f )
            {
                $f['options_']          = !empty( $f['options'] ) ? $this->etc->hstore2arr($f['options']) : '';
                $f['is_seo_important']  = !empty( $f['options'] ) ? $f['options_']['is_seo_important'] : '';
                $f['checked']           = in_array( $f['id'], $filter_applied_ids_array ) ? '1' : '';

                unset($f['options']);
                unset($f['options_']);

                $filters[$f['filter_key_value']][] = $f;

                if( in_array( $f['id'], $filter_applied_ids_array ) )
                {
                    $filters_applied[$f['id']] = $f;
                }
            }
        }
        //p($price_array,1);

        $groups_by_filters          = $this->models->getItems()->getGroupsByFilters( $filter_applied_ids_array, $price_array, $catalog['type_id'], $catalog['subtype_id'] );

        foreach( $groups_by_filters as $g )
        {
            $groups_by_key_id[$g['key_id']][]   = $g['group_id'];
            $groups_by_key_id[$g['key_id']]     = array_unique($groups_by_key_id[$g['key_id']]);
        }

        sort($groups_by_key_id);

        if( count( $groups_by_key_id ) > 1 )
        {
            $result_groups = call_user_func_array('array_intersect',$groups_by_key_id);
        }
        else
        {
            $result_groups = $groups_by_key_id['0'];
        }

        if( !empty( $result_groups ) )
        {
            $groups     = $this->models->getItems()->getResultGroups( $lang_id, $result_groups, $filter_applied_ids_array, $price_array, $sort, $page );
            $groups_    = $this->common->getGroups( $lang_id, $groups );
        }
//p($groups_by_key_id,1);
        $total      = count($result_groups);

        $page_url_for_filter = $this->common->getUrlForFilter( $params, $page );
        $page_url_for_sort   = $this->common->getUrlForSort( $params, $sort_default_1, $sort_default_2 );

        //p($page_url_for_sort,1);

        $max_min_price = $this->models->getItems()->getMaxMinPrice( $catalog['type_id'], $catalog['subtype_id'] );

        $this->view->pick('page/subtype');

        $this->view->setVars([
            'catalog'                   => $catalog,
            'groups'                    => $groups_,
            'filters'                   => $filters,
            'filters_applied'           => $filters_applied,
            'max_min_price'             => $max_min_price['0'],
            'total'                     => $total,
            'page'                      => $page,
            'current_url_without_price' => $current_url_without_price,
            'current_url_without_sort'  => $current_url_without_sort,
            'current_url'               => $current_url,
            'price_array'               => $price_array,
            'page_url_for_filter'       => $page_url_for_filter,
            'page_url_for_sort'         => $page_url_for_sort,
            'sort_default_1'            => $sort_default_1,
            'sort_default_2'            => $sort_default_2,
            'sort'                      => $sort

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function searchAction( $search = '', $page = 1, $lang_id = 1 )
    {
        if( $this->request->has('search') )
        {
            $search = $this->request->get('search', 'string', NULL );

            return $this->response->redirect([ 'for' => 'search_items_route', 'search' => $search ]);
        }

        $items_         = $this->models->getItems()->getItemsByTerm( $search, 'items', $page );
        $total_items    = $this->models->getItems()->getTotalItemsByTerm( $search );

        $type_subtype   = $this->models->getItems()->getTypeSubtypeByTerm( $search, $lang_id );
        $type_subtype   = array_filter( $type_subtype );

        foreach( $type_subtype as $t )
        {
            $subtypes[$t['type']][$t['subtype']] =
                [
                    'subtype_alias' => $t['subtype_alias'],
                    'subtype_title' => $t['subtype_title']
                ];
            $type_subtype_[$t['type']] =
                [
                    'type_alias' => $t['type_alias'],
                    'type_title' => $t['type_title'],
                    'subtype' => $subtypes[$t['type']],

                ];
        }

        $items          = [];

        if( !empty( $items_ ) )
        {
            $items_ids  = $this->common->array_column( $items_, 'item_id' );
            $items      = $this->models->getItems()->getItemsByIds( $lang_id, $items_ids );

            foreach( $items as &$i )
            {
                $i['cover']         = !empty( $i['group_cover'] ) ? $this->storage->getPhotoUrl( $i['group_cover'], 'avatar', '200x' ) : '/images/packet.jpg';
                $i['alias']         = $this->url->get([ 'for' => 'item', 'type' => $i['type_alias'], 'subtype' => $i['subtype_alias'], 'group_alias' => $i['group_alias'], 'item_id' => $i['id'] ]);

                $i['options_'] = $this->etc->hstore2arr($i['options']);

                $i['is_new'] = !empty( $i['options_']['is_new'] ) ? $i['options_']['is_new'] : '0';
                $i['is_top'] = !empty( $i['options_']['is_top'] ) ? $i['options_']['is_top'] : '0';

                unset($i['options_']);
                unset($i['options']);
            }
        }

        //p($type_subtype_,1);

        $this->view->setVars([
            'groups'        => $items,
            'page'          => $page,
            'search'        => $search,
            'type_subtype'  => $type_subtype_,
            'total'         => $total_items['0']['total']
        ]);


    }

    ///////////////////////////////////////////////////////////////////////////

    public function compareItemsAction( $type, $subtype, $compare_ids, $lang_id = 1 )
    {
        // TODO: make right session for adding ids in url

        //p($this->dispatcher->getParams(),1);

        if( empty( $compare_ids ) )
        {
            //return $this->response->redirect([ 'for' => 'homepage' ]);
        }

        $items_ids              = explode( '-', $compare_ids );
        //p($items_ids, 1);

        $items                  = $this->models->getItems()->getItemsByIds( $lang_id, $items_ids );
        $properties             = $this->models->getProperties()->getPropertiesByTypeSubtype( $lang_id, $type, $subtype ); // for cache
        $properties_for_items   = $this->models->getProperties()->getPropertiesForItems( $items_ids );


        foreach( $properties as $p )
        {
            $properties_[$p['id']] = $p;
            $properties_names[$p['property_key_id']] = $p['key_value'];
        }

        foreach( $properties_for_items as $p )
        {
            $properties_for_items_[$p['item_id']][] = $properties_[$p['property_id']];
        }

        foreach( $properties_for_items_ as $key => $val )
        {
            foreach( $val as $v )
            {
                //$properties_for_items__[$v['key_value']][$key] = $v['value_value'];
                $properties_for_items___[$v['key_value']][] = $v['value_value'];
            }
        }

        foreach( $properties_for_items___ as $p )
        {
            $count[] = count($p);
        }

        foreach( $items as &$i )
        {
            //p(join( '-', array_diff( $items_ids, [$i['id']] )));
            $i['cover']         = !empty( $i['group_cover'] ) ? $this->storage->getPhotoUrl( $i['group_cover'], 'avatar', '200x' ) : '/images/packet.jpg';
            $i['alias']         = $this->url->get([ 'for' => 'item', 'type' => $i['type_alias'], 'subtype' => $i['subtype_alias'], 'group_alias' => $i['group_alias'], 'item_id' => $i['id'] ]);
            $i['alias_del']     =
                count( $items_ids ) == 1
                ?
                    $this->url->get([ 'for' => 'homepage'])
                :
                    $this->url->get([ 'for' => 'compare_items', 'type' => $i['type_alias'], 'subtype' => $i['subtype_alias'], 'compare_ids' => join( '-', array_diff( $items_ids, [$i['id']] )) ]);
        }

        //p($items,1);
        //p($properties_for_items__,1);

        $this->view->setVars([
            'properties_names'      => array_unique($properties_names),
            'properties_for_items'  => $properties_for_items___,
            'items'                 => $items,
            'count'                 => max($count)

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////
}
