{{include "common.header"}}

<div id="page-" class="col-md-4 col-md-offset-4">

    <form class="form loginform" method="POST">
        <input type="hidden" name="action" value="login">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">Для входа в систему требуется авторизация</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label">Имя пользователя</label>
                    <input title="Имя пользователя" type="text" name="username" class="form-control" required="required">
                </div>
                <div class="form-group">
                    <label class="control-label">Пароль</label>
                    <input title="Пароль" type="password" name="passwd" class="form-control" required="required">
                </div>
            {{if $data.error}}
                <div class="alert alert-danger">Неверно указано имя пользователя или пароль</div>;
            {{/if}}
            <button type="submit" class="btn btn-success loginField" >Войти</button>
        </div>
    </form>

</div>

{{include "common.footer"}}
