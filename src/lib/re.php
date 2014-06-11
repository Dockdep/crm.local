<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace 
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
    /**
     * redis Class
     *
     * @author          Roman Telychko
     * @version         4.0.20130226
     */
    class re
    {
        ///////////////////////////////////////////////////////////////////////////

        private             $instance               = [];    
        private             $dbindex                = [ 0, 0, 0, 0 ];
        private             $redis                  = false;
        private             $server_ip              = false;
        private             $server_port            = false;
        private             $server_timeout         = 10;
        
        ///////////////////////////////////////////////////////////////////////////
        
        protected           $log                    = true;
        public              $exec_time              = [
                                                        'Connect to Redis' => 0,
                                                      ];
        public              $exec_count             = [];
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::__construct()
         *
         * @author          Roman Telychko
         * @version         2.4.20110419
         *
         */
        public function __construct() 
        {
            if( ! extension_loaded('Redis') )
            {        
                throw new \Phalcon\Exception('Extension Redis not loaded.');
            }
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::init()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @throws          Phalcon\Exception
         *
         * @param           integer       $server_index
         * @return          object
         */
        protected function init( $server_index = 0 )
        {
            if( !isset($this->instance[$server_index]) || $this->instance[$server_index]==false )
            {
                if( $this->log===true )
                {
                    $start_time = microtime(true); 
                }
            
                $obj = new self();            
                $obj->redis = new Redis();
                
                switch( $server_index )
                {
                    case 0:
                    default:
                        $obj->server_ip   = '127.0.0.1';
                        $obj->server_port = 6379;
                        break;
                }

                // connect
                try 
                {
                    $obj->redis->connect( $obj->server_ip, $obj->server_port, $obj->server_timeout );
                }
                catch( RedisException $e ) 
                {
                    throw new \Phalcon\Exception( 'Cannot connect to redis server: '.$e->getMessage() );
                }
                
                /*
                // authentificate
                if( strlen($this->passwd)>0 )
                {
                    $obj->redis->auth( $this->passwd );  
                }
                */
                	
                $this->instance[$server_index] = $obj;
                
                if( $this->log===true )
                {
                    $this->exec_time['Connect to Redis'] = microtime(true)-$start_time;
                }
            }
            
            return $this->instance[$server_index];
        }    

        ///////////////////////////////////////////////////////////////////////////
        
        /**
         * re::set()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           mixed         $data
         * @param           integer       $lifetime
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function set( $key, $data, $lifetime = 0, $dbindex = -1, $serverindex = 0 )
        {        
            if( strlen($key)<=0 )
            {
                return false;
            }

            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }        
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            // serialize array
            if( is_array($data) )
            {        
                $data = serialize( $data );
            }

            /*
            if( $this->init($serverindex)->redis->set( $key, $data ) )
            {
                if( $lifetime>0 )
                {
                    $this->init($serverindex)->redis->setTimeout( $key, $lifetime );
                }
            
                return true;
            }
            */
        
            if( $lifetime>0 )
            {
                if( $this->init($serverindex)->redis->setex( $key, $lifetime, $data ) )
                {            
                    if( $this->log===true )
                    {
                        if( isset($this->exec_time[ __FUNCTION__ ]) )
                        {
                            $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                            $this->exec_count[ __FUNCTION__ ]++;
                        }
                        else
                        {
                            $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                            $this->exec_count[ __FUNCTION__ ] = 1;
                        }
                    }
                
                    return true;
                }
            }
            else
            {
                if( $this->init($serverindex)->redis->set( $key, $data ) )
                {
                    if( $this->log===true )
                    {
                        if( isset($this->exec_time[ __FUNCTION__ ]) )
                        {
                            $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                            $this->exec_count[ __FUNCTION__ ]++;
                        }
                        else
                        {
                            $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                            $this->exec_count[ __FUNCTION__ ] = 1;
                        }
                    }
                
                    return true;
                }
            }
                
            return false;    
        }    
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::get()
         *
         * @author          Roman Telychko
         * @version         3.1.20111020
         *
         * @param           string        $key
         * @param           mixed         $default
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function get($key, $default = null, $dbindex = -1, $serverindex = 0)
        {
            if( strlen($key)<=0 )
            {
                return false;
            }    

            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }        

            $data = $this->init($serverindex)->redis->get( $key );

            if( $data==false && !empty($default) )
            {
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
            
                return $default;
            }     
            else
            {
                // is serialized array?
                if( !is_int($data) && strlen($data)>5 && substr($data, 0, 2)=='a:' )
                {
                    $temp = unserialize( $data );
                    
                    // is array?
                    if( is_array($temp) )
                    {
                        $data = $temp;
                    }
                }
            
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
            
                return $data;
            }       
        }    
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::mget()
         *
         * @author          Roman Telychko
         * @version         3.1.20111020
         *
         * @param           array         $keys
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          array
         */
        public function mget($keys, $dbindex = -1, $serverindex = 0)
        {
            if( empty($keys) )
            {
                return [];
            }  
        
            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }
        
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            } 
            
            #$keys = array_merge(array_unique($keys));
            $keys = array_unique($keys);        
     
            $data = [];                    
            $temp = $this->init($serverindex)->redis->getMultiple( $keys );
                                
            if( !empty($temp) )
            {
                foreach( $temp as $k => &$d )
                {
                    // is serialized array?
                    if( !is_int($d) && strlen($d)>5 && substr($d, 0, 2)=='a:' )
                    {
                        @$temp2 = unserialize( $d );
                        
                        // is array?
                        if( is_array($temp2) )
                        {
                            $d = $temp2;
                        }                
                    }      
                    
                    if( isset($keys[$k]) )
                    {                
                        $data[ $keys[$k] ] = $d;
                    }
                }            
            }
            
            if( $this->log===true )
            {
                if( isset($this->exec_time[ __FUNCTION__ ]) )
                {
                    $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                    $this->exec_count[ __FUNCTION__ ]++;
                }
                else
                {
                    $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                    $this->exec_count[ __FUNCTION__ ] = 1;
                }
            }

            return $data;    
        }        
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::has()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function has($key, $dbindex = -1, $serverindex = 0)
        {
            if( strlen($key)<=0 )
            {
                return false;
            }    
            
            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }  
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }        

            if( $this->init($serverindex)->redis->exists( $key ) )
            {
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }      
            
                return true;
            }

            return false;
        }    
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::exists()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function exists($key, $dbindex = -1, $serverindex = 0)
        {
            return self::has( $key, $dbindex, $serverindex );
        }     
        
        ///////////////////////////////////////////////////////////////////////////
        
        /**
         * re::delete()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */    
        public function delete( $key, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($key)<=0 )
            {
                return false;
            }    
            
            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }  
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }         

            if( $this->init($serverindex)->redis->delete( $key ) )
            {    
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
            
                return true;
            }
            
            return false;
        }    
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::mdelete()
         *
         * @author          Roman Telychko
         * @version         4.0.20111224
         *
         * @param           array         $keys
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          array
         */
        public function mdelete($keys, $dbindex = -1, $serverindex = 0)
        {
            if( empty($keys) )
            {
                return [];
            }  

            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }  
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }         

            if( $this->init($serverindex)->redis->delete( $keys ) )
            {    
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
            
                return true;
            }
            
            return false;
        }

        ///////////////////////////////////////////////////////////////////////////
        
        /**
         * re::remove()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */      
        public function remove( $key, $dbindex = -1, $serverindex = 0 )
        {
            return self::delete( $key, $dbindex, $serverindex );
        }     
        
        ///////////////////////////////////////////////////////////////////////////
        
        /**
         * re::rename()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key_old_name
         * @param           string        $key_new_name
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */      
        public function rename( $key_old_name, $key_new_name, $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->rename( $key_old_name, $key_new_name );
        } 
        
        ///////////////////////////////////////////////////////////////////////////    

        /**
         * re::keys()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           string        $pattern
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          array
         */
        public function keys( $pattern = '*', $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($pattern)<=0 )
            {
                return false;
            }
            
            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }  
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }         
        
            if( $data = $this->init($serverindex)->redis->getKeys( $pattern ) )
            {
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
            
                return $data;
            }
            
            return false;
        }      
        
        ///////////////////////////////////////////////////////////////////////////    

        /**
         * re::length()
         *
         * @author          Roman Telychko
         * @version         3.0.20120826
         * 
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          integer
         */
        public function length( $key, $dbindex = -1, $serverindex = 0 )
        { 
            if( strlen($key)<=0 )
            {
                return false;
            }    

            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }        

            return $this->init($serverindex)->redis->strlen( $key );
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::inc()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          integer
         */
        public function inc( $key, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($key)<=0 )
            {
                return false;
            }      
            
            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }  
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }        
            
            if( $data = $this->init($serverindex)->redis->incr( $key ) )
            {     
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
            
                return $data;
            }                 

            return false;
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::dec()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          integer
         */
        public function dec( $key, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($key)<=0 )
            {
                return false;
            }      
            
            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }  
        
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }    
        
            if( $data = $this->init($serverindex)->redis->decr( $key ) )
            {    
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
                
                return $data;
            }
            
            return false; 
        }     
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::select_db()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function select_db( $dbindex, $serverindex = 0 )
        {
            if( $this->dbindex[$serverindex]==$dbindex || $dbindex<0 || $dbindex>15 )
            {
                return false;
            }
            
            if( $this->log===true )
            {
                $start_time = microtime(true); 
            }  

            if( $this->init($serverindex)->redis->select( $dbindex ) )
            {
                $this->dbindex[$serverindex] = $dbindex;
            
                if( $this->log===true )
                {
                    if( isset($this->exec_time[ __FUNCTION__ ]) )
                    {
                        $this->exec_time[ __FUNCTION__ ] += microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ]++;
                    }
                    else
                    {
                        $this->exec_time[ __FUNCTION__ ] = microtime(true)-$start_time;
                        $this->exec_count[ __FUNCTION__ ] = 1;
                    }
                }
            
                return true;
            }
            
            return false;    
        }      
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::ttl()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          integer
         */
        public function ttl( $key, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($key)<=0 )
            {
                return false;
            }     
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }         
     
            if( $data = $this->init($serverindex)->redis->ttl( $key ) )
            {
                return $data;
            }
            
            return false;
        }     
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::expire()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         *
         * @param           string        $key
         * @param           integer       $lifetime
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function expire( $key, $lifetime = 0, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($key)<=0 || $lifetime<0 )
            {
                return false;
            }    
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }        
      
            if( $this->init($serverindex)->redis->setTimeout( $key, $lifetime ) )
            {
                return true;
            }

            return false;
        }     
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::save()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           bool          $background
         * @param           integer       $serverindex
         * @return          bool
         */
        public function save( $background = true, $serverindex = 0 )
        {
            if( $background==true )
            {
                if( $this->init($serverindex)->redis->bgSave() )
                {
                    return true;
                }
                return false;
            }
            else
            {
                if( $this->init($serverindex)->redis->save() )
                {
                    return true;
                }
                return false;            
            }
        }      
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::flush()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function flush( $dbindex, $serverindex = 0 )
        {
            if( $dbindex<0 || $dbindex>15 )
            {
                return false;
            }     

            $this->init($serverindex)->redis->select( $dbindex );
            #re::select_db( $dbindex, $serverindex );
            
            if( $this->init($serverindex)->redis->flushDb() )
            {
                return true;
            }
            
            return false;
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::info()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           integer       $serverindex
         * @return          array         $info
         */
        public function info($serverindex = 0)
        {
            if( $data = $this->init($serverindex)->redis->info() )
            {
                return $data;
            }
            
            return false;        
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::dbsize()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           integer|bool  $dbindex
         * @param           integer       $serverindex
         * @return          integer       $data
         */
        public function dbsize( $dbindex = false, $serverindex = 0 )
        {
            $this->init($serverindex)->redis->select( $dbindex );
            #re::select_db( $dbindex, $serverindex );
            
            if( $data = $this->init($serverindex)->redis->dbSize() )
            {
                return $data;
            }
            
            return false;        
        }   
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::getRandomKey()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           integer|bool  $dbindex
         * @param           integer       $serverindex
         * @return          string        $key
         */
        public function getRandomKey( $dbindex = false, $serverindex = 0 )
        {
            $this->init($serverindex)->redis->select( $dbindex );
            #re::select_db( $dbindex, $serverindex );
            
            if( $key = $this->init($serverindex)->redis->randomKey() )
            {
                return $key;
            }
            
            return false;        
        }      
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::sadd()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           string          $set
         * @param           array           $keys
         * @param           integer         $dbindex
         * @param           integer         $serverindex
         * @return          bool
         */
        public function sadd( $set, $keys, $dbindex = -1, $serverindex = 0 )
        {
            if( is_array($keys) )
            {
                if( empty($keys) )
                {
                    return false;
                }
            }
            else
            {
                $keys = [ $keys ];
            }
            
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }          
            
            foreach( $keys as &$key )
            {
                $obj->redis->sAdd( $set, $key );
            }
            
            return true;
        }   
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::sremove()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           string        $set
         * @param           array         $keys
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          bool
         */
        public function sremove( $set, $keys, $dbindex = -1, $serverindex = 0 )
        {
            if( is_array($keys) )
            {
                if( empty($keys) )
                {
                    return false;
                }
            }
            else
            {
                $keys = [ $keys ];
            }
            
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }        
            
            foreach( $keys as &$key )
            {
                $obj->redis->sRemove( $set, $key );
            }
            
            return true;
        }      
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::smembers()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           string        $set
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          integer
         */
        public function smembers( $set, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($set)<=0 )
            {
                return false;
            }     
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }        

            if( $data = $this->init($serverindex)->redis->sGetMembers( $set ) )
            {
                return $data;
            }        
            
            return false;
        }    
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::scontains()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           string        $set
         * @param           string        $key
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          integer
         */
        public function scontains( $set, $key, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($set)<=0 || strlen($key)<=0 )
            {
                return false;
            }     

            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }

            if( $this->init($serverindex)->redis->sContains( $set, $key ) )
            {
                return true;
            }        
            
            return false;
        }     
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::ssize()
         *
         * @author          Roman Telychko
         * @version         3.0.20111011
         * 
         * @param           string        $set
         * @param           integer       $dbindex
         * @param           integer       $serverindex
         * @return          integer
         */
        public function ssize( $set, $dbindex = -1, $serverindex = 0 )
        {
            if( strlen($set)<=0 )
            {
                return false;
            }
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }             

            if( $data = $this->init($serverindex)->redis->sSize( $set ) )
            {
                return $data;
            }        
            
            return false;
        }    

        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zadd()
         *
         * @author          Roman Telychko
         * @version         3.0.20120630
         * 
         * @param           string            $set
         * @param           integer           $score
         * @param           string            $value
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zadd( $set, $score = 0, $value = '', $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            $obj->redis->zAdd( $set, floatval($score), strval($value) );
            
            return true;
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zremove()
         *
         * @author          Roman Telychko
         * @version         3.0.20120630
         * 
         * @param           string            $set
         * @param           string            $value
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zremove( $set, $value = '', $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            $obj->redis->zDelete( $set, $value );
            
            return true;
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zrange()
         *
         * @author          Roman Telychko
         * @version         3.0.20120630
         * 
         * @param           string            $set
         * @param           integer           $start_score
         * @param           integer           $end_score
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zrange( $set, $start_score = 0, $end_score = -1, $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->zRange( $set, $start_score, $end_score, true );
        }
            
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zrevrange()
         *
         * @author          Roman Telychko
         * @version         3.0.20120630
         * 
         * @param           string            $set
         * @param           integer           $start_score
         * @param           integer           $end_score
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zrevrange( $set, $start_score = 0, $end_score = -1, $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->zRevRange( $set, $start_score, $end_score, true );
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zrangebyscore()
         *
         * @author          Roman Telychko
         * @version         3.0.20120703
         * 
         * @param           string            $set
         * @param           integer           $start_score
         * @param           integer           $end_score
         * @param           integer           $limit
         * @param           integer           $offset
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zrangebyscore( $set, $start_score = 0, $end_score = -1, $limit = -1, $offset = 0, $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->zRangeByScore( $set, $start_score, $end_score, [ 'withscores' => true, 'limit' => [ $offset, $limit ] ] );
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zrevrangebyscore()
         *
         * @author          Roman Telychko
         * @version         3.0.20120703
         * 
         * @param           string            $set
         * @param           integer           $start_score
         * @param           integer           $end_score
         * @param           integer           $limit
         * @param           integer           $offset
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zrevrangebyscore( $set, $start_score = 0, $end_score = -1, $limit = -1, $offset = 0, $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->zRevRangeByScore( $set, $start_score, $end_score, [ 'withscores' => true, 'limit' => [ $offset, $limit ] ] );
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zcard()
         *
         * @author          Roman Telychko
         * @version         3.0.20120703
         * 
         * @param           string            $set
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zcard( $set, $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->zCard( $set );
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zcount()
         *
         * @author          Roman Telychko
         * @version         3.0.20120703
         * 
         * @param           string            $set
         * @param           integer           $start_score
         * @param           integer           $end_score
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zcount( $set, $start_score = 0, $end_score = -1, $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->zCount( $set, $start_score, $end_score );
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zinter()
         *
         * @author          Roman Telychko
         * @version         3.0.20120703
         * 
         * @param           string            $output_set
         * @param           array             $input_sets
         * @param           array             $weights
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zinter( $output_set, $input_sets = [], $weights = [], $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            return $obj->redis->zInter( $output_set, $input_sets, $weights );
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::zincr()
         *
         * @author          Roman Telychko
         * @version         3.0.20120703
         * 
         * @param           string            $set
         * @param           integer           $score
         * @param           string            $value
         * @param           integer           $dbindex
         * @param           integer           $serverindex
         * @return          bool
         */
        public function zincr( $set, $score = 0, $value = '', $dbindex = -1, $serverindex = 0 )
        {
            $obj = $this->init($serverindex);
            
            if( $dbindex>=0 )    
            {
                $this->init($serverindex)->redis->select( $dbindex );
                #re::select_db( $dbindex, $serverindex );
            }
            
            $obj->redis->zIncrBy( $set, $score, strval($value) );
            
            return true;
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::getServerIP()
         *
         * @author          Roman Telychko
         * @version         2.2.20110829
         *    
         * @param           integer       $serverindex
         * @return          string
         */
        public function getServerIP( $serverindex = 0 )
        {
            return $this->init($serverindex)->server_ip;
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::getServerPort()
         *
         * @author          Roman Telychko
         * @version         2.2.20110829
         *    
         * @param           integer       $serverindex
         * @return          string
         */
        public function getServerPort( $serverindex = 0 )
        {
            return $this->init($serverindex)->server_port;
        }

        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::getExecTime()
         *
         * @author          Roman Telychko
         * @version         1.0.20130301
         *    
         * @return          array
         */
        public function getExecTime()
        {
            return $this->exec_time;
        }
        
        ///////////////////////////////////////////////////////////////////////////

        /**
         * re::getExecCount()
         *
         * @author          Roman Telychko
         * @version         1.0.20130301
         *    
         * @return          array
         */
        public function getExecCount()
        {
            return $this->exec_count;
        }        
        
        ///////////////////////////////////////////////////////////////////////////   
            
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

