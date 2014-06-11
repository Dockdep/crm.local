<div id="content" class="clearfix">
    <div class="cabinet">
        <div class="breadcrumbs">
            <div class="inner">
                <div class="order_menu_shadow"></div>
                <ul class="clearfix">
                    <li class="float"><a href="/" title="Головна">Головна</a></li>
                    <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
                    <li class="float"><a href="/basket" title="Особистий кабінет">Особистий кабінет</a></li>
                    <li class="float more"><img src="/images/breadcrumbs_arrow.png" alt="" height="7" width="7" /></li>
                    <li class="float"><a href="javascript:void(0);" title="Змінити пароль" class="breadcrumbs_last">Змінити пароль</a></li>
                </ul>
            </div>
        </div>

        <div class="inner"><?= $this->flash->output(); ?></div>

        <div class="sidebar_content_wrapper">
            <div class="inner clearfix">
                <div id="sidebar" class="float">
                    <div class="subcategory_sidebar_title">
                        <h3>Особистий кабінет</h3>
                        <p>Змінити пароль</p>
                    </div>
                </div>
                <div id="content_wrapper" class="float">
                    <div class="subcategory_content_wrapper_title">
                        <h3>Змінити пароль</h3>
                    </div>
                    <form id="change_passwd_<?= ($lang_id == 1 ? 'ua' : 'ru') ?>"  method="post" action="" name="change_passwd">
                        <ul class="form clearfix">
                            <li class="clearfix">
                                <div class="label float"><label for="previous_passwd">Попередній пароль<span class="required">&#8727;</span></label></div>
                                <div class="input float"><input type="password" name="previous_passwd" id="previous_passwd" class="name" value=""></div>
                            </li>
                            <li class="clearfix">
                                <div class="label float"><label for="password">Новий пароль<span class="required">&#8727;</span></label></div>
                                <div class="input float"><input type="password" name="passwd" id="passwd" class="name" value=""></div>
                            </li>
                            <li class="clearfix">
                                <div class="label float"><label for="confirm_passwd">Підтвердження паролю<span class="required">&#8727;</span></label></div>
                                <div class="input float"><input type="password" name="confirm_passwd" id="confirm_passwd" class="name" value=""></div>
                            </li>
                        </ul>
                        <div class="submit">
                            <input type="submit" value="Зберігти" class="btn green">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


</div>