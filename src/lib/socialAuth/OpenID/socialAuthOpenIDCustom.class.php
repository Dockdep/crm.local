<?php

/**
 * socialAuthOpenIDCustom Class
 *
 * @author		Roman
 * @version         1.0.20111014
 */
class socialAuthOpenIDCustom extends socialAuthOpenID
{
    ///////////////////////////////////////////////////////////////////////////
    
    public function init()
    {
        ///////////////////////////////////////////////////////////////////////    
    
        if( filter_var( $this->openid_url, FILTER_VALIDATE_URL )===false )
        {
            throw new kException( core::i18n( 'w23_error_openid_invalid_url' ) );
        }

        ///////////////////////////////////////////////////////////////////////

        $store      = new Auth_OpenID_FileStore( sys_get_temp_dir() );        
        $consumer   = new Auth_OpenID_Consumer( $store );

        $auth_request = $consumer->begin($this->openid_url);

        if( !$auth_request ) 
        {
            throw new kException( core::i18n( 'w23_error_openid_invalid_server' ) );
        }

        ///////////////////////////////////////////////////////////////////////

        $sreg_request = Auth_OpenID_SRegRequest::build(
            // Required
            array('nickname'),
            // Optional
            array('fullname', 'email', 'dob', 'gender', 'country', 'language', 'timezone')
            );

        if($sreg_request) 
        {
            $auth_request->addExtension($sreg_request);
        }          
        
        $policy_uris = null;
        if( isset($_GET['policies']) && strlen($_GET['policies'])>0 ) 
        {
            $policy_uris = trim($_GET['policies']);
        }

        $pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
        if ($pape_request) 
        {
            $auth_request->addExtension($pape_request);
        }

        ///////////////////////////////////////////////////////////////////////
        
        $attribute = array();
        
        // Create attribute request object
        // See http://code.google.com/apis/accounts/docs/OpenID.html#Parameters
        //     http://www.hyves-developers.nl/documentation/openid/specifications
        // Usage: make($type_uri, $count=1, $required=false, $alias=null)
        
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/friendly',     1,      1,  'nickname');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first',        1,      1,  'firstname');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last',         1,      1,  'lastname');
        
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/birthDate',               1,      0,  'dob');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/person/gender',           1,      0,  'gender');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/language',           1,      0,  'language');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/timezone',           1,      0,  'timezone');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email',           1,      0,  'email');
        $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/country/home',    1,      0,  'country');

        // Create AX fetch request
        $ax = new Auth_OpenID_AX_FetchRequest();

        // Add attributes to AX fetch request
        foreach($attribute as $attr)
        {
            $ax->add($attr);
        }

        // Add AX fetch request to authentication request
        $auth_request->addExtension($ax);
        
        ///////////////////////////////////////////////////////////////////////        

        // Редирект пользователя на OpenID server для авторизации.
        // Для OpenID 1, делаем редирект.  
        if($auth_request->shouldSendRedirect()) 
        {
            $redirect_url = $auth_request->redirectURL( 
                                core::route( '@homepage', true ), 
                                core::route( '@user_login_social?mechanism=openid_finish', true )
                                );

            // Если redirect URL не может быть сформирован, выведем сообщение об ошибках.
            if (Auth_OpenID::isFailure($redirect_url)) 
            {
                throw new kException( core::i18n( 'w23_error_openid_cannot_locate_on_server' ) . $redirect_url->message );
            } 
            else 
            {
                // Делаем редирект.
                header("Location: ".$redirect_url);
            }
        } 
        // Для OpenID 2, используем форму для  отправки POST
        else 
        {
            // Создаем форму для JS-редиректа
            $form_id = 'openid_message';
            
            $form_html = $auth_request->formMarkup(
                core::route( '@homepage', true ), 
                core::route( '@user_login_social?mechanism=openid_finish', true ),
                false, 
                array('id' => $form_id)
                );

            // Отправляем форму
            if( Auth_OpenID::isFailure($form_html) ) 
            {
                throw new kException( core::i18n( 'w23_error_openid_cannot_locate_on_server' ) . $form_html->message );
            } 
            else 
            {
                die( '<html><body onLoad="document.getElementById(\''.$form_id.'\').submit();">'.core::i18n( 'wait' ).'<br /><br />'.$form_html.'</body></html>' );
                #die( '<html><body>'.core::i18n( 'wait' ).'<br /><br />'.$form_html.'</body></html>' );
            }
        }
    }
    
    ///////////////////////////////////////////////////////////////////////////
}
