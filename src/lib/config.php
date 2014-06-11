<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace 
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * config Class
     *
     * @author          Roman Telychko
     * @version         1.0.20130227
     */
    class config
    { 
        ///////////////////////////////////////////////////////////////////////

        protected static    $instance               = false;
        protected           $config                 = [];
        protected           $application_name       = false;
    
        ///////////////////////////////////////////////////////////////////////

        /**
         * config::__construct()
         *
         * @author          Roman Telychko
         * @version         1.0.20130227
         *
         * @return          object
         */
        public function __construct()
        {
            $this->config = require( ROOT_PATH.'config/global.php' );
        }
        
        ///////////////////////////////////////////////////////////////////////

        /**
         * config::getInstance()
         *
         * @author          Roman Telychko
         * @version         1.0.20130227
         *
         * @return          object
         */
        protected static function getInstance()
        {
            if( empty(self::$instance) )
            {
                self::$instance = new self();
            }

            return self::$instance;
        }
        
        ///////////////////////////////////////////////////////////////////////
        
        /**
         * config::get()
         *
         * @author          Roman Telychko
         * @version         1.0.20130227
         * 
         * @param           string          $conf_string
         * @param           string|bool     $default
         * @return          string
         */
        public static function get( $conf_string, $default = false )
        {
            $self       = self::getInstance();

            if( !empty($self->config) )
            {
                $application_name = '';
            
                if( strpos( $conf_string, '#' ) !== false )
                {
                    list( $application_name, $config_name ) = explode( '#', $conf_string );
                }
                else
                {                    
                    $config_name = $conf_string;
                }
                
                if( empty($application_name) )
                {
                    $application_name = self::getApp();
                }
                
                if( strlen($config_name)>0 )
                {
                    $config_name_parts = explode( '/', trim( $config_name, '/' ) );

                    switch( count($config_name_parts) )
                    {                    
                        case 1:
                            if( isset($self->config[$application_name][$config_name_parts['0']]) )
                            {
                                return $self->config[$application_name][$config_name_parts['0']];
                            }
                            break;
                            
                        case 2:
                            if( isset($self->config[$application_name][$config_name_parts['0']][$config_name_parts['1']]) )
                            {
                                return $self->config[$application_name][$config_name_parts['0']][$config_name_parts['1']];
                            }
                            break;
                            
                        case 3:
                            if( isset($self->config[$application_name][$config_name_parts['0']][$config_name_parts['1']][$config_name_parts['2']]) )
                            {
                                return $self->config[$application_name][$config_name_parts['0']][$config_name_parts['1']][$config_name_parts['2']];
                            }
                            break;
                            
                        case 4:
                            if( isset($self->config[$application_name][$config_name_parts['0']][$config_name_parts['1']][$config_name_parts['2']][$config_name_parts['3']]) )
                            {
                                return $self->config[$application_name][$config_name_parts['0']][$config_name_parts['1']][$config_name_parts['2']][$config_name_parts['3']];
                            }
                            break;
                    }
                }
            }
            
            return $default;
        }
        
        ///////////////////////////////////////////////////////////////////////
        
        /**
         * config::setApp()
         *
         * @author          Roman Telychko
         * @version         1.0.20130227
         * 
         * @param           string          $application_name
         * @return          bool
         */
        public static function setApp( $application_name = 'frontend' )
        {
            self::getInstance()->application_name = $application_name;   
            
            return true;
        }
        
        ///////////////////////////////////////////////////////////////////////
        
        /**
         * config::getApp()
         *
         * @author          Roman Telychko
         * @version         1.0.20130227
         * 
         * @return          string
         */
        public static function getApp()
        {
            return self::getInstance()->application_name;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * config::getDomain()
         *
         * @author          Roman Telychko
         * @version         1.0.20131105
         *
         * @return          string
         */
        public static function getDomain()
        {
            $domain     = \Phalcon\DI::getDefault()->get('request')->getHttpHost();
            $domains    = \config::get('global#domains/www');

            if( in_array( $domain, $domains ) )
            {
                return $domain;
            }

            return ( isset($domains['1']) ? $domains['1'] : 'victoriadate.com.ua' );
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * config::getDomainByID()
         *
         * @author          Roman Telychko
         * @version         1.0.20140110
         *
         * @param           integer         $domain_id
         * @return          string
         */
        public static function getDomainByID( $domain_id = 1 )
        {
            $domains    = \config::get('global#domains/www');

            return ( isset($domains[$domain_id]) ? $domains[$domain_id] : 'victoriadate.com.ua' );
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * config::getDomainID()
         *
         * @author          Roman Telychko
         * @version         1.0.20131105
         *
         * @return          string
         */
        public static function getDomainID()
        {
            $domain     = self::getDomain();
            $domains    = array_flip( \config::get('global#domains/www') );

            if( isset($domains[ $domain ]) )
            {
                return $domains[ $domain ];
            }

            return 1;
        }

        ///////////////////////////////////////////////////////////////////////
        
        /**
         * config::getProjectDir()
         *
         * @author          Roman Telychko
         * @version         1.0.20130227
         * 
         * @return          string
         */       
        public static function getProjectDir()
        {
            return ROOT_PATH;
        }

        ///////////////////////////////////////////////////////////////////////
        
        /**
         * config::getDataDir()
         *
         * @author          Roman Telychko
         * @version         1.0.20130227
         * 
         * @return          string
         */       
        public static function getDataDir()
        {
            return ROOT_PATH.'data/';
        }
        
        ///////////////////////////////////////////////////////////////////////
        
        /**
         * config::getLogDir()
         *
         * @author          Roman Telychko
         * @version         1.0.20130312
         * 
         * @return          string
         */       
        public static function getLogDir()
        {
            return ROOT_PATH.'log/';
        }
        
        ///////////////////////////////////////////////////////////////////////    
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
