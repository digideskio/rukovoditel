
<h3 class="page-title"><?php echo TEXT_HEADING_APPLICATION ?></h3>

<?php echo form_tag('cfg_form', url_for('configuration/save','redirect_to=configuration/application'),array('enctype'=>'multipart/form-data','class'=>'form-horizontal')) ?>
<div class="form-body">

  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_NAME"><?php echo TEXT_APPLICATION_NAME ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[APP_NAME]', CFG_APP_NAME, array('class'=>'form-control input-large required')); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_SHORT_NAME"><?php echo TEXT_APPLICATION_SHORT_NAME ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[APP_SHORT_NAME]', CFG_APP_SHORT_NAME, array('class'=>'form-control input-small required')); ?>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="APP_LOGO"><?php echo TEXT_APPLICATION_LOGO ?></label>
    <div class="col-md-9">	
  	  <?php echo input_file_tag('APP_LOGO') . input_hidden_tag('CFG[APP_LOGO]',CFG_APP_LOGO);             
      if(is_file(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGO))
      {
        echo  '<span class="help-block">' . CFG_APP_LOGO . '<label class="checkbox">' . input_checkbox_tag('delete_logo') . ' ' . TEXT_DELETE . '</label></span>';
      }                                                                                                 
    ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_LANGUAGE"><?php echo TEXT_LANGUAGE ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[APP_LANGUAGE]', app_get_languages_choices(),CFG_APP_LANGUAGE,array('class'=>'form-control input-medium')); ?>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_SKIN"><?php echo TEXT_SKIN ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[APP_SKIN]', app_get_skins_choices(),CFG_APP_SKIN,array('class'=>'form-control input-medium')); ?>
      <span class="help-block"><?php echo TEXT_SKIN_TOOLTIP ?></span>
    </div>			
  </div> 
  
<?php
  $timezone_list = array();
  $timezone_identifiers = DateTimeZone::listIdentifiers();
  for ($i=0; $i < sizeof($timezone_identifiers); $i++) 
  {    
      $timezone_list[$timezone_identifiers[$i]] = $timezone_identifiers[$i];
  }  
?> 

  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_TIMEZONE"><?php echo TEXT_APPLICATION_TIMEZONE ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[APP_TIMEZONE]',$timezone_list,  CFG_APP_TIMEZONE,array('class'=>'form-control input-large')); ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_ROWS_PER_PAGE"><?php echo TEXT_ROWS_PER_PAGE ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[APP_ROWS_PER_PAGE]', CFG_APP_ROWS_PER_PAGE,array('class'=>'form-control input-small required number')); ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_DATE_FORMAT"><?php echo TEXT_DATE_FORMAT ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[APP_DATE_FORMAT]', CFG_APP_DATE_FORMAT, array('class'=>'form-control input-small required')); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_DATETIME_FORMAT"><?php echo TEXT_DATETIME_FORMAT ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[APP_DATETIME_FORMAT]', CFG_APP_DATETIME_FORMAT, array('class'=>'form-control input-small required')) ?>
      <?php echo '<span class="help-block">' . TEXT_DATE_FORMAT_IFNO . '</span>';?>
    </div>			
  </div>

<?php
  $days_array = explode(',',str_replace('"','',TEXT_DATEPICKER_DAYS));  
  $days_list = array('0'=>$days_array[0],'1'=>$days_array[1]);
?>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_APP_FIRST_DAY_OF_WEEK"><?php echo TEXT_FIRST_DAY_OF_WEEK ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[APP_FIRST_DAY_OF_WEEK]',$days_list, CFG_APP_FIRST_DAY_OF_WEEK, array('class'=>'form-control input-medium required')) ?>
      <?php echo '<span class="help-block">' . TEXT_FIRST_DAY_OF_WEEK_INFO . '</span>';?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_PASSWORD_MIN_LENGTH"><?php echo TEXT_MIN_PASSWORD_LENGTH ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[PASSWORD_MIN_LENGTH]', CFG_PASSWORD_MIN_LENGTH, array('class'=>'form-control input-small required')); ?>
    </div>			
  </div>          

<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
  $(function() { 
    $('#cfg_form').validate({
      rules: {
        APP_LOGO: {
          required: false,
          extension: "gif|jpeg|jpg|png"          
        }
      } 
    });                                                                            
  });
    
</script> 