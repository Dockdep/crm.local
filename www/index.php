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
    
    config::setApp( 'frontend' );

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
        'frontend\lib'      => ROOT_PATH.config::get( 'dirs/appLibrariesDir' ),
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
            ->setName( 'homepage' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'type',
                ]
            )
            ->setName( 'type' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}{type_child:\-+\-+[a-z0-9\-\_]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'type',
                ]
            )
            ->setName( 'type_with_child' );


        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'subtype',
                ]
            )
            ->setName( 'subtype' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/compare/{compare_ids:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'compareItems',
                ]
            )
            ->setName( 'compare_items' );

        $router->add
            (
                '/search',
                [
                    'controller'    => 'page',
                    'action'        => 'search',
                ]
            )
            ->setName( 'search_items' );

        $router->add
            (
                '/search/{search:[A-Za-zА-Яа-я0-9\-\_]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'search',
                ]
            )
            ->setName( 'search_items_route' );

        $router->add
            (
                '/search/{search:[A-Za-zА-Яа-я0-9\-\_]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'search',
                ]
            )
            ->setName( 'search_items_paged' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'subtype',
                ]
            )
            ->setName( 'subtype_paged' );


        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{group_alias:[a-z0-9\-\_\+]+}-{item_id:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'item',
                ]
            )
            ->setName( 'item' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/sort-{sort:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'subtype',
                ]
            )
            ->setName( 'subtype_sorted' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/sort-{sort:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'subtype',
                ]
            )
            ->setName( 'subtype_sorted_paged' );

        // filters

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_ids' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}/sort-{sort:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_ids_sorted' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_ids_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}/sort-{sort:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_ids_sorted_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}/sort-{sort:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias_sorted' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}/sort-{sort:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias_sorted_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}--price-{price:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_price' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}--price-{price:[0-9\-]+}/sort-{sort:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_price_sorted' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}--price-{price:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_price_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}--price-{price:[0-9\-]+}/sort-{sort:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_price_sorted_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}--price-{price:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias_price' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}--price-{price:[0-9\-]+}/sort-{sort:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias_price_sorted' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}--price-{price:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias_price_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/{filter_ids:[0-9\-]+}{filter_alias:\-+\-+[a-z0-9\-\_\:]+}--price-{price:[0-9\-]+}/sort-{sort:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_id_alias_price_sorted_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/price-{price:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_price' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/price-{price:[0-9\-]+}/sort-{sort:[0-9\-]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_price_sorted' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/price-{price:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_price_paginate' );

        $router->add
            (
                '/{type:[a-z0-9\-\_]+}/{subtype:[a-z0-9\-\_]+}/price-{price:[0-9\-]+}/sort-{sort:[0-9\-]+}/page/{page:[0-9]+}',
                [
                    'controller'    => 'page',
                    'action'        => 'filters',
                ]
            )
            ->setName( 'get_items_with_filters_price_sorted_paginate' );



        ///////////////////////////////////////////////////////////////////////

        $router->add
            (
                '/change_top_items',
                [
                    'controller'    => 'page',
                    'action'        => 'topItems',
                ]
            )
            ->setName( 'change_top_items' );

        $router->add
            (
                '/change_with_size',
                [
                    'controller'    => 'page',
                    'action'        => 'changeWithSize',
                ]
            )
            ->setName( 'change_with_size' );

        $router->add
            (
                '/change_similar_items',
                [
                    'controller'    => 'page',
                    'action'        => 'changeSimilarItems',
                ]
            )
            ->setName( 'change_similar_items' );

        ///////////////////////////////////////////////////////////////////////

        $router->add
            (
                '/{page_alias:[a-z0-9\-\_]+}-{page_id:[0-9]+}',
                [
                    'controller'    => 'menu',
                    'action'        => 'staticPage',
                ]
            )
            ->setName( 'static_page' );

        $router->add
            (
                '/news-actions',
                [
                    'controller'    => 'menu',
                    'action'        => 'news',
                ]
            )
            ->setName( 'news' );

        $router->add
            (
                '/news-actions/page/{page:[0-9]+}',
                [
                    'controller'    => 'menu',
                    'action'        => 'news',
                ]
            )
            ->setName( 'news_paginate' );

        $router->add
            (
                '/news-actions/{news_alias:[a-z0-9\-\_]+}-{news_id:[0-9]+}',
                [
                    'controller'    => 'menu',
                    'action'        => 'oneNews',
                ]
            )
            ->setName( 'one_news' );

        $router->add
            (
                '/prof_tips',
                [
                    'controller'    => 'menu',
                    'action'        => 'profTips',
                ]
            )
            ->setName( 'prof_tips' );

        $router->add
            (
                '/prof_tips/page/{page:[0-9]+}',
                [
                    'controller'    => 'menu',
                    'action'        => 'profTips',
                ]
            )
            ->setName( 'prof_tips_paginate' );

        $router->add
            (
                '/prof_tips/{tips_alias:[a-z0-9\-\_]+}-{tips_id:[0-9]+}',
                [
                    'controller'    => 'menu',
                    'action'        => 'oneTips',
                ]
            )
            ->setName( 'one_tips' );

        $router->add
            (
                '/partners',
                [
                    'controller'    => 'menu',
                    'action'        => 'partners',
                ]
            )
            ->setName( 'partners' );

        $router->add
            (
                '/contacts',
                [
                    'controller'    => 'menu',
                    'action'        => 'contacts',
                ]
            )
            ->setName( 'contacts' );

        $router->add
            (
                '/basket',
                [
                    'controller'    => 'menu',
                    'action'        => 'order',
                ]
            )
            ->setName( 'basket' );

        $router->add
            (
                '/basket/add_item',
                [
                    'controller'    => 'menu',
                    'action'        => 'addToBasket',
                ]
            )
            ->setName( 'add_to_basket' );

        $router->add
            (
                '/basket/delete_item',
                [
                    'controller'    => 'menu',
                    'action'        => 'deleteFromBasket',
                ]
            )
            ->setName( 'delete_from_basket' );

        $router->add
            (
                '/callback',
                [
                    'controller'    => 'menu',
                    'action'        => 'callback',
                ]
            )
            ->setName( 'callback' );

        $router->add
            (
                '/call-back', // callbackErrors
                [
                    'controller'    => 'menu',
                    'action'        => 'callbackErrors',
                ]
            )
            ->setName( 'callback_errors' );

        ///////////////////////////////////////////////////////////////////////

        $router->add
            (
                '/ajax/get_cities',
                [
                    'controller'    => 'menu',
                    'action'        => 'getCities',
                ]
            )
            ->setName( 'get_cities' );

        $router->add
            (
                '/ajax/get_offices',
                [
                    'controller'    => 'menu',
                    'action'        => 'getOffices',
                ]
            )
            ->setName( 'get_offices' );

        $router->add
            (
                '/ajax/get_items',
                [
                    'controller'    => 'ajax',
                    'action'        => 'getItems',
                ]
            )
            ->setName( 'get_items' );

        $router->add
            (
                '/ajax/add_item_for_compare',
                [
                    'controller'    => 'ajax',
                    'action'        => 'addItemsForCompare',
                ]
            )
            ->setName( 'add_item_for_compare' );

        ///////////////////////////////////////////////////////////////////////



        ///////////////////////////////////////////////////////////////////////


        $router->add
            (
                '/customer_login',
                [
                    'controller'    => 'customer',
                    'action'        => 'customerLogin',
                ]
            )
            ->setName( 'customer_login' );

        $router->add
            (
                '/customer_login/social/{mechanism:[a-z0-9\-\_]+}',
                [
                    'controller'    => 'customer',
                    'action'        => 'customerLoginSocial',
                ]
            )
            ->setName( 'customer_login_social' );

        $router->add
            (
                '/customer_logout',
                [
                    'controller'    => 'customer',
                    'action'        => 'customerLogout',
                ]
            )
            ->setName( 'customer_logout' );

        $router->add
            (
                '/registration',
                [
                    'controller'    => 'customer',
                    'action'        => 'registration',
                ]
            )
            ->setName( 'registration' );

        $router->add
            (
                '/finish_registration',
                [
                    'controller'    => 'customer',
                    'action'        => 'finishRegistration',
                ]
            )
            ->setName( 'finish_registration' );

        $router->add
            (
                '/registration_canceled',
                [
                    'controller'    => 'customer',
                    'action'        => 'registrationCancel',
                ]
            )
            ->setName( 'registration_canceled' );

        $router->add
            (
                '/restore_passwd',
                [
                    'controller'    => 'customer',
                    'action'        => 'restorePasswd',
                ]
            )
            ->setName( 'restore_passwd' );

        $router->add
            (
                '/restore/{confirm_key:[a-z0-9]+}',
                [
                    'controller'    => 'customer',
                    'action'        => 'resetPasswd',
                ]
            )
            ->setName( 'reset_passwd' );

        $router->add
            (
                '/confirm_registration/{confirm_key:[a-z0-9]+}',
                [
                    'controller'    => 'customer',
                    'action'        => 'confirmRegistration',
                ]
            )
            ->setName( 'confirm_registration' );

        $router->add
            (
                '/cabinet',
                [
                    'controller'    => 'customer',
                    'action'        => 'cabinet',
                ]
            )
            ->setName( 'cabinet' );

        $router->add
            (
                '/cabinet/order-{order_id:[0-9]+}',
                [
                    'controller'    => 'customer',
                    'action'        => 'listOrders',
                ]
            )
            ->setName( 'list_orders' );

        $router->add
            (
                '/change_customer_passwd',
                [
                    'controller'    => 'customer',
                    'action'        => 'changeCustomerPasswd',
                ]
            )
            ->setName( 'change_customer_passwd' );

        ///////////////////////////////////////////////////////////////////////

        $router->add
            (
                '/login',
                [
                    'controller'    => 'user',
                    'action'        => 'login',
                ]
            )
            ->setName( 'user_login' );

        ///////////////////////////////////////////////////////////////////////

        $router->add
            (
                '/_service/get_types',
                [
                    'controller'    => 'service',
                    'action'        => 'types',
                ]
            )
            ->setName( 'get_types' );

        $router->add
            (
                '/_service/get_images',
                [
                    'controller'    => 'service',
                    'action'        => 'images',
                ]
            )
            ->setName( 'get_images' );

        $router->add
            (
                '/_service/change_images',
                [
                    'controller'    => 'service',
                    'action'        => 'storage',
                ]
            )
            ->setName( 'change_images' );

        $router->add
            (
                '/_service/change_catalog',
                [
                    'controller'    => 'service',
                    'action'        => 'catalog',
                ]
            )
            ->setName( 'change_catalog' );

        $router->add
            (
                '/_service/change_cities',
                [
                    'controller'    => 'service',
                    'action'        => 'cities',
                ]
            )
            ->setName( 'change_cities' );

        $router->add
            (
                '/_service/poshta',
                [
                    'controller'    => 'service',
                    'action'        => 'poshta',
                ]
            )
            ->setName( 'poshta' );

        $router->add
            (
                '/_service/type_subtype',
                [
                    'controller'    => 'service',
                    'action'        => 'typeSubtype',
                ]
            )
            ->setName( 'type_subtype' );

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

    // novaposhta

    $di->set( 'novaposhta', function()
    {
        return new \novaposhta();
    }, true );

    ///////////////////////////////////////////////////////////////////////////

    // social

    $di->set( 'social', function()
    {
        return new \social();
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
