
<h3 class="page-title"><?php echo TEXT_HEADING_USER_REGISTRATION_EMAIL ?></h3>

<?php echo form_tag('cfg', url_for('configuration/save','redirect_to=configuration/registration_email'),array('class'=>'form-horizontal')) ?>
<div class="form-body">


  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_REGISTRATION_EMAIL_SUBJECT"><?php echo TEXT_REGISTRATION_EMAIL_SUBJECT ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[REGISTRATION_EMAIL_SUBJECT]', CFG_REGISTRATION_EMAIL_SUBJECT,array('class'=>'form-control input-xlarge')); ?>
      <span class="help-block"><?php echo TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT ?></span>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_REGISTRATION_EMAIL_BODY"><?php echo TEXT_REGISTRATION_EMAIL_BODY ?></label>
    <div class="col-md-9">	
  	  <?php echo textarea_tag('CFG[REGISTRATION_EMAIL_BODY]', CFG_REGISTRATION_EMAIL_BODY,array('class'=>'form-control input-xlarge editor')); ?>
      <span class="help-block"><?php echo TEXT_REGISTRATION_EMAIL_BODY_NOTE ?></span>
    </div>			
  </div>


<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div> 
</form>