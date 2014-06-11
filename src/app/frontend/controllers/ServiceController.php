<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace controllers;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class ServiceController extends \Phalcon\Mvc\Controller
{
    ///////////////////////////////////////////////////////////////////////////

    public function typesAction()
    {
        $rows = [];
        $all_rows = [];
        $file = fopen(ROOT_PATH.'data/SEMENA_1_gazonni travy.csv', 'r');

        $lang_id = '1';

        //p( $file,1 );
        if ($file)
        {
            while( ($buffer = fgetcsv( $file, 100000, ',' ) ) !== false)
            {
                $all_rows[] = array_filter($buffer);
            }
        }
        fclose($file);

        // get all filters_keys id => title
        $termins = $all_rows['2'];

        //p($all_rows,1);

        foreach( $all_rows as $r )
        {
            if( !empty( $r ) && !empty( $r['1'] ) )
            {
                $veg[ $r['1'] ][] = $r;
            }
        }

        // get all kavuns
        //$kavun = $veg['Кавун'];
        $kavun = $all_rows;

        $type = 4;
        $subtype = 1;
        $lang_id = 1;


        // get filters_key_id_from
        $filters_key_id = array_unique($all_rows['0']);
        foreach( $filters_key_id as $k => $v )
        {
            if( $v == 'Характеристики' )
            {
                $char_key_id_from = $k;
            }
            if( $v == 'Фільтри для розділу:' )
            {
                $filters_key_id_from = $k;
            }
        }

        unset($kavun['0']);
        unset($kavun['1']);
        unset($kavun['2']);

        foreach( $kavun as $key => $value )
        {
            foreach( $value as $k => $v )
            {
                $filter_values_[$k][] = trim($v);
                $filter_values_[$k] = array_unique($filter_values_[$k]);
            }
        }

        //p($filter_values_);

        // get filters_values for one group && get filters_keys for one group
        foreach( $filter_values_ as $k => $v )
        {
            if( $k >= $filters_key_id_from )
            {
                //$filter_values_for_group[trim($termins[$k])] = array_unique($v);

                $val[$termins[$k]] = array_unique($v);

                foreach( $val[$termins[$k]] as $key => $val_ )
                {
                    $key_values[$termins[$k]][] =
                        [
                            'value_value' => $val_,
                            'value_alias' => $this->common->transliterate( trim($val_), $lang_id ),
                        ];
                }

                $filter_values_for_group[] =
                    [
                        'key_value' => trim($termins[$k]),
                        'key_alias' => $this->common->transliterate( trim($termins[$k]), $lang_id ),
                        'key_values' => $key_values[$termins[$k]]
                    ];
            }
            if( $k < $filters_key_id_from && $k >= $char_key_id_from )
            {
                $char_values_for_group[trim($termins[$k])] = array_unique($v);
            }
        }

        //p($char_values_for_group);
        //p($filter_values_for_group,1);

        //p($this->models->getFilters()->addFilters( $filter_values_for_group, $type, $subtype, $lang_id ));

        //p($this->models->getFilters()->addProperties( $char_values_for_group, $type, $subtype, $lang_id ));

        // get all filters_keys id => title
        $termins = $all_rows['2'];



        unset($all_rows['0']);
        unset($all_rows['1']);
        unset($all_rows['2']);

        foreach( $all_rows as $r )
        {
            if( !empty( $r ) && !empty( $r['1'] ) )
            {
                $veg[ $r['1'] ][] = $r;
            }
        }


        $filters_for_group  = $this->models->getFilters()->getFilters( $lang_id, $type, $subtype );

        $char_for_group     = $this->models->getFilters()->getChar( $lang_id, $type, $subtype );
        //p($char_for_group,1);

        //p($kavun);

        foreach( $kavun as $key => $value )
        {
            for ($i = 1; $i <= 6; $i++)
            {
                if( file_exists( STORAGE_PATH.'foto gazonni travy/'.$value['3'].'/'.$i.'.jpg' ) )
                {
                    $md5_files[$value['3']][] = md5_file(STORAGE_PATH.'foto gazonni travy/'.$value['3'].'/'.$i.'.jpg');

                    $md5_files_temp[STORAGE_PATH.'foto gazonni travy/'.$value['3'].'/'.$i.'.jpg'] = md5_file(STORAGE_PATH.'foto gazonni travy/'.$value['3'].'/'.$i.'.jpg');
                }
            }

            $md5_files[$value['3']]     = array_filter($md5_files[$value['3']]);
            $md5_files[$value['3']]     = array_unique($md5_files[$value['3']]);
            $md5_files_[$value['3']]    = $this->etc->arr2int($md5_files[$value['3']]);

            foreach( $value as $k => $v )
            {
                $filter_values_[$k][] = trim($v);

                if( $k >= $filters_key_id_from )
                {
                    foreach( $filters_for_group as $f )
                    {
                        if( $f['filter_key_value'] == trim($termins[$k]) && $f['filter_value_value'] == trim($v) )
                        {
                            $filters[$key][] = $f['id'];
                        }
                    }

                    $filters_[$key][trim($termins[$k])] = trim($v);
                }


                if( $k < $filters_key_id_from && $k >= $char_key_id_from )
                {
                    foreach( $char_for_group as $f )
                    {
                        if( $f['property_key_value'] == trim($termins[$k]) && $f['property_value_value'] == trim($v) )
                        {
                            $char[$key][] = $f['id'];
                        }
                    }

                    $char_[$key][trim($termins[$k])] = trim($v);
                }
            }


            $items[$value['2']][] =
                [
                    'size'                  => $value['6'],
                    'price1'                => isset( $value['7'] ) && !empty( $value['7'] ) ? str_replace( ',', '.', $value['7'] ) : '5',
                    'price2'                => isset( $value['7'] ) && !empty( $value['7'] ) ? str_replace( ',', '.', $value['7'] ) : '5',
                    'status'                => 1,
                    'type'                  => $type,
                    'subtype'               => $subtype,
                    'lang_id'               => '1',
                    'product_id'            => NULL,
                    'color'                 => NULL,
                    'filters'               => $filters[$key],
                    'char'                  => $char[$key],
                    //'filters_'              => $filters_[$key],
                ];

            $kavun_group[$value['2']] =
                [
                    'subtype_title'         => isset( $value['1'] ) && !empty( $value['1'] ) ? $value['1'] : '',
                    'group_title'           => $value['2'],
                    'group_alias'           => $this->common->transliterate( $value['2'] ),
                    'description'           => isset( $value['4'] ) && !empty( $value['4'] ) ? $value['4'] : NULL,
                    'content_description'   => isset( $value['5'] ) && !empty( $value['5'] ) ? $value['5'] : NULL,
                    'content_video'         => NULL,
                    'cover'                 => isset( $value['3'] ) && !empty( $value['3'] ) ? md5_file(STORAGE_PATH.'foto gazonni travy/'.'/'.$value['3'].'/1.jpg') : NULL,
                    'photogallery'          => isset( $md5_files_[$value['3']] ) && !empty( $md5_files_[$value['3']] ) ? $md5_files_[$value['3']] : NULL,
                    'status'                => 1,
                    'type'                  => $type,
                    'subtype'               => $subtype,
                    'lang_id'               => '1',
                    'items'                 => $items[$value['2']],
                    //'char_'                 => $char_[$key],
                ];

        }

        sort($kavun_group);

        $md5_files_temp = array_unique( $md5_files_temp );
        $md5_files_temp = array_filter( $md5_files_temp );

        /*
        foreach( $md5_files_temp as $path => $md5_file )
        {
            $this->storage->mkdir( 'group', $md5_file );

            $image_path = $this->storage->getPhotoPath( 'group', $md5_file );

            copy( $path, $image_path );
        }
        */
        p($kavun_group,1);

        //p($md5_files_temp,1);


        foreach( $kavun_group as $kavun )
        {
            p($this->models->getFilters()->addItems( $kavun ) );
        }


        p($kavun_group,1);
        /*
        // get filters_values for one group && get filters_keys for one group
        foreach( $filter_values_ as $k => $v )
        {
            if( $k >= $filters_key_id_from )
            {
                $filter_values_for_group[trim($termins[$k])] = array_unique($v);
            }
            if( $k < $filters_key_id_from && $k >= $char_key_id_from )
            {
                $char_values_for_group[trim($termins[$k])] = array_unique($v);
            }
        }
        */

        //p($char_values_for_group);
        //p($filter_values_for_group);

        //p($this->models->getFilters()->addFilters( $filter_values_for_group, $char_values_for_group, 1, 1 ));
        //p($filter_values_);




        $termins = array_unique($termins);
        //p($termins);
        //p($all_rows);

        /*
         *
        // form to config

        foreach( $all_rows as $row )
        {
            if( !empty( $row['1'] ) )
            {
                $veg[$row['1']][] = $row;

                $veg_catalog[] = trim($row['1']);
            }
        }



        $veg_catalog = (array_unique( $veg_catalog ));

        $i = 0;

        foreach( $veg_catalog as $v )
        {
            $i++;
            $veg_catalog_[$i] =
                [
                    'title' => $v,
                    'alias' => $this->common->transliterate($v)
                ];
        }

        var_export($veg_catalog_);

        p($veg_catalog_);

        */



        p('hello',1);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function imagesAction()
    {
        //$file = '/images/cat/1.jpg';

        $path = STORAGE_PATH.'temp/111.jpg';
        $image_path = $this->storage->getPhotoPath( 'avatar', '38550b4ad85b2ad8314b888458eb6bfe', '11' );
        //$this->storage->mkdir( 'avatar', md5_file($path) );
        p($path);

        //copy( $path, $image_path );

        p($this->storage->imageResizeWithCrop( [], '38550b4ad85b2ad8314b888458eb6bfe', 'avatar' ));

        //p(md5_file($path),1);

        /*

        for ($i = 1; $i <= 7; $i++)
        {
            $path = STORAGE_PATH.'temp/'.$i.'.jpg';

            $md5_file = md5_file($path);

            //$this->storage->mkdir( 'news', $md5_file );

            //$image_path = $this->storage->getPhotoPath( 'news', $md5_file, 'original' );

            //copy( $path, $image_path );

            //p($this->storage->imageResizeWithCrop( [], $md5_file, 'news' ));

            p( $i.' - '.$md5_file );
        }

        */

        p( md5_file($path),1 );
    }

    //////////////////////////////////////////////////////////////////////////

    public function storageAction()
    {
        $images = $this->models->getFilters()->getImages();

        $subtypes = $this->common->array_column($images, 'cover');
        $subtypes = array_filter( $subtypes );
        $subtypes = array_unique( $subtypes );

        foreach( $subtypes as $s )
        {
            $path = $this->storage->getPhotoPath( 'subtype', $s, 'subtype' );
            $image_path = $this->storage->getPhotoPath( 'subtype', $s, '165x120' );
            //copy($path,$image_path);
            p($path);
        }

        p($subtypes);

        /*
        $avatars = $this->models->getFilters()->getAvatars();

        foreach( $avatars as $a )
        {
            $md5_file = $a['cover'];

            if( !empty( $a['cover'] ) )
            {
                //$this->storage->mkdir( 'avatar', $a['cover'] );
                //$path       = $this->storage->getPhotoPath( 'group', $a['cover']);
                //$image_path = $this->storage->getPhotoPath( 'avatar', $a['cover'] );
                //copy( $path, $image_path );

                //p($this->storage->imageResizeWithCrop( [], $md5_file, 'avatar' ));
            }
        }

        //p($avatars,1);

        foreach( $images as $i )
        {
            $images_[] = !empty($i['photogallery']) ? $this->etc->int2arr($i['photogallery']) : '';
        }

        $images_ = array_filter( $images_ );

        foreach( $images_ as $i )
        {
            foreach( $i as $v )
            {
                $images_arr[] = $v;
            }
        }

        $images_arr = array_unique($images_arr);

        foreach( $images_arr as $md5_file )
        {
            //$image_path = $this->storage->getPhotoPath( 'group', $md5_file, '128x' );
            //$image_path_ = $this->storage->getPhotoPath( 'group', $md5_file, '400x' );

            //p($this->storage->imageResizeWithCrop( [], $md5_file ));
        }

        //p( $this->storage->imageResizeWithCrop( [], $images_arr['1'] ) );

        */

        p($images,1);
    }

    //////////////////////////////////////////////////////////////////////////

    public function catalogAction()
    {
        $catalog = \config::get('global#types/ua');

        //p($this->models->getFilters()->addCatalog( $catalog, 1 ));

        p($catalog,1);
    }

    //////////////////////////////////////////////////////////////////////////

    public function citiesAction()
    {
        $curl = curl_init();

        $postvalues = array(
            'getpage=yes',
            'lang=en',
        );

        $header = array(
            'Origin: http://myip.ms',
            'X-Requested-With: XMLHttpRequest',
        );

        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_URL, 'http://semena.in.ua/content/contact/' );
        #curl_setopt($curl, CURLOPT_URL, 'http://myip.ms/browse/cities/IP_Addresses_Cities.html' );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, join( '&', $postvalues ) );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_REFERER, 'http://semena.in.ua/content/contact/' );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header );
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/30.0.1599.114 Chrome/30.0.1599.114 Safari/537.36' );

        curl_setopt($curl, CURLOPT_COOKIEFILE, '/home/jane/www/two/cities/cookiefile.txt' );
        curl_setopt($curl, CURLOPT_COOKIEJAR, '/home/jane/www/two/cities/cookiefile.txt' );

        $data = curl_exec($curl);

        //$reg = '#<tr>\s*<td>\s*[0-9]*<\/td>\s*<td>(?P<name>[^<]*)<\/td>\s*<td>(?P<district>[^<]*)<\/td>\s*<td>(?P<city>[^<]*)<\/td>\s*<td>(?P<phone>[^<]*)<\/td>\s*#ims';
        $reg = '#<tr>\s*<td[^>]*>\s*(?P<address>[^<]*)\s*[^t]*td>\s*<td[^>]*>(?P<tel>[0-9\-]*)\s*[^t]*td>\s*#ims';

        if( preg_match_all( $reg, $data, $matches ) )
        {
            $shop =
                [
                    //'name' => $matches['name'],
                    //'district' => $matches['district'],
                    //'city' => $matches['city'],
                    //'phone' => $matches['phone'],
                    'address' => $matches['address'],
                    'phone' => $matches['tel'],
                ];
        }

        //p($shop,1);

        /*
        $districts = $this->models->getFilters()->getDistrict(  );

        foreach( $shop as $s )
        {
            foreach( $s as $k => $v )
            {
                foreach( $districts as $d )
                {
                    if( strtolower(trim($d['title'],'обл.'))  == strtolower(trim($shop['district'][$k], 'обл.')) )
                    $shop_[$k] =
                        [
                            'title' => $shop['name'][$k],
                            'district' => $d['id'],
                            'city' => $shop['city'][$k],
                            'phone' => $shop['phone'][$k],
                        ];
                }
            }
        }

        */

        foreach( $shop['address'] as $k => &$s )
        {
            $s = trim($s);
            $shop['address_'][$k] = trim(mb_substr($s, 13));
            $shop['district'][$k] = 0;

            $shop_[$k] =
            [
                'phone' =>   $shop['phone'][$k],
                //'address_' =>   $shop['address_'][$k],
                'address_' =>   iconv(mb_detect_encoding($shop['address_'][$k]), "UTF-8", $shop['address_'][$k]),
                'district' =>   $shop['district'][$k]
            ];
        }





        //p($this->models->getFilters()->addShop( $shop_ ),1);

        //p($districts);
        p($shop_);

        p('hello',1);
    }

    //////////////////////////////////////////////////////////////////////////

    public function poshtaAction()
    {
        //$streets = $this->novaposhta->track('20290003413855');

        //p($streets,1);

        //$cities_    = $this->novaposhta->city();

        //p($cities_,1);

        $cities = $this->novaposhta->warenhouse('Полтава');

        //p($cities,1);

        foreach( $cities->warenhouse as $c )
        {
            p($c);
            //p( strval($c->address) );
        }


        die();

        #p( $this->novaposhta->city(), 1 );
        p( $this->novaposhta->price( 'Полтава', 10, 20 ), 1 );

        //p('hello',1);
    }

    //////////////////////////////////////////////////////////////////////////

    public function typeSubtypeAction()
    {
        $lang_id = 1;
        $types      = $this->models->getCatalog()->getTypes( $lang_id );
        $subtypes   = $this->models->getCatalog()->getSubtypes( $lang_id );

        foreach( $subtypes as $s )
        {
            $subtypes_with_types[$s['type']][] = $s;
        }

        //p($subtypes_with_types,1);

        foreach( $types as &$t )
        {
            $t['subtypes'] = $subtypes_with_types[$t['type']];
            $types_with_children[$t['type']] = $t;
            //$t['type_children'] = ($t['parent_id'] == $t['type']) ? $t : [];

            $types_with_children[$t['type']]['subtypes'] = $subtypes_with_types[$t['type']];

            if( $t['parent_id'] > 0 )
            {
                $types_with_children[$t['parent_id']]['type_children'][] = $t;

                unset($types_with_children[$t['type']]);
            }


        }
        p($types_with_children,1);

    }

    //////////////////////////////////////////////////////////////////////////

}
