
<?php echo ajax_modal_template_header(TEXT_EXT_СALENDAR_REPORTS) ?>

<?php echo form_tag('configuration_form', url_for('ext/calendar/configuration','action=save_report' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
     

<ul class="nav nav-tabs">
  <li class="active"><a href="#general_info"  data-toggle="tab"><?php echo TEXT_GENERAL_INFO ?></a></li>
  
  <li><a href="#access"  data-toggle="tab"><?php echo TEXT_ACCESS ?></a></li>    
</ul>
 
<div class="tab-content">
  <div class="tab-pane fade active in" id="general_info">

       
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
      <div class="col-md-9">	
    	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large required')) ?>        
      </div>			
    </div>
      
    <div class="form-group">
    	<label class="col-md-3 control-label" for="type"><?php echo TEXT_REPORT_ENTITY ?></label>
      <div class="col-md-9">	
    	  <?php echo select_tag('entities_id',entities::get_choices(), $obj['entities_id'],array('class'=>'form-control input-large required','onChange'=>'ext_get_entities_fields(this.value)')) ?>        
      </div>			
    </div>
        
    
    <div id="reports_entities_fields"></div>  
          
  </div>
  
  <div class="tab-pane fade" id="access">
    <p><?php echo TEXT_EXT_USERS_GROUPS_INFO ?></p>
    
    <?php foreach(access_groups::get_choices(false) as $group_id=>$group_name): ?>
    <div class="form-group">
    	<label class="col-md-3 control-label" for="allowed_groups"><?php echo $group_name ?></label>
      <div class="col-md-9">	
    	   <?php echo select_tag('access[' . $group_id . ']',array(''=>'','view'=>TEXT_VIEW_ONLY_ACCESS,'full'=>TEXT_FULL_ACCESS),calendar::get_access_by_report($obj['id'],$group_id),array('class'=>'form-control input-medium'))?>
      </div>			
    </div>
    <?php endforeach ?>
     
  </div>  
</div>

 
 
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>

  $(function() { 
    $('#configuration_form').validate();
                        
    ext_get_entities_fields($('#entities_id').val());
                                                                              
  });
  
function ext_get_entities_fields(entities_id)
{ 
  $('#reports_entities_fields').html('<div class="ajax-loading"></div>');
   
  $('#reports_entities_fields').load('<?php echo url_for("ext/calendar/configuration","action=get_entities_fields")?>',{entities_id:entities_id, id:'<?php echo $obj["id"] ?>'},function(response, status, xhr) {
    if (status == "error") {                                 
       $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
    }
    else
    {
      appHandleUniform();
    }    
  }); 
   
  
}   

  
</script>   