<?php

/**
 * socialAuth Class
 *
 * @pattent         Abstract Factory
 *
 * @author		Roman
 * @version         3.0.20111010
 */
class socialAuth
{
    ///////////////////////////////////////////////////////////////////////////
    
    private static $settings  = array(

        // OAuth 2.0 //////////////////////////////////////////////////////////
            
        'vkontakte' => array(
            'client_id'             => '4393164',
            'client_secret'         => 'Vb22Sn4L6oqduK737N5T',
            
            'authorize_uri'         => 'http://api.vkontakte.ru/oauth/authorize?scope=notify&response_type=code&display=page',
            'access_token_uri'      => 'https://api.vkontakte.ru/oauth/access_token',
            
            'profile_uri'           => 'https://api.vkontakte.ru/method/getProfiles?fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,photo_big',
            ),
            
        'facebook' => array(
            'client_id'             => '194750163930252', 
            'client_secret'         => 'b678a74a660f40754e70644339593edb',
            
            'authorize_uri'         => 'https://www.facebook.com/dialog/oauth?scope=user_about_me,user_birthday,user_location',
            'access_token_uri'      => 'https://graph.facebook.com/oauth/access_token',
            
            'profile_uri'           => 'https://graph.facebook.com/me',
            ),

        'google' => array(
            'client_id'             => '1075068890014.apps.googleusercontent.com',
            'client_secret'         => 'vy1jdGQy_QoA_hJ1itMXjaWm',
            
            'authorize_uri'         => 'https://accounts.google.com/o/oauth2/auth?scope=https://www.google.com/m8/feeds/&response_type=code',
            'access_token_uri'      => 'https://accounts.google.com/o/oauth2/token',
            
            'profile_uri'           => 'https://www.google.com/m8/feeds/contacts/default/full',
            ),
        );
        
    private static $redirect_uri  = 'http://semena.dev.artwebua.com.ua/customer_login/social/'; // + $mechanism
    
    ///////////////////////////////////////////////////////////////////////////

    /**
     * socialAuth::login()
     *
     * @author		Roman
     * @version         1.0.20111012
     *
     * @param           string      $mechanism
     * @return          array
     */
    public static function login( $mechanism )
    {
        ///////////////////////////////////////////////////////////////////////
        
        try
        {
            switch( $mechanism )
            {
                // OAuth v2.0 /////////////////////////////////////////////////
                    
                case 'vkontakte':
                    $auth = new socialAuthOAuth20Vkontakte( array_merge( self::$settings[$mechanism], array( 'redirect_uri' => self::$redirect_uri.$mechanism ) ) );
                    break;
                    
                case 'facebook':
                    $auth = new socialAuthOAuth20Facebook( array_merge( self::$settings[$mechanism], array( 'redirect_uri' => self::$redirect_uri.$mechanism ) ) );
                    break;
                    
                case 'google':
                    $auth = new socialAuthOAuth20Google( array_merge( self::$settings[$mechanism], array( 'redirect_uri' => self::$redirect_uri.$mechanism ) ) );
                    break;

                // OpenID /////////////////////////////////////////////////////
                
                case 'openid':
                    $auth = new socialAuthOpenIDCustom( isset($_GET['openid_url']) ? trim($_GET['openid_url']) : '' );
                    break;
                    
                case 'openid_finish':
                    $auth = new socialAuthOpenIDCustomFinish();
                    break;
                    
                case 'yandex':
                    $auth = new socialAuthOpenIDCustom( 'http://openid.yandex.ru' );
                    break;

                default:
                    return false;
                    break;
            }

            return $auth->init();
        }
        catch( kException $e )
        {
            $e->setFlashMessage( 'user_login_social' );
            
            #$e->sendErrorMail();
            
            core::redirect( '@user_login' );
            die();
        }
        
        ///////////////////////////////////////////////////////////////////////
        
        // set flash message        
        user::setFlash( 
            core::i18n( 'w23_error_unknown' ),
            'user_login_social'
            );

        core::redirect( '@user_login' );       
        die();
        
        ///////////////////////////////////////////////////////////////////////        
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * socialAuth::userLoginOrRegisterIfNotExists()
     *
     * @author		Roman
     * @version         1.1.20111007
     *
     * @param           array           $data
     * @return          array
     */
    public static function userLoginOrRegisterIfNotExists( $data = array() )
    {
        // check for exists
        $db = db::AutoExecute(
            'sp_user_select_check_login',
            array(
                'login'         => 'eid_'.md5(md5($data['login'])),
                ), 
            15, 0
            );
            
        if( !isset($db['error']) )
        {
            if( isset($db['data']['0']['unique']) && $db['data']['0']['unique']==0 )
            {
                // TODO
                // show form change user name
            }
            
            // register
            $db = db::AutoExecute(
                'sp_user_insert_social', 
                array(
                    'login'         => 'eid_'.md5(md5($data['login'])),
                    'email'         => $data['email'],
                    'name'          => $data['name'],
                    'name_unique'   => common::simplifyString( $data['name'] ),
                    'bithday'       => $data['bithday'],
                    'gender'        => $data['gender'],
                    'region_id'     => $data['region_id'],
                    'about_me'      => $data['about_me'],
                    'interests'     => $data['interests'],
                    'vote'          => $data['vote'],
                    'contact_icq'   => $data['contact_icq'],
                    'options'       => $data['options'],
                    #'avatar'        => 'avatar_filename' 
                    'user_ip'       => common::getIP(),
                    )
                );   
                
            if( !isset($db['error']) )
            {
                if( !empty($db['data']) && isset($db['data']['1']['id']) )
                {
                    // AVATAR /////////////////////////////////////////////////
                                    
                    $user_id = $db['data']['1']['id'];
                    $filename = common2::strconvert( $user_id );
                    
                    $filepath = sfConfig::get('sf_root_dir').'/storage_units/1/avatars/'.fs::getDepthFilePath( $filename, 4, true ).$filename.'/';                                
                    fs::createDir( $filepath );  
                    
                    $filename .= '.png';

                    // generate identicon and save to avatar path                
                    identicon::save( md5(md5(sha1( 'censor_user_'.$user_id ))), 100, $filepath.$filename );                                
                    
                    // update user avatar
                    $db2 = db::AutoExecute(
                        'sp_user_update_avatar',
                        array(
                            'user_id'   => $user_id,
                            'avatar'    => $filename,
                            )
                        );

                    // drop and refresh user data
                    common::dropDependences( 'user', array( 'user_id' => $user_id ) );
                        
                    // DATA ///////////////////////////////////////////////////

                    return $db['data']['1'];
                }
            }
            else
            {
                //user::setFlash( core::i18n( 'w23_error_403', null, 'widgets' ), 'user_login' );
                user::setFlash( core::i18n( 'w23_error_500', array( '{{user_email}}' => $data['email'] ), 'widgets', true ), 'user_login' );
            }
        }
        else
        {
            // login            
            $db = db::AutoExecute(
                'sp_user_select',
                array(
                    'login'         => 'eid_'.md5(md5($data['login'])),
                    'passwd'        => '00000000000000000000000000000000',
                    'user_ip'       => common::getIP(),
                    ), 
                15, 0
                );
                
            if( !isset($db['error']) )
            {
                if( !empty($db['data']) && isset($db['data']['1']['id']) )
                {
                    return $db['data']['1'];
                }
            }
            else
            {
                user::setFlash( core::i18n( 'w23_error_403', null, 'widgets' ), 'user_login' );
            }
        }
       
        return false;
    }
    
    ///////////////////////////////////////////////////////////////////////////
    
}
