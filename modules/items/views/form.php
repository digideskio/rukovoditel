
<?php echo ajax_modal_template_header((strlen($entity_cfg['window_heading'])>0 ? $entity_cfg['window_heading'] : TEXT_INFO)) ?>

<?php echo form_tag('items_form', url_for('items/','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('enctype'=>'multipart/form-data','class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
     
<?php echo input_hidden_tag('path',$_GET['path']) ?>
<?php echo input_hidden_tag('redirect_to',$app_redirect_to) ?>
<?php if(isset($_GET['related'])) echo input_hidden_tag('related',$_GET['related']) ?>

<?php

  $html_user_password .='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="password">' . TEXT_FIELDTYPE_USER_PASSWORD_TITLE . '</label>
            <div class="col-md-9">	
          	  ' . input_password_tag('password',array('class'=>'form-control input-medium','autocomplete'=>'off')) . '
              ' . tooltip_text(TEXT_FIELDTYPE_USER_PASSWORD_TOOLTIP) . '
            </div>			
          </div>        
        ';   


  $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
      
  $count_tabs = db_count('app_forms_tabs',$current_entity_id,"entities_id");
  
  if($count_tabs>1)
  {
    $count = 0;
    $html = '<ul class="nav nav-tabs" id="form_tabs">';
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {
      $html .= '<li ' . ($count==0 ? 'class="active"':'') . ' ><a data-toggle="tab" href="#form_tab_' . $tabs['id'] . '">' . $tabs['name'] . '</a></li>';
      $count++;
    }
    $html .= '</ul>';
    
    
    $html .= '<div class="tab-content">';
    $count = 0;
    
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {
              
      $html .= '
        <div class="tab-pane fade ' . ($count==0 ? 'active in':'') . '" id="form_tab_' . $tabs['id'] . '">
      ';
      
      $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form() . ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
      while($v = db_fetch_array($fields_query))
      {
        //check field access
        if(isset($fields_access_schema[$v['id']])) continue;
        
        
        $html .='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' . fields_types::get_option($v['type'],'name',$v['name']) . '</label>
            <div class="col-md-9">	
          	  ' . fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id,'form'=>'item')) . '
              ' . tooltip_text($v['tooltip']) . '
            </div>			
          </div>        
        ';   
        
        //including user password field for new user form
        if($v['type']=='fieldtype_user_username' and !isset($_GET['id']))
        {
          $html .= $html_user_password;
        }     
      }
      
      $html .= '</div>';
      
      $count++;
    }
    
    $html .= '</div>';
  
  }
  else
  {  
    $html = '<table class="form_table">';
    $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(). ") and  f.entities_id='" . db_input($current_entity_id) . "' order by f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {       
      //check field access
      if(isset($fields_access_schema[$v['id']])) continue;
        
      $html .='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' . fields_types::get_option($v['type'],'name',$v['name']) . '</label>
            <div class="col-md-9">	
          	  ' . fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id,'form'=>'item')) . '
              ' . tooltip_text($v['tooltip']) . '
            </div>			
          </div>        
        ';  
        
      //including user password field for new user form
      if($v['type']=='fieldtype_user_username' and !isset($_GET['id']))
      {
        $html .= $html_user_password;
      }          
    }
    $html .= '</table>';
  }
  
  echo $html;
?>
 </div>
</div>
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
                    
    $('#items_form').validate({ignore:'',      
      rules:{
        <?php echo fields::render_required_ckeditor_ruels($current_entity_id); ?>
      },
      messages: {			    
        <?php echo fields::render_required_messages($current_entity_id); ?>			   
			},
      submitHandler: function(form)
      {                                              
        <?php if($current_entity_id==1){ echo 'validate_user_form(form,\'' . url_for('users/validate_form',(isset($_GET['id']) ? 'id=' . $_GET['id']:'') ). '\');'; }else{ echo 'form.submit();'; } ?>        
      },      
      invalidHandler: function(e, validator) {
  			var errors = validator.numberOfInvalids();
  			if (errors) {
  				var message = '<?php echo TEXT_ERROR_GENERAL ?>';
  				$("div#form-error-container").html('<div class="alert alert-danger">'+message+'</div>');
  				$("div#form-error-container").show();
          $("div#form-error-container").delay(5000).fadeOut();				
  			} 
		}});
                                                                        
  });
  
</script>   
    