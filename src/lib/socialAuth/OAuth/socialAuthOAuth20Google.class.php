<?php

/**
 * socialAuthOAuth20Google Class
 *
 * @author		Roman
 * @version         1.0.20111010
 */
class socialAuthOAuth20Google extends socialAuthOAuth20
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

    protected function authorize()
    {
        header( 'Location: '.$this->settings['authorize_uri'].
            '&client_id='.$this->settings['client_id'].
            '&redirect_uri='.#$this->settings['redirect_uri']
                urlencode($this->settings['redirect_uri'])
            , true, 302
            );
            
        die();
    }

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
        $ch = curl_init( $this->settings['profile_uri'].'?access_token='.trim( $data['access_token'] ) );
                
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        $data = curl_exec($ch);
        
        curl_close($ch);

        $doc = simplexml_load_string($data);        

        if( $doc )
        {
            $data = array(            
                'id'            => strval($doc->id),
                'login'         => strval($doc->id),
                'email'         => strval($doc->author->email),
                'email_fake'    => strval($doc->id).'@google.com',
                'name'          => strval($doc->author->name),
                );
        }

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
        
        return 
            socialAuth::userLoginOrRegisterIfNotExists( 
                array(   
                    'login'         => 'google__'.$data['login'],
                    'email'         => $data['email_fake'],
                    'name'          => $data['name'],
                    'bithday'       => null,
                    'gender'        => null,
                    'region_id'     => null,
                    'about_me'      => null,
                    'interests'     => null,
                    'vote'          => null,
                    'contact_icq'   => null,
                    'options'       => etc::arr2hstore( 
                                            array( 
                                                'is_social'     => 1,
                                                'id'            => $data['id'],
                                                'login'         => $data['login'],
                                                'email'         => $data['email'],
                                                'name'          => ( isset($data['name']) ? $data['name'] : '' ),
                                                )
                                            ),
                                            
                    )                           
                );
    }
    
    ///////////////////////////////////////////////////////////////////////////    
}
