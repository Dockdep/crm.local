<?php

/**
 * socialAuthOAuth20 Class
 *
 * @author		Roman
 * @version         1.2.20120313
 */
abstract class socialAuthOAuth20
{
    ///////////////////////////////////////////////////////////////////////////

    protected $settings         = false;
    protected $oauth            = false;

    ///////////////////////////////////////////////////////////////////////////

    public function init()
    {
        if( isset($_GET['error_description']) && strlen($_GET['error_description'])>0 )
        {
            throw new kException( trim($_GET['error_description']) );
        }
        elseif( isset($_GET['error']) )
        {
            if( trim($_GET['error'])=='access_denied' )
            {
                $error_message = core::i18n( 'w23_error_user_denied' );         // Ошибка авторизации: Пользователь отм

            }
            else
            {
                $error_message = trim($_GET['error']);
            }

            throw new kException( $error_message );
        }
        else if( !isset($_GET['code']) )                                        // step 1
        {        
            $this->authorize();
            die();
        }
        else if( isset($_GET['code']) && strlen(trim($_GET['code']))>6 )        // step 2
        {                     
            $data = $this->accessToken();

            $data = $this->fetchProfileInfo( $data );
            
            return 
                $this->getProfileInfo( $data );
        }
        else
        {
            throw new kException( core::i18n( 'w23_error_unknown' ) );          // Произошла неизвестная ошибка. Попробуйте немного позже.
        }
        
        return false;
    }

    ///////////////////////////////////////////////////////////////////////////

    protected function authorize()
    {
        $_SESSION['state'] = md5(uniqid(rand(), TRUE));                 // CSRF protection

        header( 'Location: '.$this->settings['authorize_uri'].
            '&client_id='.$this->settings['client_id'].
            '&redirect_uri='.urlencode($this->settings['redirect_uri']).
            '&state='.$_SESSION['state'], 
            true, 302
            );
            
        die();
    }
    
    ///////////////////////////////////////////////////////////////////////////

    protected function accessToken()
    {
        $ch = curl_init( 
                $this->settings['access_token_uri'].
                    '?client_id='.$this->settings['client_id'].
                    '&client_secret='.$this->settings['client_secret'].
                    '&redirect_uri='.urlencode($this->settings['redirect_uri']).
                    '&code='.trim( $_GET['code'] )
                );
                
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $data = json_decode( curl_exec($ch), true );
        curl_close($ch);

        if( isset($data['error']) || !isset($data['access_token']) )
        {
            if( isset($data['error_description']) && strlen($data['error_description'])>0 )
            {
                $error_message = $data['error_description'];
            }
            elseif( isset($data['error']) )
            {
                if( trim($data['error'])=='access_denied' )
                {
                    $error_message = core::i18n( 'w23_error_user_denied' );     // Ошибка авторизации: Пользователь отменил авторизацию.
                }
                else
                {
                    $error_message = trim($data['error']);
                }
            }
            else
            {
                $error_message = core::i18n( 'w23_error_unknown' );             // Произошла неизвестная ошибка. Попробуйте немного позже.
            }

            throw new kException( $error_message );
        }
        
        return $data;
    }
    
    ///////////////////////////////////////////////////////////////////////////

    abstract protected function fetchProfileInfo();
    
    ///////////////////////////////////////////////////////////////////////////

    abstract protected function getProfileInfo();

    ///////////////////////////////////////////////////////////////////////////
}
