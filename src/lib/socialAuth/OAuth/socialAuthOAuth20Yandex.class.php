<?php

/**
 * socialAuthOAuth20Yandex Class
 *
 * @author		Roman
 * @version         1.0.20111011
 */
class socialAuthOAuth20Yandex extends socialAuthOAuth20
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

    protected function accessToken()
    {
        if( !isset($_GET['state']) || !isset($_SESSION['state']) || trim($_GET['state'])!=$_SESSION['state'] )
        {
            throw new kException( core::i18n( 'w23_error_csrf_attack' ) );
        }
    
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
        $ch = curl_init( $this->settings['profile_uri'] );
                
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );        
        curl_setopt( $ch, CURLOPT_HTTPHEADER,   array( 'Authorization: OAuth '.trim( $data['access_token'] ) ) );

        $xml_file = curl_exec($ch);
        curl_close($ch);
        
        $xml_file = trim($xml_file);
        
        if( strpos( $xml_file, '<' )!==false )
        {
            $doc = simplexml_load_string( $xml_file );

            if( $doc )
            {
                $data = array(
                    'id'        => strval($doc->id),
                    'login'     => strval($doc->id),
                    'email'         => ( isset($doc->email) ? strval($doc->email) : strval($doc->id).'@yandex.ru' ),
                    'email_fake'    => strval($doc->id).'@yandex.ru',
                    'name'      => ( isset($doc->name) ? strval($doc->name) : strval($doc->id) ),
                    'gender'    => ( isset($doc->sex) ? ( strval($doc->sex)=='man' ? 1 : 0 ) : null ),                
                    'bithday'   => date( 'Y-m-d', strtotime(strval($doc->birth_year).'-'.strval($doc->birth_month).'-'.strval($doc->birth_day)) ),
                    'city'      => ( isset($doc->city) ? strval($doc->city) : '' ),
                    'country'   => ( isset($doc->country) ? strval($doc->country) : '' ),
                    'contact_icq'       => ( isset($doc->icq) ? strval($doc->icq) : null ),
                    'contact_skype'     => ( isset($doc->skype) ? strval($doc->skype) : null ),
                    'contact_gtalk'     => ( isset($doc->{'g-talk'}) ? strval($doc->{'g-talk'}) : null ),
                    'contact_mailru'    => ( isset($doc->{'m-agent'}) ? strval($doc->{'m-agent'}) : null ),
                    'website'   => ( isset($doc->website) ? strval($doc->website) : '' ),
                    );
            }
            else
            {
                throw new kException( core::i18n( 'w23_error_unknown' ) );
            }
        }
        else
        {
            throw new kException( strip_tags( $xml_file ) );
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
                    'login'         => 'yandex__'.$data['login'],
                    'email'         => $data['email_fake'],
                    'name'          => $data['name'],
                    'bithday'       => $data['bithday'],
                    'gender'        => $data['gender'],
                    'region_id'     => null,
                    'about_me'      => null,
                    'interests'     => null,
                    'vote'          => null,
                    'contact_icq'   => $data['contact_icq'],
                    'options'       => etc::arr2hstore( 
                                            array( 
                                                'is_social'      => 1,
                                                'id'             => $data['id'],
                                                'email'          => $data['email'],
                                                'name'           => $data['name'],
                                                'city'           => $data['city'],
                                                'country'        => $data['country'],
                                                'contact_icq'    => $data['contact_icq'],
                                                'contact_skype'  => $data['contact_skype'],
                                                'contact_gtalk'  => $data['contact_gtalk'],
                                                'contact_mailru' => $data['contact_mailru'],
                                                'website'        => $data['website'],
                                                ) 
                                            ),
                    )                            
                );
    }
    
    ///////////////////////////////////////////////////////////////////////////    
}
