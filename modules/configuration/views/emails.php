
<h3 class="page-title"><?php echo TEXT_HEADING_EMAIL_OPTIONS ?></h3>
 
<?php echo form_tag('cfg', url_for('configuration/save','redirect_to=configuration/emails'),array('class'=>'form-horizontal')) ?>
<div class="form-body">

  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_USE_NOTIFICATION"><?php echo TEXT_EMAIL_USE_NOTIFICATION ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[EMAIL_USE_NOTIFICATION]',$default_selector,CFG_EMAIL_USE_NOTIFICATION,array('class'=>'form-control input-large')); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_SUBJECT_LABEL"><?php echo TEXT_EMAIL_SUBJECT_LABEL ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_SUBJECT_LABEL]', CFG_EMAIL_SUBJECT_LABEL,array('class'=>'form-control input-small')); ?>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS"><?php echo TEXT_EMAIL_AMOUNT_PREVIOUS_COMMENTS ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_AMOUNT_PREVIOUS_COMMENTS]',CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS,array('class'=>'form-control input-small')); ?>
    </div>			
  </div>   
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_COPY_SENDER"><?php echo TEXT_EMAIL_COPY_SENDER ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[EMAIL_COPY_SENDER]',$default_selector,CFG_EMAIL_COPY_SENDER,array('class'=>'form-control input-small')); ?>
    </div>			
  </div> 
  
<h3 class="form-section"></h3>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_SEND_FROM_SINGLE"><?php echo TEXT_EMAIL_SEND_FROM_SINGLE ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[EMAIL_SEND_FROM_SINGLE]',$default_selector,CFG_EMAIL_SEND_FROM_SINGLE,array('class'=>'form-control input-small')); ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_ADDRESS_FROM"><?php echo TEXT_EMAIL_ADDRESS_FROM ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_ADDRESS_FROM]', CFG_EMAIL_ADDRESS_FROM,array('class'=>'form-control input-large')); ?>
    </div>			
  </div>       

  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_NAME_FROM"><?php echo TEXT_EMAIL_NAME_FROM ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_NAME_FROM]', CFG_EMAIL_NAME_FROM,array('class'=>'form-control input-large')); ?>
    </div>			
  </div>                                                                                                            
 
<h3 class="form-section"><?php echo TEXT_EMAIL_SMTP_CONFIGURATION; ?></h3>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_USE_SMTP"><?php echo TEXT_EMAIL_USE_SMTP ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[EMAIL_USE_SMTP]',$default_selector,CFG_EMAIL_USE_SMTP,array('class'=>'form-control input-small')); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_SERVER"><?php echo TEXT_EMAIL_SMTP_SERVER ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_SMTP_SERVER]', CFG_EMAIL_SMTP_SERVER,array('class'=>'form-control input-large')); ?>
    </div>			
  </div>  

  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_PORT"><?php echo TEXT_EMAIL_SMTP_PORT ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_SMTP_PORT]', CFG_EMAIL_SMTP_PORT,array('class'=>'form-control input-small')); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_ENCRYPTION"><?php echo TEXT_EMAIL_SMTP_ENCRYPTION ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_SMTP_ENCRYPTION]', CFG_EMAIL_SMTP_ENCRYPTION,array('class'=>'form-control input-small')) . ' <span class="help-block">' . implode('/',stream_get_transports()) . '</span>'; ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_LOGIN"><?php echo TEXT_EMAIL_SMTP_LOGIN ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_SMTP_LOGIN]', CFG_EMAIL_SMTP_LOGIN,array('class'=>'form-control input-large')); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_SMTP_PASSWORD"><?php echo TEXT_EMAIL_SMTP_PASSWORD ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[EMAIL_SMTP_PASSWORD]', CFG_EMAIL_SMTP_PASSWORD,array('class'=>'form-control input-large')); ?>
    </div>			
  </div> 
                                                                                                            
  <?php echo submit_tag(TEXT_BUTTON_SAVE) ?>
   
</div>
</form> 

