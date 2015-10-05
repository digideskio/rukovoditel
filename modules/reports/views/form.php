
<?php echo ajax_modal_template_header(TEXT_HEADING_REPORTS_IFNO) ?>

<?php echo form_tag('users_groups_form', url_for('reports/reports','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>

<div class="modal-body">
  <div class="form-body">
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="entities_id"><?php echo TEXT_REPORT_ENTITY ?></label>
    <div class="col-md-9">	
  	  <?php 
        
        $choices = entities::get_choices();
        
        if($app_user['group_id']>0)
        {
          $choices_new = array();
          
          foreach($choices as $k=>$v)
          {
            $acccess_query = db_query("select * from app_entities_access where access_groups_id='" . db_input($app_user['group_id']) . "' and entities_id='" . db_input($k) . "' and find_in_set('reports',access_schema)");
            if($acccess = db_fetch_array($acccess_query))
            {
              $choices_new[$k] = $v;
            }
          }
          
          $choices = $choices_new;
        }
        
        echo select_tag('entities_id',$choices,$obj['entities_id'],array('class'=>'form-control input-large required')) 
      
      ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large required')) ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="in_menu"><?php echo TEXT_IN_MENU ?></label>
    <div class="col-md-9">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('in_menu','1',array('checked'=>$obj['in_menu'])) ?></label></div>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="in_dashboard"><?php echo TEXT_IN_DASHBOARD ?></label>
    <div class="col-md-9">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('in_dashboard','1',array('checked'=>$obj['in_dashboard'])) ?></label></div>
    </div>			
  </div> 
      
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
    $('#users_groups_form').validate();                                                                  
  });
  
</script>   
    
 
