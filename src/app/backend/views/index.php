<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?= !empty( $meta_title ) ? $meta_title : \config::get( 'global#title' ) ?></title>
    <meta name="keywords" content="<?= !empty( $meta_keywords ) ? $meta_keywords : \config::get( 'global#title' ) ?>">
    <meta name="description" content="<?= !empty( $meta_description ) ? $meta_description : \config::get( 'global#title' ) ?>">

    <link rel="stylesheet" href="/css/main.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/css/jquery-ui/jquery-ui.min.css" />



    <script src="/js/jquery.js"></script>
    <script src="/js/jquery-ui.js" type="text/javascript"></script>

    <script type="text/javascript" src="/js/main.js"></script>
    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>



</head>

<body>




<div id="content" class="clearfix">

    <?php

    echo $this->getContent();

    ?>
</div>


<div id="profiler">
    <?php

    $info = $this->profiler->getInfoStatistics();

    echo
    (
        '<div id="profiler-general">'.
        '<span'.( $info['exec']>=50 ? ' class="warning"' : '' ).'>time total:&nbsp;'.$info['exec'].'&nbsp;ms</span> | '.
        '<span class="'.( $info['db']['time']>=20 ? 'warning ' : '' ).'profiler-sql-show">db time&nbsp;('.$info['db']['count'].'):&nbsp;'.$info['db']['time'].'&nbsp;ms</span> | '.
        '<span'.( $info['memory']>=800 ? ' class="warning"' : '' ).'>memory:&nbsp;'.$info['memory'].'&nbsp;KB</span>'.
        '</div>'
    );

    $info = $this->profiler->getAllStatistics();

    if( !empty($info) && isset($info['sql']) && !empty($info['sql']) )
    {
        $html   = '<div id="profiler-sql">';
        $c      = 1;

        foreach( $info['sql'] as $d )
        {
            $html .=
                '<div class="profiler-sql-item clearfix">'.
                '<div class="num">'.$c.'</div>'.
                '<div class="query">'.trim($d['sql']).'</div>'.
                '<div class="time '.( round( $d['time'] * 1000, 0 )>=5 ? 'warning' : '' ).'">'.round( $d['time'] * 1000, 3 ).'&nbsp;ms</div>'.
                '</div>';

            $c++;
        }

        $html .= '</div>';

        echo( $html );
    }
    ?>
</div>

</body>
</html>
