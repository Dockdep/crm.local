<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <title></title>
</head>
<body>
<div class="modal fade" id="registrationFormModal" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Регистрация</h4>
            </div>
            <div class="form">
                <form class="cmxform" id="registrationForm" method="get" action="">
                    <fieldset>
                        <p>
                            <label for="cname">Имя</label>
                            <input id="cname" name="name" minlength="2" type="text" required/>
                        </p>
                        <p>
                            <label for="cemail">E-Mail</label>
                            <input id="cemail" type="email" name="email" required/>
                        </p>
                        <p>
                            <label for="password">Пароль</label>
                            <input id="password" name="password" />
                        </p>
                        <p>
                            <label for="password_again">Повторите</label>
                            <input class="left" id="password_again" name="password_again" />
                        </p>
                        <p>
                            <input class="submit" type="submit" value="Submit"/>
                        </p>
                    </fieldset>
                </form>
            </div><!-- form -->
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="enterForm" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Регистрация</h4>
            </div>
            <div class="form">
                <form class="cmxform" id="enterFormBody" method="get" action="">
                    <fieldset>
                        <p>
                            <label for="ename">Имя</label>
                            <input id="ename" name="name" minlength="2" type="text" required/>
                        </p>
                        <p>
                            <label for="epassword">Пароль</label>
                            <input id="epassword" name="password" />
                        </p>
                        <p>
                            <input class="submit" type="submit" value="Submit"/>
                        </p>
                    </fieldset>
                </form>
            </div><!-- form -->
        </div>
    </div>
</div>
<div id="wrapper">
    <header>
        <div class="container">
            <div id="header-menu" class="span12">
                <ul>
                    <li><a href="#">Soft</a></li>
                    <li><a href="#">Analytics</a></li>
                    <li><a href="#">CRM</a></li>
                    <li><a href="#">Настройка</a></li>
                    <li><a href="#enterForm" data-toggle = 'modal'>Вход</a></li>
                    <li><a href="#registrationFormModal" data-toggle = 'modal'>Регистрация</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div id="content" class="container">
        <?php

        echo $this->getContent();

        ?>
    </div>
    <footer>
        <div id="footer-content-block">
            <div class="container">
                <div id="footer-left-column" class="span4">

                </div>
                <div id="footer-central-column" class="span4">

                </div>
                <div id="footer-right-column" class="span4">

                </div>
            </div>
        </div>
        <div id="footer-social-links-block">
            <div class="container">
                <div id="copy" class="span2">

                </div>
                <div id="social-link-menu" class="span8 offset2">
                    <p>Copyright &copy; <?php echo date('Y'); ?> by My Company. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/bootstrap.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
</body>
</html>