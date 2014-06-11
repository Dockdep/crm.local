<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace
{
    /**
     * sendmail
     *
     * @author      Jane Bezmaternykh
     * @version     0.1.20131120
     */
    class sendmail extends \core
    {
        /////////////////////////////////////////////////////////////////////////////

        /**
         * sendmail::addCustomer()
         *
         * @author          Jane Bezmaternykh
         * @version         0.1.20131120
         *
         * @param           integer         $type
         * @param           array           $data
         * @return          string
         */
        public function addCustomer( $type, $data )
        {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir( ROOT_PATH.config::get( 'dirs/viewsDir' ) );

            $view->start();

            $view->setVar( 'data', $data );
            $view->render( 'sendmail', 'message_'.$type );

            $view->finish();

            switch( $type )
            {
                case 1: // customer order
                    $data_base =
                        [
                            'email_to'      => $data['email'],
                            'email_reply'   => $data['name'].' <'.$data['email'].'>',
                            'subject'       => 'Заказ на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                case 2: // admin order
                    $data_base =
                        [
                            'email_to'      => \config::get( 'global#email' ),
                            'email_reply'   => \config::get( 'global#email' ),
                            'subject'       => 'Новый заказ на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                case 3:   //customer registration
                    $data_base =
                        [
                            'email_to'      => $data['email'],
                            'email_reply'   => $data['name'].' <'.$data['email'].'>',
                            'subject'       => 'Регистрация на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                case 4:   //customer do not finish registration
                    $data_base =
                        [
                            'email_to'      => $data['email'],
                            'email_reply'   => $data['name'].' <'.$data['email'].'>',
                            'subject'       => 'Регистрация на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                case 5:   //customer registration
                    $data_base =
                        [
                            'email_to'      => $data['email'],
                            'email_reply'   => $data['name'].' <'.$data['email'].'>',
                            'subject'       => 'Регистрация на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                case 6:   //restore passwd
                    $data_base =
                        [
                            'email_to'      => $data['email'],
                            'email_reply'   => $data['name'].' <'.$data['email'].'>',
                            'subject'       => 'Смена пароля на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                case 7:   //callback email for admin
                    $data_base =
                        [
                            'email_to'      => \config::get( 'global#email' ),
                            'email_reply'   => \config::get( 'global#email' ),
                            'subject'       => 'Новое сообщение на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                case 8:   //callback email for admin
                    $data_base =
                        [
                            'email_to'      => $data['email'],
                            'email_reply'   => $data['name'].' <'.$data['email'].'>',
                            'subject'       => 'Ваше сообщение на сайте: '.\config::get( 'global#title' ),
                            'message_body'  => $view->getContent()
                        ];
                    break;

                default:
                    return false;
                    break;
            }

            //p($data_base['message_body'],1);

            //$email_to_customer      = $data['email'];
            $email_from             = 'Robot <robot@'.\config::get( 'global#domains/www' ).'>';
            //$email_reply_customer   = $data['name'].' <'.$data['email'].'>';


            $header =
                'From: '.$email_from."\n".
                'Reply-To: '.$data_base['email_reply']."\n".
                'Return-Path: '.$data_base['email_reply']."\n".
                'MIME-Version: 1.0'."\n".
                'Content-type: text/html; charset=UTF-8'."\n".
                'Content-Transfer-Encoding: 8bit'."\n".
                'X-Mailer: PHP/'.phpversion();

            if( mail( $data_base['email_to'], $data_base['subject'], $data_base['message_body'], $header, '-f '.$email_from ) )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * sendmail::addAdmin()
         *
         * @author          Jane Bezmaternykh
         * @version         0.1.20131120
         *
         * @param           integer         $type
         * @param           array           $data
         * @return          string
         */
        public function addAdmin( $type, $data )
        {
            $view_admin = new \Phalcon\Mvc\View();
            $view_admin->setViewsDir( ROOT_PATH.config::get( 'dirs/viewsDir' ) );

            $view_admin->start();

            $view_admin->setVar( 'data', $data );
            $view_admin->render( 'sendmail', 'message_'.$type );

            $view_admin->finish();

            $email_to_admin         = \config::get( 'global#email' );
            $email_from             = 'Robot <robot@'.\config::get( 'global#domains/www' ).'>';
            $email_reply_admin      = '<'.\config::get( 'global#email' ).'>';


            $header_admin =
                'From: '.$email_from."\n".
                'Reply-To: '.$email_reply_admin."\n".
                'Return-Path: '.$email_reply_admin."\n".
                'MIME-Version: 1.0'."\n".
                'Content-type: text/html; charset=UTF-8'."\n".
                'Content-Transfer-Encoding: 8bit'."\n".
                'X-Mailer: PHP/'.phpversion();

            if( mail( $email_to_admin, $data['subject_admin'], $view_admin->getContent(), $header_admin, '-f '.$email_from ) )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /////////////////////////////////////////////////////////////////////////////
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
