<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace
{
    /**
     * social
     *
     * @author      Jane Bezmaternykh
     * @version     1.0.20140602
     */
    class social extends \core
    {
        /////////////////////////////////////////////////////////////////////////////

        private $settings  = [

            // OAuth 2.0 //////////////////////////////////////////////////////////

            'vkontakte' =>
            [
                'client_id'             => '4393164',
                'client_secret'         => 'Vb22Sn4L6oqduK737N5T',

                'authorize_uri'         => 'http://api.vkontakte.ru/oauth/authorize?scope=notify&response_type=code&display=page',
                'access_token_uri'      => 'https://api.vkontakte.ru/oauth/access_token',

                'profile_uri'           => 'https://api.vkontakte.ru/method/getProfiles?fields=uid,first_name,last_name,screen_name,sex,bdate',
            ],

            'facebook' =>
            [
                'client_id'             => '665897706815356',
                'client_secret'         => 'd0c02c90c3b36a5ced060b6691684633',

                'authorize_uri'         => 'https://www.facebook.com/dialog/oauth?scope=user_about_me,user_birthday&response_type=code',
                'access_token_uri'      => 'https://graph.facebook.com/oauth/access_token',

                'profile_uri'           => 'https://graph.facebook.com/me',
            ],

            'google' =>
            [
                'client_id'             => '431319763872-q5t39j51apcl6fspger3bc7m5q6iofio.apps.googleusercontent.com',
                'client_secret'         => 'J2hwT-vX3WMKgxeAnku549xJ',

                'authorize_uri'         => 'https://accounts.google.com/o/oauth2/auth?scope=https://www.google.com/m8/feeds/&response_type=code',
                'access_token_uri'      => 'https://accounts.google.com/o/oauth2/token',

                'profile_uri'           => 'https://www.google.com/m8/feeds/contacts/default/full',
            ],
        ];

        /////////////////////////////////////////////////////////////////////////////

        private $redirect_uri  = 'http://semena.dev.artwebua.com.ua/customer_login/social/'; // + $mechanism

        /////////////////////////////////////////////////////////////////////////////

        /**
         * social::getParams()
         *
         * @author      Jane Bezmaternykh
         * @version     1.0.20140602
         *
         * @param     string      $mechanism
         * @return    boolean
         */

        public function createUrl( $mechanism )
        {
            return
                $this->settings[$mechanism]['authorize_uri'].'&'.
                'client_id='.$this->settings[$mechanism]['client_id'].'&'.
                'redirect_uri='.$this->redirect_uri.$mechanism;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * social::getParams()
         *
         * @author      Jane Bezmaternykh
         * @version     1.0.20140602
         *
         * @param     string      $mechanism
         * @param     string      $code
         * @return    boolean
         */

        public function authorizeWithSocial( $mechanism, $code )
        {
            switch( $mechanism )
            {
                // OAuth v2.0 /////////////////////////////////////////////////

                case 'vkontakte':
                    $auth = $this->authorizeWithVkontakte( $mechanism, $code );
                    break;

                case 'facebook':
                    $auth = $this->authorizeWithFacebook( $mechanism, $code );
                    break;

                case 'google':
                    $auth = $this->authorizeWithGoogle( $mechanism, $code );
                    break;

                default:
                    return false;
                    break;
            }

            return $auth;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * social::authorizeWithVkontakte()
         *
         * @author      Jane Bezmaternykh
         * @version     1.0.20140602
         *
         * @param     string      $mechanism
         * @param     string      $code
         * @return    boolean
         */

        public function authorizeWithVkontakte( $mechanism, $code )
        {
            $params =
                [
                    'client_id'     => $this->settings[$mechanism]['client_id'],
                    'client_secret' => $this->settings[$mechanism]['client_secret'],
                    'code'          => $code,
                    'redirect_uri'  => $this->redirect_uri.$mechanism
                ];

            $token = json_decode(file_get_contents($this->settings[$mechanism]['access_token_uri']. '?' . urldecode(http_build_query($params))), true);

            if (isset($token['access_token']))
            {
                $params = array(
                    'uids'         => $token['user_id'],
                    'access_token' => $token['access_token']
                );

                //p($this->settings[$mechanism]['profile_uri']. '?' . urldecode(http_build_query($params)),1);

                $userInfo = json_decode(file_get_contents($this->settings[$mechanism]['profile_uri']. '?' . urldecode(http_build_query($params))), true);

                if (isset($userInfo['response'][0]['uid']))
                {
                    $userInfo = $userInfo['response'][0];
                    $result = true;
                }
            }

            //return $userInfo;

            return
                $this->userLoginOrRegisterIfNotExists(
                    array(
                        'email'         => ( isset($userInfo['screen_name']) ? $userInfo['screen_name'] : 'id'.$userInfo['uid'] ).'@vkontakte.ru',
                        'name'          => trim( ( isset($userInfo['first_name']) ? $userInfo['first_name'] : '' ).' '.( isset($userInfo['last_name']) ? $userInfo['last_name'] : '' ) ),
                        'bithday'       => ( isset($userInfo['bdate']) ? date( 'Y-m-d', strtotime($userInfo['bdate']) ) : null ),
                    )
                );
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * social::authorizeWithFacebook()
         *
         * @author      Jane Bezmaternykh
         * @version     1.0.20140602
         *
         * @param     string      $mechanism
         * @param     string      $code
         * @return    boolean
         */

        public function authorizeWithFacebook( $mechanism, $code )
        {
            //p($code,1);
            @$response = file_get_contents($this->settings[$mechanism]['access_token_uri'].
                '?client_id='.$this->settings[$mechanism]['client_id'].
                '&client_secret='.$this->settings[$mechanism]['client_secret'].
                '&redirect_uri='.urlencode($this->redirect_uri.$mechanism).
                '&code='.trim( $code )
            );
            //p($response,1);
            $token = null;

            parse_str($response, $token);

            if( isset( $token['access_token'] ) )
            {
                $userInfo = json_decode(
                    file_get_contents(
                        $this->settings[$mechanism]['profile_uri'].'?access_token='.trim( $token['access_token'] )
                    ), true
                );
            }

            //p($userInfo,1);

            if( !empty( $userInfo ) )
            {
                return
                    $this->userLoginOrRegisterIfNotExists(
                        array(
                            'email'     => ( isset($userInfo['username']) ? $userInfo['username'] : 'id'.$userInfo['id'] ).'@facebook.com',
                            'name'      => ( isset($userInfo['name']) ? $userInfo['name'] : ( isset($userInfo['username']) ? $userInfo['username'] : 'id'.$userInfo['id'] ) ),
                            'bithday'   => ( isset($userInfo['birthday']) ? date( 'Y-m-d', strtotime($userInfo['birthday']) ) : null ),
                        )
                    );
            }
            else
            {
                $this->getDi()->get('flash')->error('Під час авторизації сталася помилка. Спробуйте ще раз пізніше');
                return $this->getDi()->get('response')->redirect([ 'for' => 'customer_login' ]);
            }
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * social::authorizeWithGoogle()
         *
         * @author      Jane Bezmaternykh
         * @version     1.0.20140602
         *
         * @param     string      $mechanism
         * @param     string      $code
         * @return    boolean
         */

        public function authorizeWithGoogle( $mechanism, $code )
        {
            $result = false;

            $params =
                [
                    'client_id'     => $this->settings[$mechanism]['client_id'],
                    'client_secret' => $this->settings[$mechanism]['client_secret'],
                    'code'          => $code,
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => $this->redirect_uri.$mechanism
                ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->settings[$mechanism]['access_token_uri']);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);

            $token = json_decode($result, true);

            //p($token,1);

            if(isset($token['access_token']))
            {
                $ch = curl_init( $this->settings[$mechanism]['profile_uri'].'?access_token='.trim( $token['access_token'] ) );

                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

                $data = curl_exec($ch);

                curl_close($ch);

                $doc = simplexml_load_string($data);
                //p($doc,1);

                if( $doc )
                {
                    $userInfo = array(
                        'id'            => strval($doc->id),
                        'login'         => strval($doc->id),
                        'email'         => strval($doc->author->email),
                        'email_fake'    => strval($doc->id).'@google.com',
                        'name'          => strval($doc->author->name),
                    );
                }
            }



            //return $userInfo;

            return
                $this->userLoginOrRegisterIfNotExists(
                    array(
                        'email'         => $userInfo['email'],
                        'name'          => $userInfo['name'],
                        'bithday'       => ( isset($userInfo['bdate']) ? date( 'Y-m-d', strtotime($userInfo['bdate']) ) : null ),
                    )
                );
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * social::userLoginOrRegisterIfNotExists()
         *
         * @author      Jane Bezmaternykh
         * @version     1.0.20140602
         *
         * @param     string      $userInfo
         * @return    boolean
         */

        public function userLoginOrRegisterIfNotExists( $userInfo )
        {
            //return $userInfo; die();
            $passwd_                    = $this->getDI()->get('common')->generatePasswd(10);
            $userInfo['passwd']         = $this->getDI()->get('common')->hashPasswd( $passwd_ );

            return $this->getDi()->get('models')->getCustomers()->LoginOrRegisterSocial($userInfo);

            //p($userInfo,1);
        }

        /////////////////////////////////////////////////////////////////////////////
    }

}