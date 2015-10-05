<h3 class="page-title"><?php echo TEXT_EXT_СALENDAR_PERSONAL ?></h3>

<p><?php echo TEXT_EXT_СALENDAR_PERSONAL_ACCESS ?></p>

<?php echo form_tag('configuration_form', url_for('ext/calendar/configuration','action=save_personal'),array('class'=>'form-horizontal')) ?>
<div class="form-body">

  <div class="form-group">
  	<label class="col-md-3 control-label" for="allowed_groups"><?php echo TEXT_EXT_USERS_GROUPS ?></label>
    <div class="col-md-9">	
  	   <?php echo select_checkboxes_tag('allowed_groups',access_groups::get_choices(false),implode(',',calendar::get_personal_access()),array('class'=>'form-control input-large'))?>
    </div>			
  </div>
  
</div>  

<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>
</form> 