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
                    <li class="float"><a href="javascript:void(0);" title="<?= isset($breadcrambs_title) && !empty($breadcrambs_title) ? $breadcrambs_title : 'Реєстрація' ?>" class="breadcrumbs_last"><?= isset($breadcrambs_title) && !empty($breadcrambs_title) ? $breadcrambs_title : 'Реєстрація' ?></a></li>
                </ul>
            </div>
        </div>

        <div class="inner"><?= $this->flash->output(); ?></div>

        <div class="sidebar_content_wrapper">
            <div class="inner clearfix">
                <div id="sidebar" class="float">
                    <div class="subcategory_sidebar_title">
                        <h3>Особистий кабінет</h3>
                        <p><?= isset($breadcrambs_title) && !empty($breadcrambs_title) ? $breadcrambs_title : 'Реєстрація' ?></p>
                    </div>
                </div>
                <div id="content_wrapper" class="float">
                    <div class="subcategory_content_wrapper_title">
                        <h3><?= isset($breadcrambs_title) && !empty($breadcrambs_title) ? $breadcrambs_title : 'Реєстрація' ?></h3>
                    </div>
                    <form id="finish_registration_<?= ($lang_id == 1 ? 'ua' : 'ru') ?>"  method="post" action="" name="finish_registration">
                        <ul class="form clearfix">
                            <li class="clearfix">
                                <div class="label float"><label for="passwd">Пароль<span class="required">&#8727;</span></label></div>
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