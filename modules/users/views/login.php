<h3 class="form-title"><?php echo (strlen(CFG_LOGIN_PAGE_HEADING)>0 ? CFG_LOGIN_PAGE_HEADING : TEXT_HEADING_LOGIN)?></h3>

<?php echo (strlen(CFG_LOGIN_PAGE_CONTENT)>0 ? '<p>' . nl2br(CFG_LOGIN_PAGE_CONTENT) . '</p>':'') ?>

<?php echo form_tag('login_form', url_for('users/login','action=login'),array('class'=>'login-form')) ?>

<div class="form-group">
	<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
	<label class="control-label visible-ie8 visible-ie9"><?php echo TEXT_USERNAME ?></label>
	<div class="input-icon">
		<i class="fa fa-user"></i>
		<input class="form-control placeholder-no-fix required" type="text" autocomplete="off" placeholder="<?php echo TEXT_USERNAME ?>" name="username"/>
	</div>
</div>
<div class="form-group">
	<label class="control-label visible-ie8 visible-ie9"><?php echo TEXT_PASSWORD ?></label>
	<div class="input-icon">
		<i class="fa fa-lock"></i>
		<input class="form-control placeholder-no-fix required"  type="password" autocomplete="off" placeholder="<?php echo TEXT_PASSWORD ?>" name="password"/>
	</div>
</div>
<div class="form-actions">
	<label class="checkbox"> <?php echo input_checkbox_tag('remember_me',1,array('checked'=>(isset($_COOKIE['app_remember_me']) ? true:false))) . ' ' . TEXT_REMEMBER_ME  ?></label>
	<button type="submit" class="btn btn-info pull-right"><?php echo TEXT_BUTTON_LOGIN ?></button>
</div>

<div class="forget-password">	
	<p>
		 <a href="<?php echo url_for('users/restore_password') ?>"><?php echo TEXT_PASSWORD_FORGOTTEN ?></a>
	</p>
</div>
<div class="create-account">
	<p>
		 <a href="<?php echo url_for('users/ldap_login') ?>"><?php echo TEXT_MENU_LDAP_LOGIN ?></a>
	</p>
</div>

</form>

<script>
  $(function() { 
    $('#login_form').validate();                                                                            
  });    
</script> 



