<?php

/**
 * socialAuthOAuth20Facebook Class
 *
 * @author		Roman
 * @version         1.0.20111010
 */
class socialAuthOAuth20Facebook extends socialAuthOAuth20
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
        if( !isset($_GET['state']) || !isset($_SESSION['state']) || trim($_GET['state'])!=$_SESSION['state'] )
        {
            //throw new kException( 'CSRF attack was detected.' );
            throw new kException( core::i18n( 'w23_error_unknown' ) );
        }
    
        @$response = file_get_contents($this->settings['access_token_uri'].
                        '?client_id='.$this->settings['client_id'].
                        '&client_secret='.$this->settings['client_secret'].
                        '&redirect_uri='.urlencode($this->settings['redirect_uri']).
                        '&code='.trim( $_GET['code'] )
                        );
        $data = null;

        parse_str($response, $data);    

        if( isset($data['error']) || !isset($data['access_token']) )
        {
            if( isset($data['error_description']) && strlen($data['error_description'])>0 )
            {
                $data['error_description'] = trim($data['error_description']);

                if( $data['error_description']=='The user denied your request.' )
                {
                    $message = core::i18n( 'w23_error_oauth_user_denied' );
                }
                else
                {
                    $message = $data['error_description'];
                }
            }
            else
            {
                $message = core::i18n( 'w23_error_unknown' );
            }

            throw new kException( $message );
        }
        
        return $data;
    }

    ///////////////////////////////////////////////////////////////////////////    

    protected function fetchProfileInfo( $data = array() )
    {   
        $data = json_decode(
                    file_get_contents(
                        $this->settings['profile_uri'].'?access_token='.trim( $data['access_token'] ) 
                        ), true
                    );
    
        if( isset($data['error']) || empty($data) )
        {
            throw new kException( ( isset($data['error_description']) && strlen($data['error_description'])>0 ? trim($data['error_description']) : core::i18n( 'w23_error_unknown' ) ) );
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
            'id'        => $data['id'],
            'login'     => ( isset($data['username']) ? $data['username'] : 'id'.$data['id'] ),
            'email'     => ( isset($data['username']) ? $data['username'] : 'id'.$data['id'] ).'@facebook.com',
            'name'      => ( isset($data['name']) ? $data['name'] : ( isset($data['username']) ? $data['username'] : 'id'.$data['id'] ) ),
            );

        return 
            socialAuth::userLoginOrRegisterIfNotExists( 
                array(   
                    'login'         => 'facebook__'.$user['login'],
                    'email'         => $user['email'],
                    'name'          => $user['name'],
                    'bithday'       => ( isset($data['birthday']) ? date( 'Y-m-d', strtotime($data['birthday']) ) : null ),
                    'gender'        => ( isset($data['gender']) && in_array( $data['gender'], array('male','female') ) ? ( $data['gender']=='male' ? 1 : 0 ) : null ),
                    'region_id'     => null,
                    'about_me'      => ( isset($data['bio']) ? $data['bio'] : null ),
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
                                                'name'          => ( isset($data['name']) ? $data['name'] : '' ),
                                                'location'      => ( isset($data['location']['name']) ? $data['location']['name'] : '' ),
                                                'timezone'      => ( isset($data['timezone']) ? $data['timezone'] : '' ),
                                                'lang'          => ( isset($data['locale']) ? $data['locale'] : '' ),
                                                )
                                            ),
                                            
                    )                           
                );
    }
    
    ///////////////////////////////////////////////////////////////////////////    
}
