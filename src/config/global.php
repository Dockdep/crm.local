<?php

return 
[
    ///////////////////////////////////////////////////////////////////////////

    'global'        =>
    [
        'database'          =>
        [
            'server'                => '127.0.0.1',
            'port'                  => '5432',
            'db'                    => 'seo',
            'user'                  => 'seo',
            'passwd'                => '2c8UCLa2VCgQmkFzzAemqVtP3FnJcL89',
        ],

        'domains'           =>
            [
                'www'           => 'seo.dev.artwebua.com.ua/',
                'storage'       => 'http://storage.seo.dev.artwebua.com.ua/',
            ],

        'types'           =>
        [
            'ua'           =>
                [
                    1 => array (
                        'title' => 'Насіння овочів',
                        'subtypes' => array (
                            1 => array ( 'title' => 'Кавун', 'alias' => 'kavun', 'cover' => 'ca6e91933e7c02ac68da2c3bb1089d12' ),
                            2 => array ( 'title' => 'Баклажан', 'alias' => 'baklajan', 'cover' => '2939b6e37ee741a165bae3209501f04d' ),
                            3 => array ( 'title' => 'Горох овочевий', 'alias' => 'goroh_ovochevyyi', 'cover' => 'e44f4ac980199320f16ff762b19a880e' ),
                            4 => array ( 'title' => 'Диня', 'alias' => 'dynya', 'cover' => NULL ),
                            5 => array ( 'title' => 'Кабачок', 'alias' => 'kabachok', 'cover' => '135614aeb78730fd8bfd472ad238691a' ),
                            6 => array ( 'title' => 'Патисон', 'alias' => 'patyson', 'cover' => '135614aeb78730fd8bfd472ad238691a' ),
                            7 => array ( 'title' => 'Капуста білоголова', 'alias' => 'kapusta_bilogolova', 'cover' => 'b09904f334dce200ca042052daee8269' ),
                            8 => array ( 'title' => 'Капуста броколі', 'alias' => 'kapusta_brokoli', 'cover' => 'd515fac1327f12f636c5ef6f0d022a38' ),
                            9 => array ( 'title' => 'Капуста брюсельська', 'alias' => 'kapusta_bryuselska', 'cover' => NULL ),
                            10 => array ( 'title' => 'Капуста червоноголова', 'alias' => 'kapusta_chervonogolova', 'cover' => NULL ),
                            11 => array ( 'title' => 'Капуста пекінська', 'alias' => 'kapusta_pekinska', 'cover' => '7f0e069d2889a67ba7a8b3d07264e485' ),
                            12 => array ( 'title' => 'Капуста савойська', 'alias' => 'kapusta_savoyiska', 'cover' => NULL ),
                            13 => array ( 'title' => 'Капуста цвітна', 'alias' => 'kapusta_tsvitna', 'cover' => 'd76a6a1d26e319c79f4ae8eaac4a762b' ),
                            14 => array ( 'title' => 'Кукурудза цукрова', 'alias' => 'kukurudza_tsukrova', 'cover' => '987b658c7b64b83d843aa1e1de506bc6' ),
                            15 => array ( 'title' => 'Цибуля', 'alias' => 'tsybulya', 'cover' => 'd328606bb1c8a023c1ccd480b6ad82cb' ),
                            16 => array ( 'title' => 'Морква', 'alias' => 'morkva', 'cover' => '14cb2fb096fe6da190a76e89651dead6' ),
                            17 => array ( 'title' => 'Огірок', 'alias' => 'ogirok', 'cover' => '101a5438d757ed31624ed59d5b6e5726' ),
                            18 => array ( 'title' => 'Перець', 'alias' => 'perets', 'cover' => 'c50645de133536028aeecdc1b2d61a74' ),
                            19 => array ( 'title' => 'Пряні та зелені культури', 'alias' => 'pryani_ta_zeleni_kultury', 'cover' => NULL ),
                            20 => array ( 'title' => 'Селера', 'alias' => 'selera', 'cover' => NULL ),
                            21 => array ( 'title' => 'Редиска', 'alias' => 'redyska', 'cover' => 'a82262d5d027b6ea468919188ca4ed23' ),
                            22 => array ( 'title' => 'Редька', 'alias' => 'redka', 'cover' => NULL ),
                            23 => array ( 'title' => 'Салати', 'alias' => 'salaty', 'cover' => NULL ),
                            24 => array ( 'title' => 'Буряк столовий', 'alias' => 'buryak_stolovyyi', 'cover' => 'a07146d6bddf4efbea61e8e0b9b2bd3c' ),
                            25 => array ( 'title' => 'Томати', 'alias' => 'tomaty', 'cover' => NULL ),
                            26 => array ( 'title' => 'Гарбуз', 'alias' => 'garbuz', 'cover' => '5cb50646c508433b032dfe85f8299420' ),
                            27 => array ( 'title' => 'Квасоля', 'alias' => 'kvasolya', 'cover' => NULL  )),
                        'alias' => 'nasinnya_ovochiv', ),
                    2 => array (
                        'title' => 'Насіння квітів',
                        'subtypes' => array (
                            1 => array (
                                'title' => 'Однорічники', 'alias' => 'odnorichnyky', ),
                            2 => array ( 'title' => 'Дворічники', 'alias' => 'dvorichnyky', ),
                            3 => array ( 'title' => 'Багаторічники', 'alias' => 'bagatorichnyky', ),
                            4 => array ( 'title' => 'Кімнатні', 'alias' => 'kimnatni', ), ),
                        'alias' => 'nasinnya_kvitiv', ),
                    3 => array (
                        'title' => 'Добрива',
                        'subtypes' => array (
                            1 => array ( 'title' => 'Водорозчинні', 'alias' => 'vodorozchynni', ),
                            2 => array ( 'title' => 'Мінеральні', 'alias' => 'mineralni', ),
                            3 => array ( 'title' => 'Рідки', 'alias' => 'ridky', ), ),
                        'alias' => 'dobryva_ta_zahyst', ),
                    4 => array (
                        'title' => 'Газонні трави',
                        'subtypes' => array (
                            1 => array ( 'title' => 'Газонні трави', 'alias' => 'gazonni_travy', ), ),
                        'alias' => 'gazonni_travy', ),
                    5 => array (
                        'title' => 'Квіткові суміші',
                        'subtypes' => array (
                            1 => array ( 'title' => 'Elite Серія', 'alias' => 'elite_seriya', ),
                            2 => array ( 'title' => 'Nova Flora', 'alias' => 'nova_flora', ),
                        ),
                        'alias' => 'kvitkovi_sumishi', ),
                    6 => array (
                        'title' => 'Біопрепарати',
                        'subtypes' => array (
                            1 => array ( 'title' => 'Біопрепарати', 'alias' => 'biopreparaty', ), ),
                        'alias' => 'biopreparaty', )
                ],
            'ru'       =>
                [

                ],
        ],

        'title'                     => 'Професійне насіння',
        'keywords'                  => 'Професійне насіння keywords',
        'description'               => 'Професійне насіння description',


        'phones'                    => '(044)-581-67-15, (044)-451-48-59 <br /> (050)-464-48-59, (067)-464-48-59',
        'email'                     => 'jane.bezmaternykh@gmail.com',

        'delivery'      =>
            [
                '1' => 'Я заберу товар в пункті видачі / самовивіз (м.Київ, вул.Віскозна 17/а)',
                '2' => 'Доставка по м.Києву кур\'єром (вартість доставки 35 грн)',
                '3' => 'Достака по Україні службою "Нова Пошта" - самовивіз зі складу / вартість доставки від 20 грн (за тарифами поштової служби)',
                '4' => 'Достака по Україні службою "Нова Пошта" - адресна доставка курє\'ром / вартість доставки від 20 грн (за тарифами поштової служби)',
                '5' => 'Достака по Україні службою перевозки (Автолюкс, Укрпошта) / вартість доставки від 12 грн (за тарифами поштової служби)',
            ],

        'pay'      =>
            [
                '1' => 'Оплатить наличными',
                '2' => 'Оплатить на карту Приват Банка (оплата поступает 30 минут до суток!)',
                '3' => 'Оплатить по безналичному расчету (оплата поступает на счет от  1 до 3 рабочих дней! Счет на оплату отправим сразу после обработки заказана на ваш e-mail)',
                '4' => 'Оплатить "Правекс-телеграф" (оплата денежным переводом поступает от 30 мин. до 4 часов)',
            ],

        'status'      =>
            [
                '1' =>
                    [
                        '1' => 'Новий',
                        '2' => 'Обробленний',
                        '3' => 'Виконанний',
                        '4' => 'Відкладенний',
                        '5' => 'Відміненний',
                        '6' => 'Повернення',
                    ]
            ],

        'status_pay'      =>
            [
                '1' =>
                    [
                        '1' => 'Сплачено',
                        '2' => 'Не сплачено',
                    ]

            ],

        'storage'           =>
            [
                'avatar'               =>
                    [
                        '128x'    =>
                            [
                                'width'         => 128,
                                'height'        => 128,
                            ],
                        '200x'    =>
                            [
                                'width'         => 200,
                                'height'        => 200,
                            ],
                        '400x'    =>
                            [
                                'width'         => 400,
                                'height'        => 400,
                            ],
                        '800x'   =>
                            [
                                'width'         => 800,
                                'height'        => 800,
                            ]
                    ],
                'group'               =>
                    [
                        '128x128'    =>
                            [
                                'width'         => 128,
                                'height'        => 128,
                            ],
                        '400x400'    =>
                            [
                                'width'         => 400,
                                'height'        => 400,
                            ],
                        '800x'   =>
                            [
                                'width'         => 800,
                                'height'        => 800,
                            ]
                    ],

                'subtype'               =>
                    [
                        '165x120'    =>
                            [
                                'width'         => 165,
                                'height'        => 120,
                            ]
                    ],

                'news'               =>
                    [
                        '400x265'    =>
                            [
                                'width'         => 400,
                                'height'        => 265,
                            ],
                        '180x120'    =>
                            [
                                'width'         => 180,
                                'height'        => 120,
                            ],
                        '135x100'    =>
                            [
                                'width'         => 135,
                                'height'        => 100,
                            ],
                        '800x'   =>
                            [
                                'width'         => 800,
                                'height'        => 800,
                            ]
                    ],
            ],
    ],



    ///////////////////////////////////////////////////////////////////////////

    'frontend'                           =>
    [
        'dirs'              =>
        [
            'controllersDir'            => 'app/frontend/controllers/',
            'appLibrariesDir'           => 'app/frontend/lib/',
            'librariesDir'              => 'lib/',
            'modelsDir'                 => 'lib/models/',
            'viewsDir'                  => 'app/frontend/views/',
            'messagesDir'               => 'app/frontend/messages/',
        ],

        'defaults'          =>
        [
            'default_route'             => 'homepage',
        ],

        'limits'            =>
        [
            'search'                    => 20,
            'top_items'                 => 5,
            'items'                     => 12,
            'news'                      => 10,
            'groups2news'               => 5,
            'items_dropdown'            => 5,
        ],

        'lifetime'          =>
        [

            'models\settings'             =>
            [
                'getSettingsChatInviteMessages' => 5
            ],
        ],
    ],

    ///////////////////////////////////////////////////////////////////////////

    'backend'                           =>
    [
        'dirs'              =>
        [
            'controllersDir'            => 'app/backend/controllers/',
            'appLibrariesDir'           => 'app/backend/lib/',
            'librariesDir'              => 'lib/',
            'modelsDir'                 => 'lib/models/',
            'viewsDir'                  => 'app/backend/views/',
            'messagesDir'               => 'app/backend/messages/',
        ],


    ],

    ///////////////////////////////////////////////////////////////////////////

    'tasks'                             =>
    [
        'dirs'                          =>
        [
            'controllersDir'            => 'app/tasks/',
            'librariesDir'              => 'lib/',
            'modelsDir'                 => 'lib/models/',
        ],
    ],

    ///////////////////////////////////////////////////////////////////////////
];
