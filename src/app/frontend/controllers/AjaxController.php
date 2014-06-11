<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace controllers;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class AjaxController extends \Phalcon\Mvc\Controller
{
    ///////////////////////////////////////////////////////////////////////////

    public function getItemsAction( $lang_id = '1' )
    {
        header('Content-Type: application/json; charset=utf8');

        $term       = $this->request->getPost('term', 'string', '' );

        $items_     = $this->models->getItems()->getItemsByTerm( $term, 'items_dropdown' );

        $items      = [];

        if( !empty( $items_ ) )
        {
            $items_ids  = $this->common->array_column( $items_, 'item_id' );
            $items      = $this->models->getItems()->getItemsByIds( $lang_id, $items_ids );

            foreach( $items as &$i )
            {
                $i['cover']         = !empty( $i['group_cover'] ) ? $this->storage->getPhotoUrl( $i['group_cover'], 'avatar', '200x' ) : '/images/packet.jpg';
                $i['alias']         = $this->url->get([ 'for' => 'item', 'type' => $i['type_alias'], 'subtype' => $i['subtype_alias'], 'group_alias' => $i['group_alias'], 'item_id' => $i['id'] ]);
            }
        }

        die( json_encode( $items ) );
    }

    ///////////////////////////////////////////////////////////////////////////

    public function addItemsForCompareAction( $lang_id = '1' )
    {
        header('Content-Type: application/json; charset=utf8');

        $count = 0;

        if( $this->request->isAjax() && $this->request->isPost() )
        {
            $item_properties    = $this->request->getPost( 'item_id', 'string', '' );
            $item_properties    = explode( '-', $item_properties );
            $check              = $this->request->getPost( 'check', 'int', '' );

            $type_id            = $item_properties['0'];
            $subtype_id         = $item_properties['1'];
            $item_id            = $item_properties['2'];

            $compare            = $this->session->get('compare', []);

            if( !isset($compare[$type_id][$subtype_id]) || ( isset($compare[$type_id][$subtype_id]) && !in_array( $item_id, $compare[$type_id][$subtype_id] ) ) )
            {
                $compare[$type_id][$subtype_id][] = $item_id;
            }
            elseif( isset($compare[$type_id][$subtype_id]) && in_array( $item_id, $compare[$type_id][$subtype_id] ) )
            {
                foreach( $compare[$type_id][$subtype_id] as $k => $v )
                {
                    if( $v == $item_id )
                    {
                        unset( $compare[$type_id][$subtype_id][$k] );

                        if( empty( $compare[$type_id][$subtype_id] ) )
                        {
                            unset($compare[$type_id][$subtype_id]);
                        }
                        if( empty( $compare[$type_id] ) )
                        {
                            unset($compare[$type_id]);
                        }
                    }
                }
            }

            $count = 0;
            $compare_ = [];

            if( !empty( $compare ) )
            {
                $catalog_ = $this->common->getTypeSubtype( NULL, NULL, NULL, $lang_id );

                foreach( $compare as $key => $comp )
                {
                    $type_ids[] = $key;

                    foreach( $comp as $k => $c )
                    {
                        $subtype_ids[] = $k;

                        $count += count($c);
                    }
                }

                foreach( $compare as $key => $comp )
                {
                    foreach( $comp as $k => $c )
                    {
                        //$compare_[$key][$subtype_title[$key][$k]] = $c;
                        $compare_[$key][$k] =
                            [
                                'title' => $catalog_[$key]['subtypes'][$k]['title'],
                                'count' => count($c),
                                'items' => $c,
                                'url'   => $this->url->get([ 'for' => 'compare_items', 'type' => $catalog_[$key]['alias'], 'subtype' => $catalog_[$key]['subtypes'][$k]['alias'], 'compare_ids' => join('-', $c) ])
                            ];
                    }
                }
            }


            $this->session->set( 'compare', $compare );
            //$this->session->set( 'compare', [] );
        }

        die( json_encode( $compare_ ) );
    }

    ///////////////////////////////////////////////////////////////////////////
}
 