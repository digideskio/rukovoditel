<?php

$field_info = db_find('app_fields',$_POST['fields_id']);
$filter_info = db_find('app_reports_filters',$_POST['id']);

$html = '';

$condition_html = '
  <div class="form-group">
  	<label class="col-md-3 control-label" for="filters_condition">' . TEXT_FILTERS_CONDITION . '</label>
    <div class="col-md-9">	
  	  ' . select_tag('filters_condition',array('include'=>TEXT_CONDITION_INCLUDE,'exclude'=>TEXT_CONDITION_EXCLUDE),$filter_info['filters_condition'],array('class'=>'form-control input-small')) . '
      ' .tooltip_text(TEXT_FILTERS_CONDITION_TOOLTIP) . '
    </div>			
  </div>  
';  

switch($field_info['type'])
{
  case 'fieldtype_related_records':
      $html = '
        <div class="form-group">
        	<label class="col-md-3 control-label" for="filters_condition">' . TEXT_FILTERS_DISPLAY . '</label>
          <div class="col-md-9">	
        	  ' . select_tag('values',array('include'=>TEXT_FILTERS_DISPLAY_WITH_RELATED_RECORDS,'exclude'=>TEXT_FILTERS_DISPLAY_WITHOUT_RELATED_RECORDS),$filter_info['filters_values'],array('class'=>'form-control')) . '            
          </div>			
        </div>  
      ';       
    break;
  case 'fieldtype_entity':
      $cfg = fields_types::parse_configuration($field_info['configuration']);
      
      $entity_info = db_find('app_entities',$cfg['entity_id']);
      
      if($entity_info['parent_id']>0 and !isset($_POST['path']))
      {
        $html = '
        <div class="form-group">
        	<label class="col-md-3 control-label" for="values"></label>
          <div class="col-md-9">	
        	  ' . TEXT_FILTER_FIELD_VALUES_NOT_AVAILABLE . '            
          </div>			
        </div>
        '; 
        
        break;
      }

      $listing_sql_query = '';
      $listing_sql_query_join = '';
                  
      if($entity_info['parent_id']>0 and isset($_POST['path']))
      {
        $path_array = explode('/',$_POST['path']);
        $v = explode('-',$path_array[count($path_array)-2]);
        $parent_entity_item_id = $v[1];
        
        $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
      }
        
      $default_reports_query = db_query("select * from app_reports where entities_id='" . db_input($cfg['entity_id']). "' and reports_type='default'");
      if($default_reports = db_fetch_array($default_reports_query))
      {
        $_POST['reports_id'] = $default_reports['id'];
        $_POST['listing_order_fields'] = $default_reports['listing_order_fields'];
        
        $listing_sql_query = reports::add_filters_query($_POST['reports_id'],$listing_sql_query);
        
        require(component_path('items/add_order_query'));
      }
      else
      {
        $listing_sql_query .= " order by e.id";
      }
      
      $field_heading_id = 0;
      $fields_query = db_query("select f.* from app_fields f where f.is_heading=1 and  f.entities_id='" . db_input($cfg['entity_id']) . "'");
      if($fields = db_fetch_array($fields_query))
      {
        $field_heading_id = $fields['id'];
      }
      
      $choices = array();
                      
      $listing_sql = "select e.* from app_entity_" . $cfg['entity_id'] . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query;
      $items_query = db_query($listing_sql);
      while($item = db_fetch_array($items_query))
      {
        if($field_heading_id>0)
        {
          $choices[$item['id']] = $item['field_' . $field_heading_id];
        }
        else
        {
          $choices[$item['id']] = $item['id'];
        } 
      }
      
      $html = $condition_html . 
        '<div class="form-group">
        	<label class="col-md-3 control-label" for="values">' . TEXT_FILTER_BY_VALUES. '</label>
          <div class="col-md-9">	
        	  ' . select_checkboxes_tag('values',$choices,$filter_info['filters_values']) . '            
          </div>			
        </div>';

      
    break;
  case 'fieldtype_checkboxes':
  case 'fieldtype_radioboxes':
  case 'fieldtype_dropdown':
  case 'fieldtype_grouped_users':
      $choices = fields_choices::get_choices($field_info['id'],false);
      
      $html = $condition_html . 
        '<div class="form-group">
        	<label class="col-md-3 control-label" for="values">' . TEXT_FILTER_BY_VALUES. '</label>
          <div class="col-md-9">	
        	  ' . select_checkboxes_tag('values',$choices,$filter_info['filters_values']) . '            
          </div>			
        </div>';
            
    break;
  case 'fieldtype_created_by':
  case 'fieldtype_users':
                    
      $access_schema = users::get_entities_access_schema_by_groups($field_info['entities_id']);
      
      $choices = array();
      $users_query = db_query("select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 order by u.field_8, u.field_7");
      while($users = db_fetch_array($users_query))
      {
        if($users['field_6']==0 or in_array('view',$access_schema[$users['field_6']]) or in_array('view_assigned',$access_schema[$users['field_6']]))
        {               
          $group_name = (strlen($users['group_name'])>0 ? $users['group_name'] : TEXT_ADMINISTRATOR);
          $choices[$group_name][$users['id']] = $users['field_8'] . ' ' . $users['field_7'];
        } 
      }
            
      $html = $condition_html . 
        '<div class="form-group">
        	<label class="col-md-3 control-label" for="values">' . TEXT_FILTER_BY_USERS. '</label>
          <div class="col-md-9">	
        	  ' . select_checkboxes_tag('values',$choices,$filter_info['filters_values']) . '            
          </div>			
        </div>';
    break;
  case 'fieldtype_input_numeric':
  case 'fieldtype_input_numeric_comments':
  case 'fieldtype_formula':
  
      $html = '
          <div class="form-group">
          	<label class="col-md-3 control-label" for="values">' . TEXT_VALUES . '</label>
            <div class="col-md-9">	
          	  ' . input_tag('values',$filter_info['filters_values'],array('class'=>'form-control')) . input_hidden_tag('filters_condition','include'). '
              ' .tooltip_text(TEXT_FILTERS_NUMERIC_FIELDS_TOOLTIP) . '
            </div>			
          </div>  
        ';  
       
    break; 
  case 'fieldtype_date_added':
  case 'fieldtype_input_date':
  case 'fieldtype_input_datetime':
  
      $values = explode(',',$filter_info['filters_values']);
      
      $html = '
          <div class="form-group">
          	<label class="col-md-3 control-label" for="values_0">' . TEXT_FILTER_BY_DAYS . '</label>
            <div class="col-md-9">	
          	  ' .  input_tag('values[0]',$values[0],array('class'=>'form-control')) . input_hidden_tag('filters_condition','include'). '
              ' .tooltip_text(TEXT_FILTER_BY_DAYS_TOOLTIP) . '
            </div>			
          </div>  
          
          <div class="form-group">
          	<label class="col-md-3 control-label" for="values">' . TEXT_FILTER_BY_DATES . '</label>
            <div class="col-md-9">	
          	  <p class="form-control-static">' . TEXT_FILTER_BY_DATES_TOOLTIP . '</p>              
            </div>			
          </div>
          
          <div class="form-group">
          	<label class="col-md-3 control-label" for="values">' . TEXT_DATE_FROM . '</label>
            <div class="col-md-9">	
          	  <div class="input-group input-small date datepicker">' . input_tag('values[1]',$values[1],array('class'=>'form-control input-small')). '<span class="input-group-btn"><button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button></span></div>              
            </div>			
          </div> 
          
          <div class="form-group">
          	<label class="col-md-3 control-label" for="values">' . TEXT_DATE_TO . '</label>
            <div class="col-md-9">	
          	  <div class="input-group input-small date datepicker">' . input_tag('values[2]',$values[2],array('class'=>'form-control input-small datepicker')). '<span class="input-group-btn"><button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button></span></div>              
            </div>			
          </div> 
        ';
      
      $html .= '
              
              
              <script>
            $(function() {                                
               $(".datepicker").click(function(){                 
                 $("#values_0").val("")
               })
               
               $("#values_0").click(function(){                 
                 $("#values_1").val("")
                 $("#values_2").val("")
               })                     
            });
            </script>  
              ';       
    break;   
}

echo $html;


exit();