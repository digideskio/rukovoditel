<h3 class="page-title"><?php echo TEXT_EXT_СALENDAR_PUBLIC ?></h3>

<p><?php echo TEXT_EXT_СALENDAR_PUBLIC_ACCESS ?></p>

<?php echo form_tag('configuration_form', url_for('ext/calendar/configuration','action=save_public'),array('class'=>'form-horizontal')) ?>
<div class="form-body">

    <?php foreach(access_groups::get_choices(false) as $group_id=>$group_name): ?>
    <div class="form-group">
    	<label class="col-md-3 control-label" for="allowed_groups"><?php echo $group_name ?></label>
      <div class="col-md-9">	
    	   <?php echo select_tag('access[' . $group_id . ']',array(''=>'','view'=>TEXT_VIEW_ONLY_ACCESS,'full'=>TEXT_FULL_ACCESS),calendar::get_public_access($group_id),array('class'=>'form-control input-medium'))?>
      </div>			
    </div>
    <?php endforeach ?>
  
</div>  

<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>
</form> 