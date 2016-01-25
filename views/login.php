<!-- Login view -->
<div class="mobile-padding">
    <bb-loader></bb-loader>
    <bb-alert alert="alert"></bb-alert>
    <h1>Login</h1>
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
            <button type="submit" class="btn btn-submit btn-block">Login</button>
        </div>
    </form>
    <a href="<?php echo Ut::uri('password') ?>">Forgot password?</a> | <a href="<?php echo Ut::uri('home') ?>">Create your personal account</a>
</div>