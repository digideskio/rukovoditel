<?php

class reports
{
  public static function create_default_entity_report($entity_id,$reports_type) 
  {
    global $app_logged_users_id;
    
    $reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($entity_id). "' and reports_type='" . $reports_type . "' and created_by='" . $app_logged_users_id . "'");
    if(!$reports_info = db_fetch_array($reports_info_query))
    {
      $default_reports_query = db_query("select * from app_reports where entities_id='" . db_input($entity_id). "' and reports_type='default'");
      $default_reports = db_fetch_array($default_reports_query);
    
      $sql_data = array('name'=>'',
                       'entities_id'=>$entity_id,
                       'reports_type'=>$reports_type,                                              
                       'in_menu'=>0,
                       'in_dashboard'=>0,
                       'listing_order_fields'=>$default_reports['listing_order_fields'],
                       'created_by'=>$app_logged_users_id,
                       );
      db_perform('app_reports',$sql_data);
      
      $reports_id = db_insert_id();
      
      $filters_query = db_query("select rf.*, f.name from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input($default_reports['id']) . "' order by rf.id");
      while($v = db_fetch_array($filters_query))
      {
        $sql_data = array('reports_id'=>$reports_id,
                          'fields_id'=>$v['fields_id'],
                          'filters_condition'=>$v['filters_condition'],                                              
                          'filters_values'=>$v['filters_values'],
                          );
                                         
        db_perform('app_reports_filters',$sql_data);
      }
      
      $reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id). "'");
      $reports_info = db_fetch_array($reports_info_query);
    }
    
    return $reports_info;
  }
  
  public static function get_parent_reports($reports_id,$paretn_reports = array())
  {
    $report_info = db_find('app_reports',$reports_id);
    
    if($report_info['parent_id']>0)
    {
      $paretn_reports[] = $report_info['parent_id'];
      
      $paretn_reports = reports::get_parent_reports($report_info['parent_id'],$paretn_reports);
    }
    
    return $paretn_reports;
  }
  
  public static function auto_create_parent_reports($reports_id)
  {
    global $app_logged_users_id;
    
    $report_info = db_find('app_reports',$reports_id);
    $entity_info = db_find('app_entities',$report_info['entities_id']);
    
    if($entity_info['parent_id']>0 and $report_info['parent_id']==0)
    {
      $sql_data = array('name'=>'',
                        'entities_id'=>$entity_info['parent_id'],
                        'reports_type'=>'parent',                                              
                        'in_menu'=>0,
                        'in_dashboard'=>0,
                        'created_by'=>$app_logged_users_id,
                        );
                        
      db_perform('app_reports',$sql_data);
      
      $insert_id = db_insert_id();
      
      db_perform('app_reports',array('parent_id'=>$insert_id),'update',"id='" . db_input($reports_id) . "' and created_by='" . $app_logged_users_id . "'");
      
      reports::auto_create_parent_reports($insert_id);
    }
  }
  
  public static function add_filters_query($reports_id,$listing_sql_query)
  {
    $reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id). "'");
    if($reports_info = db_fetch_array($reports_info_query))
    {
      $sql_query = array();
      
      $filters_query = db_query("select rf.*, f.name,f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input($reports_info['id']) . "' order by rf.id");
      while($filters = db_fetch_array($filters_query))
      {                
        if(strlen($filters['filters_values'])>0)
        {       
          $sql_query = fields_types::reports_query(array('class'=>$filters['type'],'filters'=>$filters,'sql_query'=>$sql_query));
        }
      }
      
      if(count($sql_query)>0)
      {
        $listing_sql_query .= ' and (' . implode(' and ',$sql_query) . ')';
      } 
      
      //add filters for paretn report if exist
      if($reports_info['parent_id']>0)
      {
        $report_info = db_find('app_reports',$reports_info['parent_id']);
        $listing_sql_query .= ' and e.parent_item_id in (select e.id from app_entity_' . $report_info['entities_id']. ' e where e.id>0 ' .  items::add_access_query($report_info['entities_id'],'') . ' ' . reports::add_filters_query($reports_info['parent_id'],'')  . ')'; 	
      }                   
    }
                    
    return $listing_sql_query;
  }
  
  
  public static function prepare_dates_sql_filters($filters)
  {        
    if($filters['type']=='fieldtype_date_added')
    {
      $field_name = 'e.date_added';
    }
    else
    {
      $field_name = 'e.field_' . $filters['fields_id']; 
    }
     
    
    $sql = array();
    $values = explode(',',$filters['filters_values']);
    
    if(strlen($values[0])>0)
    {
      if(strstr($values[0],'-'))
      {
        $use_function = 'sub';
      }
      else
      {
        $use_function = 'add';  
      }
      
      $values[0] = str_replace(array('+','-'),'',$values[0]);
      
      $sql_or = array();
      foreach(explode('&',$values[0]) as $v)
      {         
        switch($use_function)
        {
          case 'add':
              $sql_or[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d')=date_format(DATE_ADD(now(),INTERVAL " . (int)$v . " DAY),'%Y-%m-%d')";
            break;
          case 'sub':
              $sql_or[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d')=date_format(DATE_SUB(now(),INTERVAL " . (int)$v . " DAY),'%Y-%m-%d')";
            break;
        }  
      }
      
      if(count($sql_or)>0) $sql[] = "(" . implode(' or ', $sql_or) . ")";
    }
    
    if(strlen($values[1])>0)
    {
      $sql[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d')>='" . db_input($values[1])  . "'";
    }
    
    if(strlen($values[2])>0)
    {
      $sql[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d')<='" . db_input($values[2])  . "'";
    }
            
    return $sql;
  }
  
  public static function prepare_numeric_sql_filters($filters)
  {
    $values = preg_split("/(&|\|)/",$filters['filters_values'],null,PREG_SPLIT_DELIM_CAPTURE);
    
    if(strlen($values[0])>0)
    {
      if($values[1]=='|')
      {
        $values = array_merge(array('','|'),$values);
      }
      else
      {
        $values = array_merge(array('','&'),$values);
      }
    }
    
    $sql = array();
    $sql_and = array();
    $sql_or = array();
    
    for($i=1;$i<count($values);$i+=2)
    {
      if(preg_match("/!=|>=|<=|>|</",$values[$i+1],$matches))
      {        
        $operator = $matches[0];
        $value = (float)str_replace($matches[0],'',$values[$i+1]);
      }
      else
      {
        $operator = '=';
        $value = (float)$values[$i+1];
      }
      
      switch($values[$i])
      {
        case '|':
            $sql_or[] = 'e.field_' . $filters['fields_id'] . $operator . $value;
          break;
        case '&':
            $sql_and[] = 'e.field_' . $filters['fields_id'] . $operator . $value;
          break;
      }
      
    }            
    
    if(count($sql_or)>0) $sql[] = "(" . implode(' or ', $sql_or) . ")";
    if(count($sql_and)>0) $sql[] = "(" . implode(' and ', $sql_and) . ")";
    
    return $sql;
  }
  
  public static function render_filters_dropdown_menu($report_id,$path='',$redirect_to='report',$parent_reports_id=0)
  {  
    $url_params = '';
    
    if(strlen($path)>0)
    {
      $url_params = '&path=' . $path;      
    }
    
    $parent_reports_param = '';
    if($parent_reports_id>0)
    {
      $url_params .= '&parent_reports_id=' . $parent_reports_id;
      
      $report_info = db_find('app_reports',$parent_reports_id);          
    }
    else
    {
      $report_info = db_find('app_reports',$report_id);  
    }
    
    $entity_info = db_find('app_entities',$report_info['entities_id']);
    
    
    
    $count_filters = 0;
    $html = '<ul class="dropdown-menu" role="menu">';
    $html .= '<li>' . link_to_modalbox(TEXT_FILTERS_FOR_ENTITY_SHORT . ': <b>' . $entity_info['name'] . '</b>',url_for('reports/filters_form','reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params )) . '</li>';
    $html .= '<li class="divider"></li>';
    
    $filters_query = db_query("select rf.*, f.name, f.type from app_reports_filters rf, app_fields f  where rf.fields_id=f.id and rf.reports_id='" . db_input(($parent_reports_id>0 ? $parent_reports_id:$report_id)) . "' order by rf.id");
    while($v = db_fetch_array($filters_query))
    {
      
      $edit_url = url_for('reports/filters_form','id=' . $v['id'] . '&reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params);
      $delete_url = url_for('reports/filters','action=delete&id=' . $v['id'] . '&reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params);
      
      $html .= '
        <li class="dropdown-submenu">' . link_to_modalbox(fields_types::get_option($v['type'],'name',$v['name']),$edit_url) . '
          <ul class="dropdown-menu">
            <li class="filters-values-content">
              '  . link_to_modalbox(reports::render_filters_values($v['fields_id'],$v['filters_values']),$edit_url) . '
            </li>
            <li class="divider"></li>
            <li>
      				' . link_to('<i class="fa fa-trash-o"></i> ' . TEXT_BUTTON_REMOVE_FILTER,$delete_url). '
      			</li>
          </ul>
        </li>
      ';
      
      $count_filters++;
    }
    $html .= '
      <li class="divider"></li>
			<li>
				' . link_to_modalbox('<i class="fa fa-plus-circle"></i> ' . TEXT_BUTTON_ADD_NEW_REPORT_FILTER,url_for('reports/filters_form','reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params )). '
			</li>
      ' . ($count_filters>0 ? '      
      <li>
				' . link_to('<i class="fa fa-trash-o"></i> ' . TEXT_BUTTON_REMOVE_ALL_FILTERS, url_for('reports/filters','action=delete&id=all&reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params )). '
			</li>':'') . '
    </ul>';
    
    return $html;
  
  }
  
  public static function render_filters_values($fields_id,$filters_values)
  {
    global $app_choices_cache, $app_users_cache;
    
    $field_info = db_find('app_fields',$fields_id);
    
    $html = '';
    
    switch($field_info['type'])
    {
      case 'fieldtype_related_records':
          $html = ($filters_values=='include' ? TEXT_FILTERS_DISPLAY_WITH_RELATED_RECORDS:TEXT_FILTERS_DISPLAY_WITHOUT_RELATED_RECORDS);
        break;
      case 'fieldtype_entity':
            
        $cfg = fields_types::parse_configuration($field_info['configuration']);
        
        $output = array();
        foreach(explode(',',$filters_values) as $item_id)
        {
          $items_info_sql = "select e.* from app_entity_" . $cfg['entity_id'] . " e where e.id='" . db_input($item_id). "'";
          $items_query = db_query($items_info_sql);
          if($item = db_fetch_array($items_query))
          {
            $field_heading_id = 0;
            $fields_query = db_query("select f.* from app_fields f where f.is_heading=1 and  f.entities_id='" . db_input($cfg['entity_id']) . "'");
            if($fields = db_fetch_array($fields_query))
            {
              $output[] = $item['field_' . $fields['id']];               
            }
            else
            {
              $output[] = $item['id'];
            }
          }
        } 
        
        $html = implode('<br>',$output); 
        break;
      case 'fieldtype_formula':
      case 'fieldtype_input_numeric':     
      case 'fieldtype_input_numeric_comments':
          $html = $filters_values;        
        break;
      case 'fieldtype_checkboxes':
      case 'fieldtype_radioboxes':
      case 'fieldtype_dropdown':
      case 'fieldtype_grouped_users':
          $list = array();
          foreach(explode(',',$filters_values) as $id)
          {
            if(isset($app_choices_cache[$id]))
            {
              $list[] = $app_choices_cache[$id]['name']; 
            }
          }
          
          $html = implode('<br>',$list);
          
        break;
      case 'fieldtype_date_added':
      case 'fieldtype_input_date':
      case 'fieldtype_input_datetime':
          $values = explode(',',$filters_values);
          
          if(strlen($values[0])>0)
          {
            $html =  $values[0];
          }
          
          if(strlen($values[1])>0)
          {
            $html =  TEXT_DATE_FROM . ': ' . format_date(get_date_timestamp($values[1])) . ' ';
          }
          
          if(strlen($values[2])>0)
          {
            $html =  TEXT_DATE_TO . ': ' . format_date(get_date_timestamp($values[2])) . ' ';
          }
          
        break;  
      case 'fieldtype_created_by':            
      case 'fieldtype_users':
          $list = array();
          foreach(explode(',',$filters_values) as $id)
          {
            if(isset($app_users_cache[$id]))
            {
              $list[] = $app_users_cache[$id]['name']; 
            }
          }
          
          $html = implode('<br>',$list);                  
        break;
    }
                
    return $html;
  }
}