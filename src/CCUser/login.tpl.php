<h1>Login</h1>
<p>Login using your acronym or email.</p>
<?=$login_form->GetHTML('form')?> <!-- Creation of this variable starts at CCUser::Login, which continues to fully develop to a HTML-form in CForm. -->
  <fieldset>
    <?=$login_form['acronym']->GetHTML()?>
    <?=$login_form['password']->GetHTML()?> 
    <?=$login_form['login']->GetHTML()?>
    <?php if($allow_create_user) : ?>
      <p class='form-action-link'><a href='<?=$create_user_url?>' title='Create a new user account'>Create user</a></p>
    <?php endif; ?>
  </fieldset>
</form> 