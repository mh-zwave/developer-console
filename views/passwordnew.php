<!-- Password confirm -->
<h1>Password confirm</h1>
<form name="form_password" id="form_password" class="form form-page" method="post" action="<?php echo Ut::uri('passwordupdate') ?>" novalidate>
    <fieldset>
        <input name="token" id="token" type="hidden" value="<?php echo $api->getInputVal('token') ?>" />
        <!-- email -->
        <div class="form-group">
            <label for="pw">Please enter your new password</label>
            <input name="pw" id="pw" type="password" class="form-control" value="" />
        </div>
    </fieldset>
    <fieldset class="submit-entry">
        <button type="submit" class="btn btn-submit">Submit</button>
    </fieldset>
</form>