<?php
///////////////////////////////////////////////////////////////////////////////

define( 'START_TIME',           microtime(true) );
define( 'ROOT_PATH',            realpath(__DIR__.'/../src').'/' );
define( 'STORAGE_PATH',         realpath(__DIR__.'/../storage').'/' );

///////////////////////////////////////////////////////////////////////////////

# IS_PRODUCTION
define( 'IS_PRODUCTION',        false );

///////////////////////////////////////////////////////////////////////////////

try
{
    ///////////////////////////////////////////////////////////////////////////

    if( IS_PRODUCTION )
    {
        error_reporting(0);

        // blank P-functions
        if( function_exists('p')===false ) { function p($var = '', $die_color = 2) {} }
        if( function_exists('z')===false ) { function z($var = '', $die_color = 2, $get_param = 'z') {} }
        if( function_exists('j')===false ) { function j($var = '', $label = '') {} }
        if( function_exists('b')===false ) { function b($label = 1, $use_p = true, $p_color = 2) {} }
        if( function_exists('info')===false ) { function info($return = false, $with_html = true) {} }
        if( function_exists('f')===false ) { function f( $var = '', $type = 2, $label = null ) {} }
        if( function_exists('fpe')===false ) { function fpe( $str, $color = 2, $append_newline = true ) {} }
    }
    else
    {
        error_reporting(-1);
        ini_set('display_errors', 1);

        // P-functions
        require( ROOT_PATH.'lib/p.php' );
    }

    ///////////////////////////////////////////////////////////////////////////

    require( ROOT_PATH.'lib/config.php' );

    config::setApp( 'backend' );

    ///////////////////////////////////////////////////////////////////////////

    $loader = new \Phalcon\Loader();

    $loader->registerDirs([
        ROOT_PATH.config::get( 'dirs/controllersDir' ),
        ROOT_PATH.config::get( 'dirs/appLibrariesDir' ),
        ROOT_PATH.config::get( 'dirs/librariesDir' ),
        ROOT_PATH.config::get( 'dirs/modelsDir' ),
    ])->register();

    $loader->registerNamespaces([
        'controllers'       => ROOT_PATH.config::get( 'dirs/controllersDir' ),
        'backend\lib'      => ROOT_PATH.config::get( 'dirs/appLibrariesDir' ),
        'lib'               => ROOT_PATH.config::get( 'dirs/librariesDir' ),
        'models'            => ROOT_PATH.config::get( 'dirs/modelsDir' ),
    ])->register();

    ///////////////////////////////////////////////////////////////////////////

    $di = new \Phalcon\DI();
    //$di = new \Phalcon\DI\FactoryDefault();

    ///////////////////////////////////////////////////////////////////////////

    // request

    $di->set( 'request', function()
    {
        return new \Phalcon\Http\Request();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // response

    $di->set( 'response', function()
    {
        return new \Phalcon\Http\Response();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // router

    $di->set( 'router', function()
    {
        //////////////////////////////////////////////////////////////////////	

        $router = new \Phalcon\Mvc\Router();

        //////////////////////////////////////////////////////////////////////        

        $router->removeExtraSlashes( true );

        //////////////////////////////////////////////////////////////////////

        $router->add
            (
                '/',
                [
                    'controller'    => 'page',
                    'action'        => 'index',
                ]
            )
            ->setName( 'admin_homepage' );


        $router->add
            (
                '/login',
                [
                    'controller'    => 'page',
                    'action'        => 'login',
                ]
            )
            ->setName( 'admin_login' );

        $router->add
            (
                '/logout',
                [
                    'controller'    => 'page',
                    'action'        => 'adminLogout',
                ]
            )
            ->setName( 'admin_login' );

        ///////////////////////////////////////////////////////////////////////


        return $router;
    }, true );

    ///////////////////////////////////////////////////////////////////////////	

    // url

    $di->set( 'url', function()
    {
        $url = new \Phalcon\Mvc\Url();

        $url->setBaseUri('/');

        return $url;
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // cache

    $di->set( 'cache', function()
    {
        $cache = new \Phalcon\Cache\Frontend\Data([
            'lifetime' => 60,
        ]);

        return new \Phalcon\Cache\Backend\Apc( $cache );
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // i18n

    $di->set( 'i18n', function()
    {
        return new \Phalcon\Translate\Adapter\NativeArray([
            'content' => require( ROOT_PATH.config::get( 'dirs/messagesDir' ).'ru.php' )
        ]);
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // database

    $di->set( 'database', function()
    {
        $config =
            [
                'host'      => config::get('global#database/server'),
                'username'  => config::get('global#database/user'),
                'password'  => config::get('global#database/passwd'),
                'dbname'    => config::get('global#database/db'),
                'schema'    => 'public',
            ];

        $database       = new \Phalcon\Db\Adapter\Pdo\Postgresql( $config );

        return $database;

    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // db

    $di->set( 'db', function()
    {
        return new \db();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    //models

    $di->set( 'models', function()
    {
        return new \models();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // redis
    $di->set( 'redis', function()
    {
        return new \re();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // etc

    $di->set( 'etc', function()
    {
        return new \etc();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // profiler

    $di->set( 'profiler', function()
    {
        return new \profiler();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // common

    $di->set( 'common', function()
    {
        return new \common();
    }, true );

    ///////////////////////////////////////////////////////////////////////////	

    // storage

    $di->set( 'storage', function()
    {
        return new \storage();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // user

    $di->set( 'user', function()
    {
        return new \user();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // sendmail

    $di->set( 'sendmail', function()
    {
        return new \sendmail();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // forapprove

    $di->set( 'forapprove', function()
    {
        return new \forapprove();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // session

    $di->set( 'session', function()
    {
        $session = new \Phalcon\Session\Adapter\Files();
        $session->start();

        return $session;
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // flash

    $di->set( 'flash', function()
    {
        return new \Phalcon\Flash\Session();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // cookies

    $di->set( 'cookies', function ()
    {
        $cookies = new \Phalcon\Http\Response\Cookies();
        $cookies->useEncryption(false);

        return $cookies;
    });

    ///////////////////////////////////////////////////////////////////////////

    // recaptchalib

    $di->set( 'recaptchalib', function()
    {
        return new \recaptchalib();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // view 

    $di->set( 'view', function()
    {
        $view = new \Phalcon\Mvc\View();

        $view->setViewsDir( ROOT_PATH.config::get( 'dirs/viewsDir' ) );

        $view->registerEngines([
            '.php' => '\Phalcon\Mvc\View\Engine\Php'
        ]);

        return $view;
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // filter

    $di->set( 'filter', function()
    {
        $filter = new \Phalcon\Filter();

        $filter->add( 'string', function($value)
        {
            return trim( filter_var( $value, FILTER_SANITIZE_STRING ) );
        });

        $filter->add( 'int', function($value)
        {
            return intval( preg_replace( '#[^0-9]#', '', $value ) );
        });

        $filter->add( 'float', function($value)
        {
            return trim( filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT ) );
        });

        return $filter;
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    $di->set( 'dispatcher', function()
    {
        // Create/Get an EventManager
        $eventsManager = new \Phalcon\Events\Manager();

        // Attach a listener
        $eventsManager->attach( 'dispatch', function($event, $dispatcher, $exception)
        {
            // The controller exists but the action not
            if ($event->getType() == 'beforeNotFoundAction')
            {
                $dispatcher->forward([
                    'controller'    => 'page',
                    'action'        => 'error404'
                ]);

                return false;
            }

            // Alternative way, controller or action doesn't exist
            if ($event->getType() == 'beforeException')
            {
                switch ($exception->getCode())
                {
                    case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward([
                            'controller'    => 'page',
                            'action'        => 'error404'
                        ]);

                        return false;
                }
            }
        });

        $dispatcher = new \Phalcon\Mvc\Dispatcher();

        $dispatcher->setDefaultNamespace('controllers');

        // Bind the EventsManager to the dispatcher
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;

    }, true );

    ///////////////////////////////////////////////////////////////////////////

    $application = new \Phalcon\Mvc\Application();
    $application->setDI($di);

    ///////////////////////////////////////////////////////////////////////////

    // check for user's timezone from jstz
    if( $di->get('cookies')->has('tz') )
    {
        $timezone = preg_replace( '#[^a-z\/]#i', '', $di->get('cookies')->get('tz')->getValue() );

        if( !empty($timezone) )
        {
            // set user's timezone
            date_default_timezone_set( $timezone );
        }
    }

    ///////////////////////////////////////////////////////////////////////////

    // init user
    $di->get('user')->init();

    ///////////////////////////////////////////////////////////////////////////

    die( $application->handle()->getContent() );

    ///////////////////////////////////////////////////////////////////////////

}
catch (Phalcon\Exception $e)
{
    if( IS_PRODUCTION )
    {
        // TODO
    }
    else
    {
        echo( ob_get_flush() );

        if( class_exists('exceptions') )
        {
            $z = new \exceptions();
            return $z->handle($e);
        }
        else
        {
            die( '[Phalcon\Exception] '.$e->getMessage() );
        }
    }
}
catch (PDOException $e)
{
    if( IS_PRODUCTION )
    {
        // TODO
    }
    else
    {
        echo( ob_get_flush() );

        if( class_exists('exceptions') )
        {
            $z = new \exceptions();
            return $z->handle($e);
        }
        else
        {
            die( '[PDOException] '.$e->getMessage() );
        }
    }
}
catch (Exception $e)
{
    if( IS_PRODUCTION )
    {
        // TODO
    }
    else
    {
        echo( ob_get_flush() );

        if( class_exists('exceptions') )
        {
            $z = new \exceptions();
            return $z->handle($e);
        }
        else
        {
            die( '[Exception] '.$e->getMessage() );
        }
    }
}

///////////////////////////////////////////////////////////////////////////////
