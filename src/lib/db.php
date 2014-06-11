<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace 
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * db Class
     *
     * @author          Roman Telychko
     * @version         1.0.20130926
     */
    class db extends \core
    { 
        ///////////////////////////////////////////////////////////////////////

        protected       $user_id                = 0;

        protected       $database               = false;
        protected       $cache                  = false;

        protected       $exec_statistics        = array();

        ///////////////////////////////////////////////////////////////////////

        /**
         * db::__construct()
         *
         * @author          Roman Telychko
         * @version         1.0.20131021
         */
        public function __construct()
        {
            $this->database     = \Phalcon\DI::getDefault()->get('database');
            $this->cache        = \Phalcon\DI::getDefault()->get('cache');
            $this->user_id      = \Phalcon\DI::getDefault()->get('session')->get('id',0 );

            if( empty($this->user_id) )
            {
                $this->user_id  = 0;
            }
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * db::get()
         *
         * @author          Roman Telychko
         * @version         1.0.20130926
         *
         * @param           string          $sql
         * @param           array           $data
         * @param           integer         $lifetime
         * @param           string          $cache_key      [sql_<user_id>_<md5_sql>_<md5_sql_data> OR user_favourites_<user_id>, user_mail_<user_id>]
         * @return          array
         */
        public function get( $sql, $data = [], $lifetime = 60, $cache_key = null )
        {
            if( empty($cache_key) )
            {
                // set cache key if not defined
                $cache_key = 'sql_'.$this->user_id;
            }

            // append to cache key
            $cache_key .= '_'.md5($sql).'_'.md5( base64_encode( serialize($data) ) );

            $result = [];
            
            if( $lifetime>0 )
            {
                $result = $this->cache->get( $cache_key );
            }
        
            if( empty($result) )
            {
                $start_time = microtime(true);

                $dbresult = $this->database->query( $sql, $data );   
                $dbresult->setFetchMode( Phalcon\Db::FETCH_ASSOC );

                $result = [];

                while( $dbrow = $dbresult->fetch() )
                {
                    $result[] = $dbrow;
                }
                
                if( $lifetime>0 )
                {
                    $this->cache->save( $cache_key, $result, $lifetime );
                }

                $this->getDi()->get('profiler')->setStatistics(
                    [
                        'type'      => 'sql',
                        'time'      => ( microtime(true) - $start_time ),
                        'sql'       => $sql,
                    ]
                );
            }
            
            return $result;
        }       

        ///////////////////////////////////////////////////////////////////////

        /**
         * db::exec()
         *
         * @author          Roman Telychko
         * @version         1.0.20130926
         *
         * @param           string          $sql
         * @param           array           $data
         * @return          bool
         */
        public function exec( $sql, $data = [] )
        {
            $start_time = microtime(true);

            $return = $this->database->execute( $sql, $data );

            $this->getDi()->get('profiler')->setStatistics(
                [
                    'type'      => 'sql',
                    'time'      => ( microtime(true) - $start_time ),
                    'sql'       => $sql,
                ]
            );

            return $return;
        }
        
        ///////////////////////////////////////////////////////////////////////

        /**
         * db::dropCache()
         *
         * @author          Roman Telychko
         * @version         1.0.20130927
         *
         * @param           string          $pattern
         * @return          bool
         */
        public function dropCache( $pattern = null )
        {
            if( empty($pattern) )
            {
                $pattern = 'sql_'.$this->user_id.'_';
            }

            $keys = $this->cache->queryKeys( $pattern );
            
            if( !empty($keys) )
            {
                foreach( $keys as $key )
                {
                    $this->cache->delete( $key );
                }
            }
            
            return true;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * db::showException()
         *
         * @author          Roman Telychko
         * @version         1.0.20140120
         *
         * @param           object          $e
         * @return          bool
         */
        public function showException( $e )
        {
            if( IS_PRODUCTION )
            {
                // TODO
            }
            else
            {
                echo( ob_get_flush() );

                if( class_exists('exceptions') )
                {
                    $z = new \exceptions();
                    $z->handle($e);
                }
                else
                {
                    p( $e->getMessage(), 9 );
                }
            }

            die();
        }

        ///////////////////////////////////////////////////////////////////////
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
