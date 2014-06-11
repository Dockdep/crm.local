<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace controllers;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class CustomerController extends \Phalcon\Mvc\Controller
{
    ///////////////////////////////////////////////////////////////////////////

    public function customerLoginAction( $lang_id = '1' )
    {
        if( $this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'cabinet' ]);
        }

        $this->session->set( 'return_url', 'cabinet' );

        if( $this->request->isPost() )
        {
            $registration['email']              = $this->request->getPost('email', 'email', NULL );
            $passwd                             = $this->request->getPost('passwd', 'string', NULL );
            $registration['passwd']             = $this->common->hashPasswd( $passwd );

            //p($registration,1);

            switch( $this->models->getCustomers()->customerLogin( $registration ) )
            {
                case 1:
                    // OK
                    // redirect
                    return $this->response->redirect([ 'for' => 'cabinet' ]);
                    break;

                case -1:
                default:
                    $this->flash->error('Невірний логін або пароль');
                    return $this->response->redirect([ 'for' => 'customer_login' ]);
                    break;

                case 2: // user with status 0
                default:
                    //$this->flash->error('You do not complete your registration. To confirm your registration, please, click <a href="/resend_confirm_message">here</a>');
                    $this->flash->success('Будь ласка, змінить пароль');
                    $this->session->set( 'customer_email', $registration['email'] );
                    return $this->response->redirect([ 'for' => 'finish_registration' ]);
                    break;

            }

        }

        $this->view->setVars([
            'lang_id' => $lang_id
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function customerLoginSocialAction( $mechanism )
    {
        if( isset($_GET['code']) )
        {
            $result = $this->social->authorizeWithSocial( $mechanism, $_GET['code'] );

            if( $result )
            {
                return $this->response->redirect([ 'for' => $this->session->get('return_url') ]);
            }
            else
            {
                $this->flash->error('Під час авторизації сталася помилка. Спробуйте ще раз пізніше');
                return $this->response->redirect([ 'for' => 'customer_login' ]);
            }
        }
        elseif( isset($_GET['error']) )
        {
            if( trim($_GET['error'])=='access_denied' )
            {
                $error_message = 'Ви відмовились від авторизаціі через соціальні мережі';         // Ошибка авторизации: Пользователь отм
            }
            else
            {
                $error_message = trim($_GET['error']);
            }

            $this->flash->error($error_message);
            return $this->response->redirect([ 'for' => $this->session->get('return_url') ]);
        }
    }

    ///////////////////////////////////////////////////////////////////////////

    public function customerLogoutAction()
    {
        // unauthorize user
        $this->session->remove('isAuth');
        $this->session->remove('id');
        //$this->session->destroy();

        return $this->response->redirect([ 'for' => 'homepage' ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function registrationAction( $lang_id = '1' )
    {
        if( $this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'cabinet' ]);
        }

        $err = 0;

        if( $this->request->isPost() )
        {
            $registration['name']               = $this->request->getPost('registration_name', 'string', NULL );
            $registration['email']              = $this->request->getPost('registration_email', 'email', NULL );
            $registration['passwd']             = $this->request->getPost('registration_passwd', 'string', NULL );
            $registration['confirm_passwd']     = $this->request->getPost('registration_confirm_passwd', 'string', NULL );

            foreach( $registration as $o )
            {
                if( empty($o) )
                {
                    $err = 1;
                }
            }

            if( $registration['confirm_passwd'] === $registration['passwd'] && empty( $err ) )
            {
                $registration['passwd']          = $this->common->hashPasswd( $registration['passwd'] );

                if( $registration['confirm_key'] = $this->models->getCustomers()->registrateCustomer( $registration ) )
                {
                    $this->sendmail->addCustomer( 5, $registration );
                    $this->flash->success( 'Для того, щоб завершити реєстрацію, будь ласка перейдіть по ссилці, яку Ви отримали на Ваш email' );
                    return $this->response->redirect([ 'for' => 'registration_canceled' ]);
                }
                else
                {
                    $this->session->set( 'customer_email', $registration['email'] );
                    $this->flash->error( 'Такий email в базі вже існує.&nbsp;<a href="'.$this->url->get([ 'for' => 'restore_passwd' ]).'">Забули пароль?</a>' );
                    return $this->response->redirect([ 'for' => 'registration' ]);
                }
            }

        }

        $this->view->setVars([
            'lang_id' => $lang_id
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function confirmRegistrationAction( $confirm_key )
    {
        switch( $this->models->getCustomers()->checkCustomerByConfirmKey( $confirm_key ) )
        {
            case 1:
                // OK
                // redirect
                $this->flash->success('Ви успішно закінчили реєстрацію');
                return $this->response->redirect([ 'for' => 'cabinet' ]);
                break;

            case 0:
            default:
                $this->flash->error('Користувача с таким email в базі не існує');
                //return $this->response->redirect([ 'for' => 'customer_login' ]);
                break;

        }

        $this->view->setVars([

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function restorePasswdAction()
    {
        $lang_id = 1;

        if( $this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'cabinet' ]);
        }

        if( $this->request->isPost() )
        {
            $email                  = $this->request->getPost('email', 'email', NULL );
            $registration['email']  = filter_var( $email, FILTER_VALIDATE_EMAIL );

            //p($email,1);

            if( !empty( $registration['email'] ) )
            {
                if( $confirm_key = $this->models->getCustomers()->restorePasswd( $email ) )
                {
                    $registration['confirm_key']    = $confirm_key['confirm_key'];
                    $registration['name']           = $confirm_key['name'];
                    $this->sendmail->addCustomer( 6, $registration );

                    $this->flash->success('Для того, щоб змінити пароль, перейдіть по ссилці, яка прийде Вам на email');
                    return $this->response->redirect([ 'for' => 'restore_passwd' ]);
                }
                else
                {
                    $this->flash->error('Користувача с таким email в базі не існує');
                    return $this->response->redirect([ 'for' => 'restore_passwd' ]);
                }
            }
            else
            {
                $this->flash->error( 'Введіть валідний email' );
                return $this->response->redirect([ 'for' => 'restore_passwd' ]);
            }

        }

        $this->view->setVars([
            'lang_id' => $lang_id
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function resetPasswdAction( $confirm_key, $lang_id = '1' )
    {
        if( $this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'cabinet' ]);
        }

        if( $this->request->isPost() )
        {
            $passwd                             = $this->request->getPost('passwd', 'string', NULL );
            $confirm_passwd                     = $this->request->getPost('confirm_passwd', 'string', NULL );

            if( $passwd === $confirm_passwd )
            {
                $registration['passwd']          = $this->common->hashPasswd( $passwd );
                $registration['confirm_key']     = $confirm_key;

                switch( $this->models->getCustomers()->resetPasswd( $registration ) )
                {
                    case 1:
                        // OK
                        // redirect
                        $this->flash->success('Ви успішно змінили пароль');
                        return $this->response->redirect([ 'for' => 'cabinet' ]);
                        break;

                    case 0:
                    default:
                        $this->flash->error('Користувача с таким email в базі не існує');
                        return $this->response->redirect([ 'for' => 'reset_passwd' ]);
                        break;

                }
            }

        }

        $this->view->pick('customer/finishRegistration');

        $this->view->setVars([
            'lang_id'           => $lang_id,
            'breadcrambs_title' => 'Зміна паролю'
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function finishRegistrationAction( $lang_id = '1' )
    {
        if( $this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'cabinet' ]);
        }

        if( $this->request->isPost() )
        {
            $passwd                             = $this->request->getPost('passwd', 'string', NULL );
            $confirm_passwd                     = $this->request->getPost('confirm_passwd', 'string', NULL );

            if( $passwd === $confirm_passwd )
            {
                $registration['passwd']          = $this->common->hashPasswd( $passwd );
                $registration['email']           = $this->session->get('customer_email');

                if( $this->models->getCustomers()->finishRegistration( $registration ) )
                {
                    $this->flash->success( 'Ви успішно завершили реєстрацію' );
                    return $this->response->redirect([ 'for' => 'cabinet' ]);
                }
            }

        }

        $this->view->setVars([
            'lang_id' => $lang_id
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function registrationCancelAction()
    {
        $this->view->setVars([

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function cabinetAction( $lang_id = '1' )
    {
        if( !$this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'customer_login' ]);
        }

        $customer   = $this->models->getCustomers()->getCustomer( $this->session->get('id') );
        $orders     = $this->models->getOrders()->getOrdersByCustomerId( $this->session->get('id') );

        //p($orders,1);

        $month_names = ['Січень','Лютий','Березень','Квітень','Травень','Червень','Липень','Серпень','Вересень','Жовтень','Листопад','Грудень'];

        if( $this->request->isPost() )
        {
            $customer_edit['id']                = $this->session->get('id');
            $customer_edit['name']              = $this->request->getPost('order_name', 'string', NULL );
            $customer_edit['phone']             = $this->request->getPost('order_phone', 'string', NULL );
            $customer_edit['city']              = $this->request->getPost('order_city', 'string', NULL );
            $customer_edit['address']           = $this->request->getPost('order_address', 'string', NULL );
            //$customer_edit['delivery']          = $this->request->getPost('order_delivery', 'string', NULL );
            //$customer_edit['pay']               = $this->request->getPost('order_pay', 'string', NULL );

            foreach( $customer_edit as $o )
            {
                if( strlen($o) == 0 )
                {
                    $err = 1;
                    //$err = 0;
                }
            }

            if( empty( $err ) )
            {
                $customer_edit['email']             = $this->request->getPost('order_email', 'email', NULL );

                $year   = $this->request->getPost('date_birth_year', 'int', '1970' );
                $month  = $this->request->getPost('date_birth_month', 'int', '1970' );
                $day    = $this->request->getPost('date_birth_day', 'int', '1970' );

                $customer_edit['birth_date'] =
                    !empty( $year ) && !empty( $month ) && !empty( $day )
                    ?
                        $year.'-'.$month.'-'.$day
                    :
                        NULL;
                $customer_edit_get_info             = $this->request->getPost('order_get_info', 'string', NULL );
                $customer_edit['subscribed']        = empty( $customer_edit_get_info ) ? 0 : 1;
                //$customer_edit['comments']          = $this->request->getPost('order_comments', 'string', NULL );

                if( $this->models->getCustomers()->editCustomer( $customer_edit ) )
                {
                    $this->flash->success( 'Ви успішно відредагували свій профайл' );
                    return $this->response->redirect([ 'for' => 'cabinet' ]);
                }
                else
                {
                    $this->flash->error( 'Під час редагування профайлу сталася помилка. Спробуйте пізніше.' );
                }
            }
        }

        $this->view->setVars([
            'customer'      => $customer['0'],
            'month_names'   => $month_names,
            'lang_id'       => $lang_id,
            'orders'        => $orders,

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function listOrdersAction( $order_id, $lang_id = '1' )
    {
        if( !$this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'customer_login' ]);
        }
        p($order_id); // без этого не отображается страница
        $orders     = $this->models->getOrders()->getOrdersByCustomerId( $this->session->get('id') );
        $order      = $this->models->getOrders()->getOrdersByOrderId( $order_id, $lang_id );

        $orders_with_id = [];

        $month_names = ['Січня','Лютого','Березня','Квітня','Травня','Червня','Липеня','Серпня','Вересня','Жовтня','Листопада','Грудня'];

        if( !empty( $orders ) )
        {
            foreach( $orders as $o )
            {
                if( $o['id'] == $order_id )
                {
                    $orders_with_id['date'] =
                        date('d', strtotime($o['created_date'])).' '.
                        $month_names[date('m', strtotime($o['created_date']))-1].' '.
                        date('Y', strtotime($o['created_date']));

                    $orders_with_id['delivery'] = \config::get( 'global#delivery/'.$o['delivery'] );
                    $orders_with_id['status']   = \config::get( 'global#status/'.$lang_id.'/'.$o['status'] );
                }
            }
        }

        $total_count = 0;

        if( !empty( $order ) )
        {
            foreach( $order as $k => $o )
            {
                $order[$k]['link']          = $this->url->get([ 'for' => 'item', 'type' => $o['type_alias'], 'subtype' => $o['subtype_alias'], 'group_alias' => $o['group_alias'], 'item_id' => $o['item_id'] ]);
                $order[$k]['image']         = !empty( $o['cover'] ) ? $this->storage->getPhotoUrl( $o['cover'], 'avatar', '200x' ) : '/images/packet.jpg';
                $order[$k]['total_count']   = $o['price2']*$o['item_count'];

                $total_count += $o['price2']*$o['item_count'];
            }
        }

        $this->view->setVars([
            'lang_id'           => $lang_id,
            'order'             => $order,
            'orders'            => $orders,
            'order_id'          => $order_id,
            'orders_with_id'    => $orders_with_id,
            'total_count'       => $total_count,

        ]);

        //$this->view->render("customer", "listOrders");

    }

    ///////////////////////////////////////////////////////////////////////////

    public function changeCustomerPasswdAction( $lang_id = '1' )
    {
        if( !$this->session->get('isAuth') )
        {
            return $this->response->redirect([ 'for' => 'customer_login' ]);
        }

        if( $this->request->isPost() )
        {
            $customer_edit_passwd['id'] = $this->session->get('id');
            $previous_passwd            = $this->request->getPost('previous_passwd', 'string', NULL );
            $passwd                     = $this->request->getPost('passwd', 'string', NULL );
            $confirm_passwd             = $this->request->getPost('confirm_passwd', 'string', NULL );

            if( $confirm_passwd === $passwd )
            {
                $customer_edit_passwd['previous_passwd']  = $this->common->hashPasswd( $previous_passwd );
                $customer_edit_passwd['passwd']           = $this->common->hashPasswd( $passwd );

                switch( $this->models->getCustomers()->editCustomerPasswd( $customer_edit_passwd ) )
                {
                    case 1:
                        // OK
                        // redirect
                        $this->flash->success( 'Ви успішно відредагували свій пароль' );
                        return $this->response->redirect([ 'for' => 'cabinet' ]);
                        break;

                    case -1:
                    default:
                        $this->flash->error('Невірний попередній пароль');
                        return $this->response->redirect([ 'for' => 'change_customer_passwd' ]);
                        break;

                }

            }
        }

        $this->view->setVars([

            'lang_id'       => $lang_id

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////
}