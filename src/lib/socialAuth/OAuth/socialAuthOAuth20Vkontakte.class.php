<?php

/**
 * socialAuthOAuth20Vkontakte Class
 *
 * @author		Roman
 * @version         1.0.20111010
 */
class socialAuthOAuth20Vkontakte extends socialAuthOAuth20
{
    ///////////////////////////////////////////////////////////////////////////

    public function __construct( $settings )
    {
        $this->settings = $settings;
    }
    
    ///////////////////////////////////////////////////////////////////////////

    // inherits: init()
    // inherits: requestToken()
    // inherits: authorize()
    // inherits: accessToken()
    // inherits: fetchProfileInfo()
    // inherits: getProfileInfo()

    ///////////////////////////////////////////////////////////////////////////    

    protected function fetchProfileInfo( $data = array() )
    {   
        $ch = curl_init( $this->settings['profile_uri'].
            '&uids='.trim( $data['user_id'] ).
            '&access_token='.trim( $data['access_token'] )
            );
                
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $data = json_decode( curl_exec($ch), true );
        curl_close($ch);

        if( isset($data['error']) || empty($data) )
        {
            throw new kException( ( isset($data['error_description']) && strlen($data['error_description'])>0 ? trim($data['error_description']) : core::i18n( 'w23_error_unknown' ) ) );
        }
                    
        return $data;
    }
        
    ///////////////////////////////////////////////////////////////////////////    
    
    protected function getProfileInfo( $data = array() )
    {   
        if( !isset($data['response']['0']) || empty($data['response']['0']) )
        {
            return false;
        }
        
        $data = $data['response']['0'];

        $user = array(
            'id'        => $data['uid'],
            'login'     => ( isset($data['screen_name']) ? $data['screen_name'] : 'id'.$data['uid'] ),
            'email'     => ( isset($data['screen_name']) ? $data['screen_name'] : 'id'.$data['uid'] ).'@vkontakte.ru',
            'name'      => trim( ( isset($data['first_name']) ? $data['first_name'] : '' ).' '.( isset($data['last_name']) ? $data['last_name'] : '' ) ),
            );
        
        return 
            socialAuth::userLoginOrRegisterIfNotExists( 
                array(
                    'login'         => 'vkontakte__'.$user['login'],
                    'email'         => $user['email'],
                    'name'          => $user['name'],
                    'bithday'       => ( isset($data['bdate']) ? date( 'Y-m-d', strtotime($data['bdate']) ) : null ),
                    'gender'        => ( isset($data['sex']) ? ( $data['sex']==2 ? 1 : 0 ) : null ),
                    'region_id'     => null,
                    'about_me'      => ( isset($data['description']) ? $data['description'] : null ),
                    'interests'     => null,
                    'vote'          => null,
                    'contact_icq'   => null,
                    'options'       => etc::arr2hstore( 
                                            array( 
                                                'is_social'     => 1,
                                                'id'            => $user['id'],
                                                'login'         => $user['login'],
                                                'first_name'    => ( isset($data['first_name']) ? $data['first_name'] : '' ),
                                                'last_name'     => ( isset($data['last_name']) ? $data['last_name'] : '' ),        
                                                ) 
                                            ),
                    )                            
                );
    }
    
    ///////////////////////////////////////////////////////////////////////////    
}
