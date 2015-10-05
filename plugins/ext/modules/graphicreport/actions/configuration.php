<?php

//check access
if($app_user['group_id']>0)
{
  redirect_to('dashboard/access_forbidden');
}

switch($app_module_action)
{
  case 'save':
  
      $yaxis = array();
      foreach($_POST['yaxis'] as $v)
      {
        if($v>0) $yaxis[] = $v; 
      }
      
      $sql_data = array('name'=>$_POST['name'],                        
                        'entities_id'=>$_POST['entities_id'],
                        'allowed_groups'=>(isset($_POST['allowed_groups']) ? implode(',',$_POST['allowed_groups']):''),
                        'xaxis'=>$_POST['xaxis'],
                        'yaxis'=>implode(',',$yaxis),                        
                        'chart_type'=>$_POST['chart_type'],
                        'period'=>$_POST['period'],                        
                        );
                        
                                                            
      if(isset($_GET['id']))
      {        
        db_perform('app_ext_graphicreport',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
      }
      else
      {                               
        db_perform('app_ext_graphicreport',$sql_data);                    
      }
                                          
      redirect_to('ext/graphicreport/configuration');
      
    break;
  case 'delete':
      $obj = db_find('app_ext_graphicreport',$_GET['id']);
      
      db_delete_row('app_ext_graphicreport',$_GET['id']);                              
      
      $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$obj['name']),'success');
      
      redirect_to('ext/graphicreport/configuration');
    break;      
  case 'get_entities_fields':
      
        $entities_id = $_POST['entities_id'];
        
        $obj = array();

        if(isset($_POST['id']))
        {
          $obj = db_find('app_ext_graphicreport',$_POST['id']);  
        }
        else
        {
          $obj = db_show_columns('app_ext_graphicreport');
        }
        
        $xaxis_fields = array();
        $fields_query = db_query("select * from app_fields where type in ('fieldtype_date_added','fieldtype_input_date','fieldtype_input_datetime') and entities_id='" . db_input($entities_id) . "'");
        while($fields = db_fetch_array($fields_query))
        {
          $xaxis_fields[$fields['id']] = ($fields['type']=='fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']); 
        }
        
        $html = '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_HORIZONTAL_AXIS . '</label>
            <div class="col-md-9">	
          	   ' .  select_tag('xaxis',$xaxis_fields,$obj['xaxis'],array('class'=>'form-control input-large required')) . '
               ' . tooltip_text(TEXT_EXT_HORIZONTAL_AXIS_INFO) . '
            </div>			
          </div>
        ';
        
        
        $yaxis_fields = array();
        $yaxis_fields_select = array(''=>'');
        $fields_query = db_query("select * from app_fields where type in ('fieldtype_input_numeric','fieldtype_input_numeric_comments','fieldtype_formula') and entities_id='" . db_input($entities_id) . "'");
        while($fields = db_fetch_array($fields_query))
        {
          $yaxis_fields[$fields['id']] =  $fields['name']; 
          $yaxis_fields_select[$fields['id']] =  $fields['name'];
        }
        
        if(count($yaxis_fields)==0)
        {
          $yaxis_fields = array(''=>'');
        }
       
        $obj_yaxis = explode(',',$obj['yaxis']);        
        $is_required = true;        
        $key = 0;
        foreach($yaxis_fields as $v)
        {
          $html .= '
           <div class="form-group">
            	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_VERTICAL_AXIS . ' ' . ($key+1) . '</label>
              <div class="col-md-9">	
            	   ' .  select_tag('yaxis[]',($key==0 ? $yaxis_fields:$yaxis_fields_select),(isset($obj_yaxis[$key]) ? $obj_yaxis[$key]:''),array('class'=>'form-control input-large ' . ($is_required ? 'required':''))) . '
                 ' . tooltip_text(TEXT_EXT_VERTICAL_AXIS_INFO) . '
              </div>			
            </div>
          ';                  
          $is_required = false;
          $key++;
        }
        
        echo $html;
        
      exit();
    break;
}