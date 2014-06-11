<?php

/**
 * socialAuthOAuth20Odnoklassniki Class
 *
 * @author		Roman
 * @version         1.0.20111012
 */
class socialAuthOAuth20Odnoklassniki extends socialAuthOAuth20
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
            'application_key'   => $this->settings['application_key'],
            'client_id'         => $this->settings['client_id'],
            'format'            => 'JSON',
            'method'            => 'users.getCurrentUser',
            );
    
        ksort($params);

        $params['sig']          = strtolower( md5( common2::joinKeyValue( $params, '=' ).md5( trim( $data['access_token'] ).$this->settings['client_secret'] ) ) );
        $params['access_token'] = trim( $data['access_token'] );
        
        $data = json_decode( file_get_contents( $this->settings['profile_uri'].'?'.common2::joinKeyValue( $params, '=', '&' ) ), true );

        if( isset($data['error_code']) || empty($data) )
        {
            throw new kException( ( isset($data['error_msg']) && strlen($data['error_msg'])>0 ? trim($data['error_msg']) : core::i18n( 'w23_error_unknown' ) ) );
        }
                    
        return $data;
    }
        
    ///////////////////////////////////////////////////////////////////////////    
    
    protected function getProfileInfo( $data = array() )
    {   
        if( empty($data) )
        {
            return false;
        }
                
        $user = array(
            'id'        => $data['uid'],
            'login'     => 'id'.$data['uid'],
            'email'     => 'id'.$data['uid'].'@odnoklassniki.ru',
            'name'      => ( isset($data['name']) ? $data['name'] : 'id'.$data['uid'] ),
            );

        return 
            socialAuth::userLoginOrRegisterIfNotExists( 
                array(   
                    'login'         => 'odnoklassniki__'.$user['login'],
                    'email'         => $user['email'],
                    'name'          => $user['name'],
                    'bithday'       => ( isset($data['birthday']) ? $data['birthday'] : null ),
                    'gender'        => ( isset($data['gender']) && in_array( $data['gender'], array('male','female') ) ? ( $data['gender']=='male' ? 1 : 0 ) : null ),
                    'region_id'     => null,
                    'about_me'      => null,
                    'interests'     => null,
                    'vote'          => null,
                    'contact_icq'   => null,
                    'options'       => etc::arr2hstore( 
                                            array( 
                                                'is_social'     => 1,
                                                'id'            => $user['id'],
                                                'email'         => $user['email'],
                                                'first_name'    => ( isset($data['first_name']) ? $data['first_name'] : '' ),
                                                'last_name'     => ( isset($data['last_name']) ? $data['last_name'] : '' ),
                                                'name'          => ( isset($data['name']) ? $data['name'] : '' ),
                                                )
                                            ),
                                            
                    )                           
                );
    }
    
    ///////////////////////////////////////////////////////////////////////////    
}
