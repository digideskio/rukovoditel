<?php

//check access
if($app_user['group_id']>0)
{
  redirect_to('dashboard/access_forbidden');
}

switch($app_module_action)
{
  case 'sort_fields':
      if(isset($_POST['fields_in_listing'])) 
      {
        $sql_data = array('fields_in_listing'=>str_replace('form_fields_','',$_POST['fields_in_listing']));
                        
        db_perform('app_ext_ganttchart',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
        
      }
      exit();
    break;
  case 'save':              
      $sql_data = array('name'=>$_POST['name'],                        
                        'entities_id'=>$_POST['entities_id'],                        
                        'weekends'=>(isset($_POST['weekends']) ? implode(',',$_POST['weekends']):''),
                        'gantt_date_format'=>$_POST['gantt_date_format'],
                        'start_date'=>$_POST['start_date'],                        
                        'end_date'=>$_POST['end_date'],
                        'progress'=>(isset($_POST['progress']) ? $_POST['progress']:''),                                                                                                
                        );
                        
                                                            
      if(isset($_GET['id']))
      {        
        $ganttchart_id =$_GET['id'];
        
        db_perform('app_ext_ganttchart',$sql_data,'update',"id='" . db_input($ganttchart_id) . "'");              
      }
      else
      {                               
        db_perform('app_ext_ganttchart',$sql_data); 
        $ganttchart_id = db_insert_id();                   
      }
      
      db_query("delete from app_ext_ganttchart_access where ganttchart_id='" . db_input($ganttchart_id) . "'");
      
      foreach($_POST['access'] as $group_id=>$access)
      {
        if(strlen($access)==0) continue;
        
        $sql_data = array('ganttchart_id'=>$ganttchart_id,                        
                          'access_groups_id'=>$group_id,                        
                          'access_schema'=>$access,                                                                                                  
                          );
                          
        db_perform('app_ext_ganttchart_access',$sql_data);
      }
                                                      
      redirect_to('ext/ganttchart/configuration');
      
    break;
  case 'delete':
      $obj = db_find('app_ext_ganttchart',$_GET['id']);
      
      db_delete_row('app_ext_ganttchart',$_GET['id']);                              
      db_delete_row('app_ext_ganttchart_access',$_GET['id'],'ganttchart_id');
      db_delete_row('app_ext_ganttchart_depends',$_GET['id'],'ganttchart_id');
      
      $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$obj['name']),'success');
      
      redirect_to('ext/ganttchart/configuration');
    break;      
  case 'get_entities_fields':
      
        $entities_id = $_POST['entities_id'];
        
        $obj = array();

        if(isset($_POST['id']))
        {
          $obj = db_find('app_ext_ganttchart',$_POST['id']);  
        }
        else
        {
          $obj = db_show_columns('app_ext_ganttchart');
        }
        
        $start_date_fields = array();
        $fields_query = db_query("select * from app_fields where type in ('fieldtype_input_date') and entities_id='" . db_input($entities_id) . "'");
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
        $fields_query = db_query("select * from app_fields where type in ('fieldtype_input_date') and entities_id='" . db_input($entities_id) . "'");
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
        
        
        $progress_fields = array();
        $fields_query = db_query("select * from app_fields where type in ('fieldtype_progress') and entities_id='" . db_input($entities_id) . "'");
        while($fields = db_fetch_array($fields_query))
        {
          $progress_fields[$fields['id']] = ($fields['type']=='fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']); 
        }
        
        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GANTT_PROGRESS . '</label>
            <div class="col-md-9">	
          	   ' .  select_tag('progress',$progress_fields,$obj['progress'],array('class'=>'form-control input-large')) . '               
            </div>			
          </div>
        ';
        
        

        
        echo $html;
        
      exit();
    break;
}