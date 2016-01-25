<!-- Login view -->
<div class="mobile-padding">
    <bb-loader></bb-loader>
    <bb-alert alert="alert"></bb-alert>
    <h1>Create your personal account</h1>
     <?php Ut::validation() ?>
     <form name="form_join" id="form_join" class="form form-page" method="post" action="<?php echo Ut::uri('join/post') ?>" novalidate>
    <fieldset>
        <!-- login -->
        <div class="form-group">
            <label for="mail">Email Address</label>
            <input name="mail" id="mail" type="text" class="form-control" value="<?php echo $form->mail ?>" />
        </div>
        <!-- password -->
        <div class="form-group">
            <label for="pw">Password</label>
            <input name="pw" id="pw" type="password" class="form-control" value="" />
        </div>

    </fieldset>
    <fieldset class="submit-entry">
        <button type="submit" class="btn btn-primary btn-block">Create an account</button>
    </fieldset>
</form>
   
    <a href="<?php echo Ut::uri('home') ?>">Already have an account?</a>
</div>