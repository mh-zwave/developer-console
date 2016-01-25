<!-- Password -->
<div ng-controller_="PasswordController">
    <bb-loader></bb-loader>
    <h1>Forgot password?</h1>
    <form name="form_password" id="form_password" class="form form-page" method="post" action="<?php echo Ut::uri('password/post') ?>" novalidate>
        <fieldset>
            <!-- email -->
            <div class="form-group">
                <label for="mail">Enter your email address</label>
                <input name="mail" id="email" type="text" class="form-control" value="" />
            </div>
        </fieldset>
       <fieldset class="submit-entry">
           <button type="submit" class="btn btn-submit">Submit</button>
        </fieldset>
    </form>
</div>