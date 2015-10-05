<?php require(component_path('entities/navigation')) ?>

<?php $default_selector = array('1'=>TEXT_YES,'0'=>TEXT_NO); ?>




<?php echo form_tag('cfg', url_for('entities/entities_configuration','action=save&entities_id=' . $_GET['entities_id']),array('class'=>'form-horizontal')) ?>

<h3 class="form-section"><?php echo TEXT_TITLES ?></h3>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_MENU_TITLE; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[menu_title]', $cfg['menu_title'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_MENU_TITLE_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_MENU_ICON_TITLE; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[menu_icon]', $cfg['menu_icon'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_listing_heading"><?php echo TEXT_LISTING_HEADING; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[listing_heading]', $cfg['listing_heading'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_LISTING_HEADING_TOOLTIP) ?>
    </div>			
  </div>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_window_heading"><?php echo TEXT_WINDOW_HEADING; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[window_heading]', $cfg['window_heading'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_WINDOW_HEADING_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_insert_button"><?php echo TEXT_INSERT_BUTTON_TITLE; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[insert_button]', $cfg['insert_button'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_INSERT_BUTTON_TITLE_TOOLTIP) ?>
    </div>			
  </div>  
  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_insert_button"><?php echo TEXT_EMAIL_SUBJECT_NEW_ITEM; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[email_subject_new_item]', $cfg['email_subject_new_item'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_EMAIL_SUBJECT_NEW_ITEM_TOOLTIP) ?>
    </div>			
  </div> 
        

<h3 class="form-section"><?php echo TEXT_COMMENTS_TITLE ?></h3>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_USE_COMMENTS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[use_comments]',$default_selector, $cfg['use_comments'],array('class'=>'form-control input-small')); ?> 
      <?php echo tooltip_text(TEXT_USE_COMMENTS_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_USE_EDITOR_IN_COMMENTS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[use_editor_in_comments]',$default_selector, $cfg['use_editor_in_comments'],array('class'=>'form-control input-small')); ?> 
      <?php echo tooltip_text(TEXT_USE_EDITOR_IN_COMMENTS_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_insert_button"><?php echo TEXT_EMAIL_SUBJECT_NEW_COMMENT; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[email_subject_new_comment]', $cfg['email_subject_new_comment'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_EMAIL_SUBJECT_NEW_COMMENT_TOOLTIP) ?>
    </div>			
  </div>



<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</form>


<script>
  $(function() {   
    $('.tooltips').tooltip();                                                                             
  });
</script>    



