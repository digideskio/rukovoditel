<?php

//check access
if($app_user['group_id']>0)
{
  redirect_to('dashboard/access_forbidden');
}

switch($app_module_action)
{      
  case 'save_personal':
        //reset access
        db_query("delete from app_ext_calendar_access where calendar_type='personal'");
        
        //insert access
        if(isset($_POST['allowed_groups']))
        {
          foreach($_POST['allowed_groups'] as $group_id)
          {
            $sql_data = array('access_groups_id'=>$group_id,'calendar_type'=>'personal','access_schema'=>'full');
            
            db_perform('app_ext_calendar_access',$sql_data);
          }
        }
      
        redirect_to('ext/calendar/configuration_personal');
    break;
    
  case 'save_public':
        //reset access
        db_query("delete from app_ext_calendar_access where calendar_type='public'");
        
        //insert access
        if(isset($_POST['access']))
        {
          foreach($_POST['access'] as $group_id=>$access_schema)
          {
            if(strlen($access_schema)>0)
            {
              $sql_data = array('access_groups_id'=>$group_id,'calendar_type'=>'public','access_schema'=>$access_schema);
              
              db_perform('app_ext_calendar_access',$sql_data);
            }
          }
        }
      
        redirect_to('ext/calendar/configuration_public');
    break; 
    
  case 'save_report':
      $sql_data = array('name'=>$_POST['name'],                        
                        'entities_id'=>$_POST['entities_id'],                                                                                              
                        'start_date'=>$_POST['start_date'],                        
                        'end_date'=>$_POST['end_date'],                                                                                                                        
                        );
                        
                                                            
      if(isset($_GET['id']))
      {        
        $calendar_id =$_GET['id'];
        
        db_perform('app_ext_calendar',$sql_data,'update',"id='" . db_input($calendar_id) . "'");              
      }
      else
      {                               
        db_perform('app_ext_calendar',$sql_data); 
        $calendar_id = db_insert_id();                   
      }
      
      db_query("delete from app_ext_calendar_access where calendar_id='" . db_input($calendar_id) . "'");
      
      foreach($_POST['access'] as $group_id=>$access)
      {
        if(strlen($access)==0) continue;
        
        $sql_data = array('calendar_id'=>$calendar_id,                        
                          'access_groups_id'=>$group_id,
                          'calendar_type' => 'report',                        
                          'access_schema'=>$access,                                                                                                  
                          );
                          
        db_perform('app_ext_calendar_access',$sql_data);
      }
                                                      
      redirect_to('ext/calendar/configuration_reports');      
    break;
    
  case 'delete':
      $obj = db_find('app_ext_calendar',$_GET['id']);
      
      db_delete_row('app_ext_calendar',$_GET['id']);                              
      db_delete_row('app_ext_calendar_access',$_GET['id'],'calendar_id');
            
      $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$obj['name']),'success');
      
      redirect_to('ext/calendar/configuration_reports');            
  case 'get_entities_fields':
      
        $entities_id = $_POST['entities_id'];
        
        $obj = array();

        if(isset($_POST['id']))
        {
          $obj = db_find('app_ext_calendar',$_POST['id']);  
        }
        else
        {
          $obj = db_show_columns('app_ext_calendar');
        }
        
        $start_date_fields = array();
        $fields_query = db_query("select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime') and entities_id='" . db_input($entities_id) . "'");
        while($fields = db_fetch_array($fields_query))
        {
          $start_date_fields[$fields['id']] = ($fields['type']=='fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']); 
        }
        
        $html = '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GANTT_START_DATE . '</label>
            <div class="col-md-9">	
          	   ' .  select_tag('start_date',$start_date_fields,$obj['start_date'],array('class'=>'form-control input-large required')) . '               
            </div>			
          </div>
        ';
        
        $end_date_fields = array();
        $fields_query = db_query("select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime') and entities_id='" . db_input($entities_id) . "'");
        while($fields = db_fetch_array($fields_query))
        {
          $end_date_fields[$fields['id']] = ($fields['type']=='fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']); 
        }
        
        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GANTT_END_DATE . '</label>
            <div class="col-md-9">	
          	   ' .  select_tag('end_date',$end_date_fields,$obj['end_date'],array('class'=>'form-control input-large required')) . '               
            </div>			
          </div>
        ';
                
        
        echo $html;
        
      exit();
    break;     
}