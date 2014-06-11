<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * etc class
     *
     * @author      Roman Telychko
     * @version     2.0.20100809
     */
    class etc
    {
        const       HSTORE_ARRAY_STRING = 'serialized::';

        ///////////////////////////////////////////////////////////////////////////

        /**
         * etc::int2arr()
         *
         * @author         Roman Telychko
         * @version        2.2.20111012
         *
         * @param          string     $int
         * @param          bool       $return_unique
         * @return         array
         */
        public function int2arr( $int, $return_unique = true )
        {
            if( is_array($int) )
            {
                return $int;
            }
            else if( strlen($int)>2 )
            {
                if( $return_unique==true )
                {
                    return array_unique( explode( ',', preg_replace( '/[^0-9a-z\,]/', '', $int ) ) );
                }
                else
                {
                    return explode( ',', preg_replace( '/[^0-9a-z\,]/', '', $int ) );
                }
            }

            return [];
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * etc::arr2int()
         *
         * @author         Roman Telychko
         * @version        2.1.20100923
         *
         * @param          array     $arr
         * @return         string
         */
        public function arr2int( $arr )
        {
            if( !is_array($arr) )
            {
                return false;
            }

            if( empty($arr) )
            {
                return '{}';
            }
            else
            {
                $arr_temp = [];

                foreach( $arr as &$a )
                {
                    if( strlen($a)>0 )
                    {
                        $arr_temp[] = $a;
                    }
                }

                return '{'.join( ',', $arr_temp ).'}';
            }
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * etc::hstore2arr()
         *
         * @author         Roman Telychko
         * @version        2.1.20110110
         *
         * @param          string     $hstore
         * @return         array      $arr
         */
        public function hstore2arr( $hstore )
        {
            if( is_array($hstore) )
            {
                return $hstore;
            }

            $hstore = trim($hstore);

            if( strlen($hstore)<=0 )
            {
                return [];
            }

            $hstore_array_string_length = strlen(self::HSTORE_ARRAY_STRING);
            $arr = [];

            $arr_temp = explode('", "', substr( $hstore, 1, -1 ));

            if( !empty($arr_temp) )
            {
                foreach( $arr_temp as &$d )
                {
                    if( strlen($d)>1 )
                    {
                        $temp = explode('"=>"', $d);

                        if( !empty($temp) && isset($temp['0']) && isset($temp['1']) )
                        {
                            if( substr( $temp['1'], 0, $hstore_array_string_length ) == self::HSTORE_ARRAY_STRING )
                            {
                                $temp2 = unserialize( substr( stripcslashes($temp['1']), 12 ) );
                                $arr[ stripcslashes($temp['0']) ] = ( is_array($temp2) ) ? $temp2 : false;
                            }
                            else
                            {
                                $arr[ stripcslashes($temp['0']) ] = stripcslashes($temp['1']);
                            }
                        }
                    }
                }
            }

            return $arr;
        }

        ///////////////////////////////////////////////////////////////////////////    

        /**
         * etc::isHstore[]
         *
         * @author         Roman Telychko
         * @version        2.0.20100809
         *
         * @param          string       $hstore
         * @return         array|bool   $arr
         */
        public function isHstoreArray( $hstore )
        {
            $hstore = trim($hstore);

            if( strlen($hstore)<=0 )
            {
                return false;
            }

            $hstore_array_string_length = strlen(self::HSTORE_ARRAY_STRING);
            $arr = [];

            $arr_temp = explode('", "', substr( $hstore, 1, -1 ));

            if( !empty($arr_temp) )
            {
                foreach( $arr_temp as &$d )
                {
                    if( strlen($d)>1 )
                    {
                        $temp = explode('"=>"', $d);

                        if( !empty($temp) && isset($temp['0']) && isset($temp['1']) )
                        {
                            if( substr( $temp['1'], 0, $hstore_array_string_length ) == self::HSTORE_ARRAY_STRING )
                            {
                                $arr[ stripcslashes($temp['0']) ] = 1;
                            }
                        }
                    }
                }
            }

            return ( empty($arr) ) ? false : $arr;
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * etc::arr2hstore()
         *
         * @author         Roman Telychko
         * @version        2.2.20110506
         *
         * @param          array       $arr
         * @param          string      $replace_str_key
         * @param          boolean     $remove_empty_value
         * @return         string
         */
        public function arr2hstore( $arr, $replace_str_key = '', $remove_empty_value = false )
        {
            if( !is_array($arr) )
            {
                return false;
            }

            if( empty($arr) )
            {
                return '';
            }

            $hstore_parts = [];

            foreach( $arr as $k => &$v )
            {
                if( is_array($v) )
                {
                    $hstore_parts[] = '"'.addcslashes( str_replace($replace_str_key,'',$k), '"' ).'"=>"'.self::HSTORE_ARRAY_STRING.addcslashes( addcslashes( serialize($v), '"' ), chr(92) ).'"';
                }
                else
                {
                    if( $remove_empty_value===true )
                    {
                        if( empty($v) )
                        {
                            //unset( $arr[$k] );
                            continue;
                        }
                    }

                    $hstore_parts[] = '"'.addcslashes( str_replace($replace_str_key,'',$k), '"' ).'"=>"'.addcslashes( $v, '"' ).'"';
                }
            }

            if( !empty($hstore_parts) )
            {
                return join( ', ', $hstore_parts );
            }

            return '';
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * etc::json2hstore()
         *
         * @author         Dimonic
         * @version        1.0.20100806
         *
         * @param          string       $json
         * @return         string
         */
        public function json2hstore( $json )
        {
            if( empty($json) )
            {
                return '';
            }

            $json = json_decode($json);
            $json_array = [];
            foreach( $json as &$val )
            {
                foreach( get_object_vars($val) as $check_val )
                {
                    if( strlen(trim($check_val))>0 )
                    {
                        $json_array[] = get_object_vars($val);
                        break;
                    }
                }
            }

            return self::arr2hstore($json_array);
        }

        ///////////////////////////////////////////////////////////////////////////

        private static $text2arr_indexer = 0;

        /**
         * etc::text2arr()
         *
         * @author         Roman Telychko
         * @version        1.0.20120808
         *
         * @param          string       $text
         * @param          bool         $reset_indexer
         * @return         array
         */
        public function text2arr( $text, $reset_indexer = true )
        {
            #static $i = 0;

            if ($reset_indexer)
            {
                self::$text2arr_indexer = 0;
            }

            $matches = [];
            $indexer = 1;   // by default sql arrays start at 1

            // handle [0,2]= cases
            if (preg_match('/^\[(?P<index_start>\d+):(?P<index_end>\d+)]=/', substr($text, self::$text2arr_indexer), $matches))
            {
                $indexer = (int)$matches['index_start'];
                self::$text2arr_indexer = strpos($text, '{');
            }

            if ($text[self::$text2arr_indexer] != '{')
            {
                return NULL;
            }

            self::$text2arr_indexer++;
            $work = [];
            $curr = '';
            $length = strlen($text);
            $count = 0;

            while (self::$text2arr_indexer < $length)
            {
                switch ($text[self::$text2arr_indexer])
                {
                    case '{':
                        $sub = self::text2arr($text, false);
                        if(!empty($sub))
                        {
                            $work[$indexer++] = $sub;
                        }
                        break;

                    case '}':
                        self::$text2arr_indexer++;
                        if (!empty($curr))
                        {
                            $work[$indexer++] = stripcslashes( $curr );
                        }
                        return $work;
                        break;

                    case '\\':
                        self::$text2arr_indexer++;
                        $curr .= $text[self::$text2arr_indexer];
                        self::$text2arr_indexer++;
                        break;

                    case '"':
                        $openq = self::$text2arr_indexer;
                        do
                        {
                            $closeq = strpos($text, '"' , self::$text2arr_indexer + 1);
                            if ($closeq > $openq && $text[$closeq - 1] == '\\')
                            {
                                self::$text2arr_indexer = $closeq + 1;
                            }
                            else
                            {
                                break;
                            }
                        }
                        while(true);

                        if ($closeq <= $openq)
                        {
                            die();
                        }

                        $curr .= substr($text, $openq + 1, $closeq - ($openq + 1));

                        self::$text2arr_indexer = $closeq + 1;
                        break;

                    case ',':
                        if (!empty($curr))
                        {
                            $work[$indexer++] = $curr;
                        }
                        $curr = '';
                        self::$text2arr_indexer++;
                        break;

                    default:
                        $curr .= $text[self::$text2arr_indexer];
                        self::$text2arr_indexer++;
                }
            }
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * etc::arr2text()
         *
         * @author         Roman Telychko
         * @version        1.0.20120808
         *
         * @param          array        $arr
         * @return         string
         */
        public function arr2text( $arr )
        {
            $text = '{';

            if( !empty($arr) )
            {
                $i = 0;
                $arr_count = count($arr);

                foreach( $arr as $item )
                {
                    if( is_array($item) )
                    {
                        $text .= self::arr2text( $item );
                    }
                    else
                    {
                        $text .= '"'.addcslashes( $item, '"' ).'"';
                    }

                    $i++;

                    if( $i < $arr_count )
                    {
                        $text .= ',';
                    }
                }
            }

            $text .= '}';

            return $text;
        }

        ///////////////////////////////////////////////////////////////////////////
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
