<!-- Home -->
<div class="homepage-jumbotron row">
    <div class="col col-md-8">
        <h1>z-wave.Me - Developer Console</h1>
        <p></p>
        <a class="btn btn-default btn-block" href="<?php echo Ut::uri('join') ?>">Create your personal account</a>
    </div>
    <div class="col col-md-4">
        <?php Ut::validation() ?>
        <form name="form_login" id="form_login" class="form" method="post" action="<?php echo Ut::uri('login/post') ?>" novalidate>
            <fieldset>
                <!-- email -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input name="mail" id="mail" type="text" class="form-control" value="" placeholder="Your e-mail" />
                    </div>
                </div>
                <!-- password -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                        <input name="pw" id="pw" type="password" class="form-control" value="" placeholder="Your password" />
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-default btn-block"><i class="fa fa-sign-in"></i> Login</button>
                </div>

            </fieldset>
            <div class="form-footer text-center">
                <a href="<?php echo Ut::uri('password') ?>">Forgot password?</a> | <a href="<?php echo Ut::uri('join') ?>">Create your personal account</a>
            </div>
        </form>

    </div>
</div>
<h2><i class="fa fa-arrow-circle-down" aria-hidden="true"></i> Newest Apps</h2>
<?php require_once 'views/apps/apps_list.php' ?>
<div class="text-right">
    <a class="btn btn-primary btn-lg" href="<?php echo Ut::uri('public') ?>#/web/apps">All Apps <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
</div>


