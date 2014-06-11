<?php

/**
 * socialAuthOpenID Class
 *
 * @author		Roman
 * @version         1.0.20111014
 */
abstract class socialAuthOpenID
{
    ///////////////////////////////////////////////////////////////////////////

    protected $oauth            = false;
    protected $openid_url       = false;

    ///////////////////////////////////////////////////////////////////////////

    public function __construct( $openid_url = false )
    {
        ///////////////////////////////////////////////////////////////////////
        
        $this->openid_url = $openid_url;
        
        if( $this->openid_url )
        {
            $_SESSION['openid']['openid_url'] = $this->openid_url;        
        }
        else
        {
            $this->openid_url = isset($_SESSION['openid']['openid_url']) ? $_SESSION['openid']['openid_url'] : '';
        }

        ///////////////////////////////////////////////////////////////////////

        /*
        $path_extra = sfConfig::get('sf_plugins_dir').'/OpenIDPlugin/lib/';
        $path = $path_extra . PATH_SEPARATOR . ini_get('include_path');
        ini_set('include_path', $path);
        */

        #define( 'PLUGIN_OPENID_ROOT', sfConfig::get('sf_plugins_dir').'/OpenIDPlugin/lib/' );
        
        require_once PLUGIN_OPENID_ROOT.'Auth/OpenID/Load.php';
        
        ///////////////////////////////////////////////////////////////////////
    }

    ///////////////////////////////////////////////////////////////////////////

    abstract public function init();

    ///////////////////////////////////////////////////////////////////////////
}
