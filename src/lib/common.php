<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace 
{
    /**
     * common
     *
     * @author      Roman Telychko
     * @version     0.1.20130712
     */
    class common extends \core
    {
        /////////////////////////////////////////////////////////////////////////////
    
        /**
         * common::hashPasswd()
         *
         * @author      Roman Telychko
         * @version     3.0.20131010
         *
         * @param     string      $passwd
         * @return    string      $hash
         */
        public function hashPasswd( $passwd )
        {
            $salt1 = 'IKudI9k4sts40Spu1yAwcxeaD7umJ8aMAYt6Uj862VTHBh55sMi7DPRkvgckXK88ecj6aDy1Q0DYB28ZVuygR6rlqFoRFcKn45XT5gzbADbzNfBHxMgUmEnb79CyFx7O';            # pwgen -s 128
            $salt2 = 'JzbdFHvuEamXvr8jXWCHkoRqXwEQE86NwPH27vxsdp7T3ln1rk2Mbtu9ADAUIgxpDePe9jzT0KpQceQLTFMSl1fZjmYIl1jbRtlNcuFjUaHy5X0FE55MpT8Kf2xZZnGI';            # pwgen -s 128

            $hash = '//'.$salt1.'//'.base64_encode( $passwd ).'//';
            $pieces = str_split( $salt2, 10 );

            for( $i=0; $i<10000; $i++ )
            {
                $hash = hash( 'sha512', $pieces[( $i % 10 )].'|'.$hash );
            }

            return $hash;
        }
    
        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::arraySortByColumn()
         *
         * @author      Roman Telychko
         * @version     0.1.20130712
         *
         * @param     	array        $arr
         * @param     	string       $column
         * @param     	integer      $direction
         * @return    	bool
         *//*
        public function arraySortByColumn( &$arr, $column, $direction = SORT_ASC )
        {
            $arr_column = [];

            foreach ($arr as $k => $d)
            {
                $arr_column[$k] = $d[$column];
            }

            array_multisort( $arr_column, $direction, $arr );
        }*/

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::array_column()
         *
         * @author      Roman Telychko
         * @version     0.1.20131030
         *
         * @param     	array        $arr
         * @param     	string       $column_key
         * @param     	string       $index_key
         * @return    	array
         */
        public function array_column( $arr, $column_key, $index_key = null )
        {
            if( empty($arr) )
            {
                return [];
            }

            $data   = [];
            $c      = 0;

            foreach( $arr as $a )
            {
                if( isset($a[$column_key]) )
                {
                    $data[ ( !empty($index_key) && isset($a[$index_key]) ) ? $a[$index_key] : $c ] = $a[$column_key];
                    $c++;
                }
            }

            return $data;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::paginate()
         *
         * @author          Roman Telychko
         * @version         0.1.20130627
         *
         * @param           array           $data
         * @param           bool            $return_output
         * @return          string
         */
        public function paginate( $data = [], $return_output = false )
        {
            //p($data,1);
            $data = array_merge(
                [
                    'page'              => 1,
                    'items_per_page'    => 20,
                    'total_items'       => 0,
                    'url_for'           => [],
                    'links_count'       => 16,
                ],
                $data
            );

            if( empty($data['page']) || empty($data['total_items']) || empty($data['url_for']) )
            {
                return false;
            }

            if( $data['total_items'] <= $data['items_per_page'] )
            {
                return false;
            }

            // pages count
            $data['pages_count'] = intval( ceil( $data['total_items'] / $data['items_per_page'] ) );

            if( $data['page'] > $data['pages_count'] )
            {
                $data['page'] = $data['pages_count'];
            }
            elseif( $data['page'] < 1 )
            {
                $data['page'] = 1;
            }

            // links count
            if( $data['links_count'] <= $data['pages_count'] )
            {
                if( $data['page'] <= floor( $data['links_count'] / 2 ) + 1 )
                {
                    $start_i = 1;
                }
                else
                {
                    $start_i = $data['page'] - floor( $data['links_count'] / 2 );
                }

                $stop_i     = $start_i + $data['links_count'] - 1;

                if( $stop_i > $data['pages_count'] )
                {
                    $start_i    = $data['pages_count'] - $data['links_count'] + 1;
                    $stop_i     = $data['pages_count'];
                }
            }
            else
            {
                $start_i    = 1;
                $stop_i     = $data['pages_count'];
            }

            $url_obj = $this->getDi()->get('url');

            $output =
                '<ul class="clearfix">'.
                    ( $data['page']==1 ? '' : '<li class="float"><a href="'.$url_obj->get( array_merge( $data['url_for'], [ 'page' => ( ($data['page'] == 1) ? 1 : $data['page'] - 1 ) ] ) ).'" title="Previous ('.( ($data['page'] == 1) ? 1 : $data['page'] - 1 ).')" class="previous"><img src="/images/page_arrow_left.png" alt="previous" width="10" height="18" /></a></li>' );

            // build links
            //p($data['page'],1);
            for( $i = $start_i; $i <= $stop_i; $i++ )
            {
                $output .= '<li'.( ($data['page'] == $i) ? ' class="current"' : '' ).'><a href="'.$url_obj->get( array_merge( $data['url_for'], [ 'page' => $i ] ) ).'" class="float'.( ($data['page'] == $i) ? ' current hover' : '' ).'" title="Page '.$i.'">'.$i.'</a></li>';
            }

            $output .=
                        ( $data['page']==$data['pages_count'] ? '' : '<li class="float"><a href="'.$url_obj->get( array_merge( $data['url_for'], [ 'page' => ( ($data['page'] + 1 == $data['pages_count']) ? $data['pages_count'] : $data['page'] + 1 ) ] ) ).'" title="Next ('.( ($data['page'] + 1 == $data['pages_count']) ? $data['pages_count'] : $data['page'] + 1 ).')" class="next"><img src="/images/page_arrow_right.png" alt="previous" width="10" height="18" /></a></li>' ).
                    '</ul>';

            if( $return_output )
            {
                return $output;
            }
            else
            {
                echo( $output );

                return true;
            }
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * common::shortenString()
         *
         * @author          Roman Telychko
         * @version         0.1.20110930
         *
         * @param           string      $str
         * @param           integer     $length
         * @return          string
         */
        public function shortenString( $str, $length = 200 )
        {
            if( strlen($str) > $length )
            {
                $str = wordwrap( $str, $length, '||BR||', false );
                $str = mb_substr( $str, 0, mb_strpos( $str, '||BR||', 0, 'UTF-8' ), 'UTF-8' );
                $str .= '...';
            }

            return $str;
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * common::transliterate()
         *
         * @author          Jane Bezmaternykh
         * @version         0.1.20131115
         *
         * @param           string
         * @param           integer      $lang_id
         * @return          string
         */
        public function transliterate( $str, $lang_id = 1 )
        {
            $str = mb_strtolower( trim($str), 'UTF-8');

            $str = preg_replace('/\s{2,}/', ' ', $str);

            if( $lang_id==1 )  /* uk */
            {
                $str = str_replace( 'и', 'y', $str );
                $str = str_replace( 'й', 'yi', $str );
                $str = str_replace( 'і', 'i', $str );
                $str = str_replace( 'ї', 'yi', $str );
                $str = str_replace( 'є', 'ye', $str );
            }

            if( $lang_id==2 )  /* ru */
            {
                $str = str_replace( 'и', 'i', $str );
                $str = str_replace( 'й', 'yi', $str );
                $str = str_replace( 'ы', 'y', $str );
                $str = str_replace( 'э', 'e', $str );
                $str = str_replace( 'ъ', '', $str );
            }

            $str = str_replace( 'а', 'a', $str );
            $str = str_replace( 'б', 'b', $str );
            $str = str_replace( 'в', 'v', $str );
            $str = str_replace( 'г', 'g', $str );
            $str = str_replace( 'д', 'd', $str );
            $str = str_replace( 'е', 'e', $str );
            $str = str_replace( 'ж', 'j', $str );
            $str = str_replace( 'з', 'z', $str );
            $str = str_replace( 'к', 'k', $str );
            $str = str_replace( 'л', 'l', $str );
            $str = str_replace( 'м', 'm', $str );
            $str = str_replace( 'н', 'n', $str );
            $str = str_replace( 'о', 'o', $str );
            $str = str_replace( 'п', 'p', $str );
            $str = str_replace( 'р', 'r', $str );
            $str = str_replace( 'с', 's', $str );
            $str = str_replace( 'т', 't', $str );
            $str = str_replace( 'у', 'u', $str );
            $str = str_replace( 'ф', 'f', $str );
            $str = str_replace( 'х', 'h', $str );
            $str = str_replace( 'ц', 'ts', $str );
            $str = str_replace( 'ч', 'ch', $str );
            $str = str_replace( 'ш', 'sh', $str );
            $str = str_replace( 'щ', 'sch', $str );
            $str = str_replace( 'ю', 'yu', $str );
            $str = str_replace( 'я', 'ya', $str );
            $str = str_replace( 'ь', '', $str );
            $str = preg_replace( '/\s/', '_', $str );

            $str = preg_replace( '/[^a-z0-9\_]/', '', $str );
            $str = preg_replace( '/\_{1,}/', '_', $str );

            return $str;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::generatePasswd()
         *
         * @author          Jane Bezmaternykh
         * @version         0.1.20140428
         *
         * @param           integer      $leight
         * @return          string
         */
        public function generatePasswd( $leight )
        {
            $passwd = '';

            $str = "qwertyuiopasdfghjklzxcvbnm123456789";

            for($i=0; $i<$leight; $i++)
            {
                $passwd .= substr($str, mt_rand(0, strlen($str)-1), 1);
            }

            return $passwd;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::getTypeSubtype()
         *
         * @author		Jane Bezmaternykh
         * @version     0.1.20140407
         *
         * @param       string      $type_alias
         * @param       string      $type_child_alias
         * @param       string      $subtype_alias
         * @param       integer     $lang_id
         * @return     	array
         */
        public function getTypeSubtype( $type_alias, $type_child_alias = NULL, $subtype_alias = NULL, $lang_id )
        {
            $types      = $this->getDi()->get('models')->getCatalog()->getTypes( $lang_id );
            $subtypes   = $this->getDi()->get('models')->getCatalog()->getSubtypes( $lang_id );

            //p($types,1);
            //p($types,1);

            foreach( $subtypes as $s )
            {
                $subtypes_[$s['type']][$s['id']] = $s;
            }

            foreach( $types as $t )
            {
                $catalog_[$t['type']]                = $t;
                $catalog_[$t['type']]['subtypes']    = !empty( $subtypes_[$t['type']] ) ? $subtypes_[$t['type']] : '';

                if( $t['parent_id'] > 0 )
                {
                    $catalog_[$t['type']]                           = $t;
                    $catalog_[$t['type']]['subtypes']               = $subtypes_[$t['parent_id']];

                    if( empty( $type_child_alias ) )
                    {
                        $catalog_[$t['parent_id']]['type_children'][]   = $t;
                    }
                    else
                    {
                        $catalog_[$t['parent_id']]['type_children_']  = $t; // for breadcrumbs
                    }
                }
            }

            //p($catalog_,1);

            foreach( $catalog_ as $k => $c )
            {
                //p($c['alias'] == $type_alias,1);
                if( !empty( $type_alias ) )
                {
                    if( $c['alias'] == $type_alias )
                    {
                        if( !empty( $subtype_alias ) )
                        {
                            foreach( $c['subtypes'] as $key => $val )
                            {
                                if( $val['alias'] == $subtype_alias )
                                {
                                    //$catalog = $val;
                                    $catalog['subtype_id']      = $key;
                                    $catalog['subtype_alias']   = $subtype_alias;
                                    $catalog['subtype_title']   = $val['title'];
                                    $catalog['cover']           = $val['cover'];
                                }
                            }
                        }
                        else
                        {
                            $catalog = $c;
                        }

                        $catalog['type_id']     = $k;
                        $catalog['type_alias']  = $c['alias'];
                        $catalog['type_title']  = $c['title'];
                    }
                }
                else
                {
                    $catalog = $catalog_;
                }
            }

            return $catalog;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::getTypeSubtype1()
         *
         * @author		Jane Bezmaternykh
         * @version     0.1.20140407
         *
         * @param       string      $type_alias
         * @param       string      $type_child_alias
         * @param       string      $subtype_alias
         * @param       integer     $lang_id
         * @return     	array
         */
        public function getTypeSubtype1( $type_alias, $type_child_alias = NULL, $subtype_alias = NULL, $lang_id )
        {
            $types      = $this->getDi()->get('models')->getCatalog()->getTypes( $lang_id );
            $subtypes   = $this->getDi()->get('models')->getCatalog()->getSubtypes( $lang_id );

            //p($types,1);
            //p($types,1);

            foreach( $subtypes as $s )
            {
                $subtypes_[$s['type']][$s['id']] = $s;
            }

            foreach( $types as $t )
            {
                $catalog_[$t['type']]                = $t;
                $catalog_[$t['type']]['subtypes']    = !empty( $subtypes_[$t['type']] ) ? $subtypes_[$t['type']] : '';

                if( $t['parent_id'] > 0 )
                {
                    $catalog_[$t['type']]                           = $t;
                    $catalog_[$t['type']]['subtypes']               = $subtypes_[$t['parent_id']];

                    if( empty( $type_child_alias ) )
                    {
                        $catalog_[$t['parent_id']]['type_children'][]   = $t;
                    }
                    else
                    {
                        $catalog_[$t['parent_id']]['type_children_']  = $t; // for breadcrumbs
                    }
                }
            }

            //p($catalog_,1);

            foreach( $catalog_ as $k => $c )
            {
                //p($c['alias'] == $type_alias,1);
                if( !empty( $type_alias ) )
                {
                    if( $c['alias'] == $type_alias )
                    {
                        if( !empty( $subtype_alias ) )
                        {
                            foreach( $c['subtypes'] as $key => $val )
                            {
                                if( $val['alias'] == $subtype_alias )
                                {
                                    //$catalog = $val;
                                    $catalog['subtype_id']      = $key;
                                    $catalog['subtype_alias']   = $subtype_alias;
                                    $catalog['subtype_title']   = $val['title'];
                                    $catalog['cover']           = $val['cover'];
                                }
                            }
                        }
                        else
                        {
                            $catalog = $c;
                        }

                        $catalog['type_id']     = $k;
                        $catalog['type_alias']  = $c['alias'];
                        $catalog['type_title']  = $c['title'];
                    }
                }
                else
                {
                    $catalog = $catalog_;
                }
            }

            return $catalog;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::getGroups()
         *
         * @author		Jane Bezmaternykh
         * @version     0.1.20140407
         *
         * @param       array      $groups
         * @param       string     $lang_id
         * @return     	array
         */
        public function getGroups( $lang_id, $groups )
        {
            $types      = $this->getDi()->get('models')->getCatalog()->getTypes( $lang_id );
            $subtypes   = $this->getDi()->get('models')->getCatalog()->getSubtypes( $lang_id );
            $compare    = $this->getDi()->get('session')->get('compare', []);

            foreach( $subtypes as $s )
            {
                $subtypes_[$s['type']][$s['id']] = $s;
            }

            foreach( $types as $t )
            {
                $catalog[$t['type']]                = $t;
                $catalog[$t['type']]['subtypes']    = !empty( $subtypes_[$t['type']] ) ? $subtypes_[$t['type']] : '';
            }

            if( !empty( $groups ) )
            {
                $item_ids   = $this->array_column( $groups, 'id' );
                $items      = $this->getDi()->get('models')->getItems()->getItemsWithMinPrice( $lang_id, join( ',', $item_ids ) );
//p($groups,1);
                if( !empty( $items ) )
                {

                    $items_ = [];
                    foreach( $items as $i )
                    {
                        $items_[$i['id']] = $i;
                    }

                    //p($items_,1);

                    foreach( $groups as &$g )
                    {
                        $g['price']                 = $items_[$g['id']]['price2'];
                        $g['title']                 = $items_[$g['id']]['title'];
                        $g['description']           = $items_[$g['id']]['description'];
                        $g['content_description']   = $items_[$g['id']]['content_description'];
                        $g['cover']                 = !empty( $g['cover'] ) ? $this->getDi()->get('storage')->getPhotoUrl( $g['cover'], 'avatar', '200x' ) : '/images/packet.jpg';
                        $g['alias']                 = $this->getDi()->get('url')->get([ 'for' => 'item', 'type' => $catalog[$g['type_id']]['alias'], 'subtype' => $catalog[$g['type_id']]['subtypes'][$g['subtype_id']]['alias'], 'group_alias' => $g['alias'], 'item_id' => $g['id'] ]);
                        $g['checked']               = !empty($compare[$g['type_id']][$g['subtype_id']]) && in_array($g['id'], $compare[$g['type_id']][$g['subtype_id']]) ? 1 : 0;

                        if( !empty( $g['options'] ) )
                        {
                            $g['options_']  = $this->getDi()->get('etc')->hstore2arr($g['options']);
                            $g['is_new']    = !empty( $g['options_']['is_new'] ) ? $g['options_']['is_new'] : '0';
                            $g['is_top']    = !empty( $g['options_']['is_top'] ) ? $g['options_']['is_top'] : '0';

                            unset($g['options_']);
                            unset($g['options']);
                        }
                    }
                }
            }

            //p($groups,1);

            return $groups;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::seo_important()
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140412
         *
         * @param     	array        $filters
         * @param     	array        $filters_applied
         * @param     	string       $url
         * @param     	string       $price
         * @param     	array        $sort
         * @return    	array
         */
        public function seo_important( $filters, $filters_applied, $url, $price, $sort )
        {
            $lang_id = 1;
            $seo_important_filters      = $this->getDi()->get('models')->getFilters()->getSeoImportantFilters( $lang_id );
            $seo_important_filters_ids  = self::array_column( $seo_important_filters, 'id' );

            foreach( $seo_important_filters as $f )
            {
                $seo_alias[$f['id']] =
                    [
                        'key'   => $f['filter_key_alias'],
                        'value' => $f['filter_value_alias']
                    ];
            }

            foreach( $filters as $k => &$f )
            {
                $filters[$k]['alias_'] =
                    in_array( $f['id'], $filters_applied )
                        ?
                        array_diff( $filters_applied, [ $f['id'] ] )
                        :
                        array_merge( $filters_applied, [ $f['id'] ] );

                sort( $f['alias_'] );

                $filters[$k]['alias'] =
                    $url.
                    (!empty( $f['alias_']  ) ? '/'.join( '-', $f['alias_'] ) : '').
                    (!empty( $f['alias_']  ) ? ( !empty($price) ? '--price-'.$price : '' ) : ( !empty($price) ? '/price-'.$price : '' )).
                    (!empty($sort) ? '/sort-'.join('-', $sort) : '');

                if( !empty( $f['alias_'] ) )
                {
                    foreach( $f['alias_'] as $v )
                    {
                        if( in_array( $v, $seo_important_filters_ids ) )
                        {
                            $filters[$k]['seo_alias_array'][] = $seo_alias[$v];
                        }
                    }

                    if( !empty( $f['seo_alias_array'] ) )
                    {
                        foreach( $f['seo_alias_array'] as $s )
                        {
                            $f['seo_alias_array_'][$s['key']][] = $s['value'];
                        }

                        foreach( $f['seo_alias_array_'] as $key => $s )
                        {
                            $f['seo_alias_array__'][$key] = $key.'-'.join( '-', $s );
                        }

                        $filters[$k]['alias'] =
                            $url.
                            (!empty(  $f['alias_'] ) && !empty( $f['seo_alias_array__'] ) ? '/'.join( '-', $f['alias_'] ).'--'.join( '--', $f['seo_alias_array__'] ) : '').
                            ( empty( $f['alias_'] ) && empty( $f['seo_alias_array__'] ) ? ( !empty($price) ? '/price-'.$price : '' ) : ( !empty($price) ? '--price-'.$price : '' ) ).
                            ( !empty($sort) ? '/sort-'.join('-', $sort) : '' );


                        unset($f['seo_alias_array_']);
                        unset($f['seo_alias_array__']);
                    }

                    unset($f['seo_alias_array']);
                    unset($f['alias_']);
                }
            }

            return $filters;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::getUrlForFilter()
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140415
         *
         * @param     	array        $params
         * @param     	string       $page
         * @return    	array
         */
        public function getUrlForFilter( $params, $page )
        {
            //p($params,1);

            if( empty($params['filter_ids']) && empty($params['filter_alias']) && empty($params['price']) && !empty($params['sort']) )
            {
                $url = ['for' => 'subtype_sorted_paginated', 'type' => $params['type'], 'subtype' => $params['subtype'], 'sort' => $params['sort'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && empty($params['filter_alias']) && empty($params['price']) && empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_ids_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && empty($params['filter_alias']) && empty($params['price']) && !empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_ids_sorted_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'sort' => $params['sort'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && !empty($params['filter_alias']) && empty($params['price']) && empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_id_alias_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && !empty($params['filter_alias']) && empty($params['price']) && !empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_id_alias_sorted_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'sort' => $params['sort'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && empty($params['filter_alias']) && !empty($params['price']) && empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_id_price_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'price' => $params['price'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && empty($params['filter_alias']) && !empty($params['price']) && !empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_id_price_sorted_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'price' => $params['price'], 'sort' => $params['sort'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && !empty($params['filter_alias']) && !empty($params['price']) && empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_id_alias_price_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'price' => $params['price'], 'page' => $page ];
            }
            elseif( !empty($params['filter_ids']) && !empty($params['filter_alias']) && !empty($params['price']) && !empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_id_alias_price_sorted_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'price' => $params['price'], 'sort' => $params['sort'], 'page' => $page ];
            }
            elseif( empty($params['filter_ids']) && empty($params['filter_alias']) && !empty($params['price']) && empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_price_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'price' => $params['price'], 'page' => $page ];
            }
            elseif( empty($params['filter_ids']) && empty($params['filter_alias']) && !empty($params['price']) && !empty($params['sort']) )
            {
                $url = [ 'for' => 'get_items_with_filters_price_sorted_paginate', 'type' => $params['type'], 'subtype' => $params['subtype'], 'price' => $params['price'], 'sort' => $params['sort'], 'page' => $page ];
            }

            return $url;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * common::getUrlForSort()
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140520
         *
         * @param     	array        $params
         * @param     	string       $sort_default_1
         * @param     	string       $sort_default_2
         * @return    	array
         */
        public function getUrlForSort( $params, $sort_default_1, $sort_default_2 )
        {
            //p($params,1);

            if( empty($params['filter_ids']) && empty($params['filter_alias']) && empty($params['price']) )
            {
                $url[0] = ['for' => 'subtype_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'sort' => '0-'.$sort_default_2];
                $url[1] = ['for' => 'subtype_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'sort' => '1-'.$sort_default_2];
                $url[2] = ['for' => 'subtype_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'sort' => '2-'.$sort_default_2];
                $url[3] = ['for' => 'subtype_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'sort' => $sort_default_1.'-3'];
                $url[4] = ['for' => 'subtype_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'sort' => $sort_default_1.'-4'];
            }
            elseif( !empty($params['filter_ids']) && empty($params['filter_alias']) && empty($params['price']) )
            {
                $url[0] = ['for' => 'get_items_with_filters_ids_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'sort' => '0-'.$sort_default_2];
                $url[1] = ['for' => 'get_items_with_filters_ids_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'sort' => '1-'.$sort_default_2];
                $url[2] = ['for' => 'get_items_with_filters_ids_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'sort' => '2-'.$sort_default_2];
                $url[3] = ['for' => 'get_items_with_filters_ids_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'sort' => $sort_default_1.'-3'];
                $url[4] = ['for' => 'get_items_with_filters_ids_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'sort' => $sort_default_1.'-4'];
            }
            elseif( !empty($params['filter_ids']) && !empty($params['filter_alias']) && empty($params['price']) )
            {
                $url[0] = ['for' => 'get_items_with_filters_id_alias_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'sort' => '0-'.$sort_default_2];
                $url[1] = ['for' => 'get_items_with_filters_id_alias_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'sort' => '1-'.$sort_default_2];
                $url[2] = ['for' => 'get_items_with_filters_id_alias_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'sort' => '2-'.$sort_default_2];
                $url[3] = ['for' => 'get_items_with_filters_id_alias_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'sort' => $sort_default_1.'-3'];
                $url[4] = ['for' => 'get_items_with_filters_id_alias_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'sort' => $sort_default_1.'-4'];
            }
            elseif( !empty($params['filter_ids']) && empty($params['filter_alias']) && !empty($params['price']) )
            {
                $url[0] = ['for' => 'get_items_with_filters_id_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'price' => $params['price'], 'sort' => '0-'.$sort_default_2];
                $url[1] = ['for' => 'get_items_with_filters_id_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'price' => $params['price'], 'sort' => '1-'.$sort_default_2];
                $url[2] = ['for' => 'get_items_with_filters_id_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'price' => $params['price'], 'sort' => '2-'.$sort_default_2];
                $url[3] = ['for' => 'get_items_with_filters_id_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'price' => $params['price'], 'sort' => $sort_default_1.'-3'];
                $url[4] = ['for' => 'get_items_with_filters_id_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'price' => $params['price'], 'sort' => $sort_default_1.'-4'];
            }
            elseif( !empty($params['filter_ids']) && !empty($params['filter_alias']) && !empty($params['price']) )
            {
                $url[0] = ['for' => 'get_items_with_filters_id_alias_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'price' => $params['price'], 'sort' => '0-'.$sort_default_2];
                $url[1] = ['for' => 'get_items_with_filters_id_alias_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'price' => $params['price'], 'sort' => '1-'.$sort_default_2];
                $url[2] = ['for' => 'get_items_with_filters_id_alias_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'price' => $params['price'], 'sort' => '2-'.$sort_default_2];
                $url[3] = ['for' => 'get_items_with_filters_id_alias_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'price' => $params['price'], 'sort' => $sort_default_1.'-3'];
                $url[4] = ['for' => 'get_items_with_filters_id_alias_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'filter_ids' => $params['filter_ids'], 'filter_alias' => $params['filter_alias'], 'price' => $params['price'], 'sort' => $sort_default_1.'-4'];
            }
            elseif( empty($params['filter_ids']) && empty($params['filter_alias']) && !empty($params['price']) )
            {
                $url[0] = ['for' => 'get_items_with_filters_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'price' => $params['price'], 'sort' => '0-'.$sort_default_2];
                $url[1] = ['for' => 'get_items_with_filters_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'price' => $params['price'], 'sort' => '1-'.$sort_default_2];
                $url[2] = ['for' => 'get_items_with_filters_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'price' => $params['price'], 'sort' => '2-'.$sort_default_2];
                $url[3] = ['for' => 'get_items_with_filters_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'price' => $params['price'], 'sort' => $sort_default_1.'-3'];
                $url[4] = ['for' => 'get_items_with_filters_price_sorted', 'type' => $params['type'], 'subtype' => $params['subtype'], 'price' => $params['price'], 'sort' => $sort_default_1.'-4'];
            }

            return $url;
        }

        /////////////////////////////////////////////////////////////////////////////
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
