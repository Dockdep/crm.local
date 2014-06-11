
<div class="inner">
    <?= $this->flash->output(); ?>

    <form class="form-signin" role="form" method="post" id="admin_login">
        <h2 class="form-signin-heading">Вход</h2>
        <input type="email" class="input" placeholder="Email" autofocus="" name="email" id="login">
        <input type="password" class="input" placeholder="Пароль" name="passwd" id="passwd">
        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
    </form>

</div>