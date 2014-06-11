<?php

/**
 * socialAuthOAuth10 Class
 *
 * @author		Roman
 * @version         1.0.20111010
 */
abstract class socialAuthOAuth10
{
    ///////////////////////////////////////////////////////////////////////////

    protected $settings         = false;
    protected $oauth            = false;

    ///////////////////////////////////////////////////////////////////////////

    public function init()
    {
        $this->oauth = new OAuth(
            $this->settings['consumer_key'],
            $this->settings['consumer_secret'],
            ( isset($this->settings['signature_method']) ? $this->settings['signature_method'] : OAUTH_SIG_METHOD_HMACSHA1 ),
            OAUTH_AUTH_TYPE_URI
            );

        if( !isset($_GET['oauth_verifier']) || strlen(trim($_GET['oauth_verifier']))<=30 )
        {         
            $this->requestToken();
                      
            $this->authorize();                
            
            die();
        }
        else
        {
            if( isset($_SESSION['oauth']['oauth_token']) && isset($_GET['oauth_token']) && trim($_GET['oauth_token'])==trim($_SESSION['oauth']['oauth_token']) )
            {
                $this->accessToken();
                
                $data = $this->fetchProfileInfo();
                
                return $this->getProfileInfo( $data );
            }
            else
            {
                throw new kException( 'Invalid OAuth token.' );
            }
        }

        return false;
    }

    ///////////////////////////////////////////////////////////////////////////

    protected function requestToken()
    {
        $request_token_info = $this->oauth->getRequestToken( $this->settings['request_token_uri'] );
        $_SESSION['oauth'] = $request_token_info;
    }

    ///////////////////////////////////////////////////////////////////////////

    protected function authorize()
    {
        header( 'Location: '.$this->settings['authorize_uri'].'?oauth_token='.$_SESSION['oauth']['oauth_token'] );
        die();       
    }
    
    ///////////////////////////////////////////////////////////////////////////

    protected function accessToken()
    {
        $this->oauth->setToken( $_SESSION['oauth']['oauth_token'], $_SESSION['oauth']['oauth_token_secret'] );

        $access_token_info = $this->oauth->getAccessToken( 
            $this->settings['access_token_uri'], 
            $_SESSION['oauth']['oauth_token'], 
            trim($_GET['oauth_verifier'])
            );
            
        $_SESSION['oauth'] = $access_token_info;
        
        $this->oauth->setToken( $_SESSION['oauth']['oauth_token'], $_SESSION['oauth']['oauth_token_secret'] );
    }
    
    ///////////////////////////////////////////////////////////////////////////

    protected function fetchProfileInfo()
    {   
        $data = $this->oauth->fetch( $this->settings['profile_uri'], array(), 'GET', array('Connection'=>'close') );
        return json_decode($this->oauth->getLastResponse(), true);   
    }
    
    ///////////////////////////////////////////////////////////////////////////

    abstract protected function getProfileInfo();

    ///////////////////////////////////////////////////////////////////////////
}
