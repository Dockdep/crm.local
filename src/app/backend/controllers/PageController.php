<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace controllers;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class PageController extends \Phalcon\Mvc\Controller
{
    ///////////////////////////////////////////////////////////////////////////

    public function indexAction()
    {
        if( !$this->session->get('isAdminAuth') )
        {
            return $this->response->redirect([ 'for' => 'admin_login' ]);
        }

        p('hello',1);

        $this->view->setVars([

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function loginAction()
    {
        if( $this->session->get('isAdminAuth') )
        {
            return $this->response->redirect([ 'for' => 'admin_homepage' ]);
        }

        if( $this->request->isPost() )
        {
            $registration['email']              = $this->request->getPost('email', 'email', NULL );
            $passwd                             = $this->request->getPost('passwd', 'string', NULL );
            $registration['passwd']             = $this->common->hashPasswd( $passwd );

            //p($registration,1);

            switch( $this->models->getAdmins()->adminLogin( $registration ) )
            {
                case 1:
                    // OK
                    // redirect
                    return $this->response->redirect([ 'for' => 'admin_homepage' ]);
                    break;

                case -1:
                default:
                    $this->flash->error('Неправильный логин или пароль');
                    return $this->response->redirect([ 'for' => 'admin_login' ]);
                    break;

                case 2: // admin with status 0
                default:
                    $this->flash->notice('Ваш статус еще не подтвержден');
                    return $this->response->redirect([ 'for' => 'admin_login' ]);
                    break;
            }

        }

        //p('hello',1);

        $this->view->setVars([

        ]);
    }

    ///////////////////////////////////////////////////////////////////////////

    public function adminLogoutAction()
    {
        // unauthorize user
        $this->session->remove('isAdminAuth');
        $this->session->remove('adminId');
        //$this->session->destroy();

        return $this->response->redirect([ 'for' => 'admin_login' ]);
    }

    ///////////////////////////////////////////////////////////////////////////
}
