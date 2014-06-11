<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?= !empty( $meta_title ) ? $meta_title : \config::get( 'global#title' ) ?></title>
    <meta name="keywords" content="<?= !empty( $meta_keywords ) ? $meta_keywords : \config::get( 'global#title' ) ?>">
    <meta name="description" content="<?= !empty( $meta_description ) ? $meta_description : \config::get( 'global#title' ) ?>">

    <link rel="stylesheet" href="/css/main.css" type="text/css" media="all" />


    <!-- ilightbox -->
    <link rel="stylesheet" href="/css/ilightbox/ilightbox.css" />
    <!-- ilightbox skin -->
    <link rel="stylesheet" href="/css/ilightbox/dark-skin/skin.css" />
    <link rel="stylesheet" href="/css/ilightbox/metro-black-skin/skin.css" />
    <link rel="stylesheet" href="/css/ilightbox/light-skin/skin.css" />


    <script src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>

    <script type="text/javascript" src="/js/ilightbox/ilightbox.packed.js"></script>

    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="/js/validate.js"></script>



</head>

<body>
<div class="callback_form clearfix">

    <h2>Зворотній зв'язок</h2>
    <form id="callback_<?= ($lang_id == 1 ? 'ua' : 'ru') ?>"  method="post" action="<?= $this->url->get([ 'for' => 'callback' ]) ?>" name="callback">
        <ul class="form clearfix">
            <li class="clearfix">
                <div class="label float"><label for="email">Назвіться будь ласка<span class="required">&#8727;</span></label></div>
                <div class="input float"><input type="text" name="name" id="name" class="name" value="<?= !empty( $customer ) ? $customer['0']['name'] : '' ?>"></div>
            </li>
            <li class="clearfix">
                <div class="label float"><label for="email">Ваш телефон або email для зворотної відповіді<span class="required">&#8727;</span></label></div>
                <div class="input float"><input type="text" name="email" id="email" class="name" value=""></div>
            </li>
            <li class="clearfix with_textarea">
                <div class="label float"><label for="comments">Текст коментаря<span class="required">&#8727;</span></label></div>
                <div class="input float">
                    <textarea name="comments" id="comments"></textarea>
                </div>
            </li>
        </ul>
        <div class="submit float float_right">
            <input type="submit" value="Надіслати повідомлення" class="btn green">
        </div>
    </form>

</div>

</body>
</html>
 