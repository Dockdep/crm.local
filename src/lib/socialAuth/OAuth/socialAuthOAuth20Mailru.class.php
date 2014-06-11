<?php

/**
 * socialAuthOAuth20Mailru Class
 *
 * @author		Roman
 * @version         1.0.20111014
 */
class socialAuthOAuth20Mailru extends socialAuthOAuth20
{
    ///////////////////////////////////////////////////////////////////////////

    public function __construct( $settings )
    {
        $this->settings = $settings;
    }
    
    ///////////////////////////////////////////////////////////////////////////

    // inherits: init()
    // inherits: authorize()
    // inherits: accessToken()
    // inherits: fetchProfileInfo()
    // inherits: getProfileInfo()

    ///////////////////////////////////////////////////////////////////////////    

    protected function accessToken()
    {
        $ch = curl_init( $this->settings['access_token_uri'] );

        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 
            'grant_type=authorization_code'.
            '&client_id='.$this->settings['client_id'].
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
            throw new kException( ( isset($data['error_description']) && strlen($data['error_description'])>0 ? trim($data['error_description']) : core::i18n( 'w23_error_unknown' ) ) );
        }
        
        return $data;    
    }

    ///////////////////////////////////////////////////////////////////////////    

    protected function fetchProfileInfo( $data = array() )
    {   
        $params = array(
            'method'            => 'users.getInfo',
            'app_id'            => $this->settings['client_id'],
            // sig
            'session_key'       => $data['access_token'],
            'secure'            => '1',            
            'format'            => 'json',
            );

        ksort($params);
        
        $params['sig']          = md5( common2::joinKeyValue( $params, '=' ).$this->settings['client_secret'] );

        $data = file_get_contents( $this->settings['profile_uri'].'?'.common2::joinKeyValue( $params, '=', '&' ) );
        
        if( empty($data) )
        {
            throw new kException( core::i18n( 'w23_error_unknown' ) );
        }
        
        $data = json_decode( $data, true );
        
        if( empty($data) || !isset($data['0']) )
        {
            throw new kException( core::i18n( 'w23_error_unknown' ) );
        }
         
        return $data['0'];
    }
        
    ///////////////////////////////////////////////////////////////////////////    
    
    protected function getProfileInfo( $data = array() )
    {   
        if( empty($data) )
        {
            return false;
        }
        
        $user = array(
            'id'            => $data['uid'],
            'login'         => isset($data['nick']) ? $data['nick'] : $data['uid'],
            'email'         => isset($data['email']) ? $data['email'] : '',
            'email_fake'    => $data['uid'].'@mail.ru',
            'name'          => ( isset($data['first_name']) || isset($data['last_name']) ? trim($data['first_name'].' '.$data['last_name']) : ( isset($data['nick']) ? $data['nick'] : $data['uid'] ) ),
            );

        return 
            socialAuth::userLoginOrRegisterIfNotExists( 
                array(   
                    'login'         => 'mailru__'.$user['id'],
                    'email'         => $user['email_fake'],
                    'name'          => $user['name'],
                    'bithday'       => ( isset($data['birthday']) && strlen($data['birthday'])>0 ? date('Y-m-d', strtotime($data['birthday'])) : null ),
                    'gender'        => ( isset($data['sex']) ? ( $data['sex']==1 ? 0 : 1 ) : null ),
                    'region_id'     => null,
                    'about_me'      => null,
                    'interests'     => null,
                    'vote'          => null,
                    'contact_icq'   => null,
                    'options'       => etc::arr2hstore( 
                                            array( 
                                                'is_social'     => 1,
                                                'id'            => $user['id'],
                                                'login'         => $user['login'],
                                                'email'         => $user['email'],
                                                'first_name'    => ( isset($data['first_name']) ? $data['first_name'] : '' ),
                                                'last_name'     => ( isset($data['last_name']) ? $data['last_name'] : '' ),
                                                'name'          => ( isset($user['name']) ? $user['name'] : '' ),                                                
                                                'location'      => ( isset($data['location']['country']['city']) ? $data['location']['country']['city'] : '' ).' '.( isset($data['location']['country']['region']) ? $data['location']['country']['region'] : '' ).' '.( isset($data['location']['country']['name']) ? $data['location']['country']['name'] : '' ),                                                
                                                )
                                            ),
                                            
                    )                           
                );
    }
    
    ///////////////////////////////////////////////////////////////////////////    
}
