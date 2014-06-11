<?php

/**
 * socialAuthOpenIDCustomFinish Class
 *
 * @author		Roman
 * @version         1.0.20111014
 */
class socialAuthOpenIDCustomFinish extends socialAuthOpenID
{
    ///////////////////////////////////////////////////////////////////////////
    
    public function init()
    {
        $store      = new Auth_OpenID_FileStore( sys_get_temp_dir() );        
        $consumer   = new Auth_OpenID_Consumer( $store );

        // Завершаем процесс авторизации, используя ответ сервера.
        $return_to = core::route( '@user_login_social?mechanism=openid_finish', true ) .'?'.$_SERVER['QUERY_STRING'];

        $openid_url = str_replace( array('http://','https://'), '', $this->openid_url );
        $openid_url = preg_replace( '/\/+$/', '', $openid_url );

        $response = $consumer->complete($return_to);

        switch( $response->status )
        {
            case Auth_OpenID_CANCEL:
                throw new kException( core::i18n( 'w23_error_openid_check' ) );
                break;
                
            case Auth_OpenID_FAILURE:
                throw new kException( core::i18n( 'w23_error_auth' ).$response->message );
                break;
                
            case Auth_OpenID_SUCCESS:            
                $openid = $response->getDisplayIdentifier();

                ///////////////////////////////////////////////////////////////

                $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response, false);
                $data1 = $sreg_resp->contents();

                if( empty($data1) && strpos($openid, '.livejournal.com')===false && strpos($openid, 'openid.yandex.ru')===false )
                {
                    $temp_args = isset($response->message->args) ? (array) $response->message->args : array();
                    
                    $data1 = array();
                    
                    if( !empty($temp_args) )
                    {                    
                        foreach( $temp_args['keys'] as $k => $v )
                        {
                            $data1[ str_replace( 'sreg.', '', $v['1'] ) ] = strval($temp_args['values'][$k]);
                        }
                    }                    
                }
                
                $ax = new Auth_OpenID_AX_FetchResponse();
                $obj = $ax->fromSuccessResponse($response);

                $data2 = isset($obj->data) ? $obj->data : array();
                
                if( !empty($data2) )
                {
                    $temp = array();

                    foreach( 
                        array( 
                            'http://axschema.org/namePerson/friendly'   => 'nickname',
                            'http://axschema.org/namePerson/first'      => 'firstname',
                            'http://axschema.org/namePerson/last'       => 'lastname',
                            'http://axschema.org/birthDate'             => 'dob',
                            'http://axschema.org/person/gender'         => 'gender',
                            'http://axschema.org/pref/language'         => 'language',
                            'http://axschema.org/pref/timezone'         => 'timezone',
                            'http://axschema.org/contact/email'         => 'email',
                            'http://axschema.org/contact/country/home'  => 'country',
                            
                        ) as $key => $val )
                    {
                        if( isset($data2[$key]['0']) )
                        {
                            $temp[$val] = $data2[$key]['0'];
                        } 
                    }
                    
                    if( !isset($temp['nickname']) )
                    {
                        $temp['nickname'] = $openid;
                    }
                    
                    $data2 = $temp;
                    
                    unset($temp);
                }
                
                $data = array_merge( $data1, $data2 );

                if( 
                    empty($data) || 
                    !isset($data['nickname']) || 
                    strlen(trim($data['nickname']))<=0 || 
                    strpos(trim($data['nickname']), '.livejournal.com')!==false ||
                    strpos(trim($data['nickname']), 'openid.yandex.ru')!==false
                  )
                {
                    if( strpos($openid, '.livejournal.com')!==false )
                    {
                        if( preg_match( '#^https?\:\/\/(.+)\.livejournal\.com.*#i', $openid, $match ) )
                        {
                            if( isset($match['1']) && strlen($match['1'])>0 )
                            {
                                $data['nickname'] = trim($match['1']);
                            }
                            
                            $file = file_get_contents( $openid.'data/foaf' );
                            
                            if( strlen($file)>10 )
                            {
                                $xml = simplexml_load_string( str_replace( 'foaf:', 'foaf_', $file ) );    

                                if( $xml )
                                {
                                    $data['nickname'] = strval( $xml->{'foaf_Person'}->{'foaf_nick'} );
                                    $data['fullname'] = strval( $xml->{'foaf_Person'}->{'foaf_name'} );
                                    $data['dob'] = strval( $xml->{'foaf_Person'}->{'foaf_dateOfBirth'} );
                                }
                            }
                        } 
                    }
                    else if( strpos($openid, 'openid.yandex.ru')!==false )
                    {
                        if( preg_match( '#^https?\:\/\/openid\.yandex\.ru/([^\/]+)#i', $openid, $match ) )
                        {
                            if( isset($match['1']) && strlen($match['1'])>0 )
                            {
                                $data['nickname'] = trim( preg_replace( '/\/+$/', '', $match['1']) );
                            }                                
                        }
                    }
                }

                if( empty($data) || !isset($data['nickname']) || strlen($data['nickname'])<=0 )
                {
                    throw new kException( core::i18n( 'w23_error_empty_answer' ) );
                }

                ///////////////////////////////////////////////////////////////

                $user = array(
                    'id'            => $data['nickname'],
                    'login'         => $data['nickname'],
                    'email'         => ( isset($data['email']) ? $data['email'] : '' ),
                    'email_fake'    => $data['nickname'].'@'.$openid_url,
                    'name'          => ( ( isset($data['fullname']) && strlen($data['fullname'])>0 ) ? $data['fullname'] : '' ),
                    );
                    
                if( strlen($user['name'])<=0 )
                {
                    if( ( isset($data['firstname']) && strlen($data['firstname'])>0 ) || ( isset($data['lastname']) && strlen($data['lastname'])>0 ) )
                    {
                        $user['name'] = 
                            trim(
                                ( ( isset($data['firstname']) && strlen($data['firstname'])>0 ) ? trim($data['firstname']).' '  : '' ).
                                ( ( isset($data['lastname']) && strlen($data['lastname'])>0 )   ? trim($data['lastname'])       : '' )
                                );
                    }
                    else
                    {
                        $user['name'] = $data['nickname'];
                    }
                }

                return 
                    socialAuth::userLoginOrRegisterIfNotExists( 
                        array(   
                            'login'         => 'openid__'.$openid_url.'__'.$user['login'],
                            'email'         => $user['email_fake'],
                            'name'          => $user['name'],
                            'bithday'       => ( isset($data['dob']) && strlen($data['dob'])>0          ? date( 'Y-m-d', strtotime($data['dob']) )  : null ),
                            'gender'        => ( isset($data['gender']) && strlen($data['gender'])>0    ? ( $data['gender']=='M' ? 1 : 0 )          : null ),
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
                                                        'name'          => $user['name'],
                                                        'location'      => ( isset($data['country']) ? $data['country'] : '' ),
                                                        'timezone'      => ( isset($data['timezone']) ? $data['timezone'] : '' ),
                                                        'lang'          => ( isset($data['language']) ? $data['language'] : '' ),
                                                        )
                                                    ),
                                                    
                            )                           
                        );
                break;
                
            default:
                throw new kException( core::i18n( 'w23_error_unknown' ) );
                break;
        }
        
        return false;
    }
    
    ///////////////////////////////////////////////////////////////////////////
}
