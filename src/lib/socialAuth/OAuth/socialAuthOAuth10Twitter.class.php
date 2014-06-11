<?php

/**
 * socialAuthOAuth10Twitter Class
 *
 * @author		Roman
 * @version         1.0.20111010
 */
class socialAuthOAuth10Twitter extends socialAuthOAuth10
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

    protected function getProfileInfo( $data = array() )
    {   
        if( empty($data) )
        {
            return false;
        }
    
        $user = array(
            'id'        => $data['id'],
            'login'     => ( isset($data['screen_name']) ? $data['screen_name'] : 'id'.$data['id'] ),
            'email'     => ( isset($data['screen_name']) ? $data['screen_name'] : 'id'.$data['id'] ).'@twitter.com',
            'name'      => ( isset($data['name']) ? $data['name'] : ( strlen($data['screen_name'])>0 ? $data['screen_name'] : 'id'.$data['id'] ) ),
            );
        
        return 
            socialAuth::userLoginOrRegisterIfNotExists( 
                array(
                    'login'         => 'twitter__'.$user['login'],
                    'email'         => $user['email'],
                    'name'          => $user['name'],
                    'bithday'       => null,
                    'gender'        => null,
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
                                                'name'          => $user['name'],    
                                                'location'      => ( isset($data['location']) ? $data['location'] : '' ),
                                                'lang'          => ( isset($data['lang']) ? $data['lang'] : '' ),
                                                'timezone'      => ( isset($data['time_zone']) ? $data['time_zone'] : '' ),
                                                'utc_offset'    => ( isset($data['utc_offset']) ? $data['utc_offset'] : '' ),
                                                ) 
                                            ),
                    )                     
                );
    }

    ///////////////////////////////////////////////////////////////////////////    
}
