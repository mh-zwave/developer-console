<!-- Home -->
<div class="row">
    <div class="col col-md-7">
        <h1>z-wave.Me - Developer Console</h1>
        <p></p>
        <a class="btn btn-default btn-block" href="<?php echo Ut::uri('join') ?>">Create your personal account</a>
    </div>
    <div class="col col-md-5">
        <?php Ut::validation() ?>
        <form name="form_login" id="form_login" class="form form-page" method="post" action="<?php echo Ut::uri('login/post') ?>" novalidate>
            <fieldset>
                <!-- email -->
                <div class="form-group">
                    <label for="mail">Email Address</label>
                    <input name="mail" id="mail" type="text" class="form-control" value="" />
                </div>
                <!-- password -->
                <div class="form-group">
                    <label for="pw">Password</label>
                    <input name="pw" id="pw" type="password" class="form-control" value="" ng-model="input.password" />
                </div>

            </fieldset>
            <div class="form-footer">
                <button type="submit" class="btn btn-submit btn-block"><i class="fa fa-sign-in"></i> Login</button>
            </div>
        </form>
        <a href="<?php echo Ut::uri('password') ?>">Forgot password?</a> | <a href="<?php echo Ut::uri('join') ?>">Create your personal account</a>
    </div>
</div>
