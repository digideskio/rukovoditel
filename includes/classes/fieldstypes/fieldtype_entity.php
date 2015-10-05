<?php

class fieldtype_entity
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_ENTITY_TITLE);
  }
  
  function get_configuration($params = array())
  {
    $choices = array();
    $entity_info = db_find('app_entities',$params['entities_id']);
    $entities_query = db_query("select * from app_entities where parent_id=0 or parent_id='" . $entity_info['parent_id'] . "' order by parent_id, sort_order, name");
    while($entities = db_fetch_array($entities_query))
    {
      $choices[($entities['parent_id']>0 ? TEXT_SUB_ENTITIES:TEXT_TOP_ENTITIES)][$entities['id']] =  $entities['name'];
    }
  
    $cfg = array();
    $cfg[] = array('title'=>TEXT_SELECT_ENTITY, 
                   'name'=>'entity_id',
                   'tooltip'=>TEXT_FIELDTYPE_ENTITY_SELECT_ENTITY_TOOLTIP,
                   'type'=>'dropdown',
                   'choices'=>$choices,
                   'params'=>array('class'=>'form-control input-medium'));
                   
    $cfg[] = array('title'=>TEXT_DISPLAY_USERS_AS, 
                   'name'=>'display_as',
                   'tooltip'=>TEXT_DISPLAY_USERS_AS_TOOLTIP,
                   'type'=>'dropdown',
                   'choices'=>array('dropdown'=>TEXT_DISPLAY_USERS_AS_DROPDOWN,'checkboxes'=>TEXT_DISPLAY_USERS_AS_CHECKBOXES,'dropdown_muliple'=>TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE),
                   'params'=>array('class'=>'form-control input-medium'));  
                   
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));                       
    
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
    $parent_entity_item_id = $params['parent_entity_item_id'];
    
    $cfg = fields_types::parse_configuration($field['configuration']);
    
    $entity_info = db_find('app_entities',$cfg['entity_id']);
            
    
                        
    $choices = array();
    
    //add empty value if dispalys as dropdown and field is not requireed
    if($field['is_required']!=1 and $cfg['display_as']=='dropdown')
    {
      $choices[''] = TEXT_NONE;  
    }
    
    $listing_sql_query = '';
    $listing_sql_query_join = '';
    
    if($parent_entity_item_id>0 and $entity_info['parent_id']>0)
    {
      $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
    }
      
    $default_reports_query = db_query("select * from app_reports where entities_id='" . db_input($cfg['entity_id']). "' and reports_type='default'");
    if($default_reports = db_fetch_array($default_reports_query))
    {
      $_POST['reports_id'] = $default_reports['id'];
      $_POST['listing_order_fields'] = $default_reports['listing_order_fields'];
      $listing_sql_query = reports::add_filters_query($default_reports['id'],$listing_sql_query);
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
    
    //echo '<pre>';
    //print_r($cfg);
    
    
    
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : ''); 
    
    if($cfg['display_as']=='dropdown')
    {
      $attributes = array('class'=>'form-control chosen-select ' . $cfg['width'] . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
      
      return select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes);
    }
    elseif($cfg['display_as']=='checkboxes')
    {
      $attributes = array('class'=>'field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
      
      return '<div class="checkboxes_list ' . ($field['is_required']==1 ? ' required':'') . '">' . select_checkboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . '</div>';
    }
    elseif($cfg['display_as']=='dropdown_muliple')
    {
      $attributes = array('class'=>'form-control chosen-select ' . $cfg['width'] . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),
                          'multiple'=>'multiple',
                          'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
      
      return select_tag('fields[' . $field['id'] . '][]',$choices,explode(',',$value),$attributes);
    }
  }
  
  function process($options)
  {        
    return (is_array($options['value']) ? implode(',',$options['value']) : $options['value']);
  }
  
  function output($options)
  {
    if(strlen($options['value'])==0)
    {
      return '';
    }
        
    $cfg = fields_types::parse_configuration($options['field']['configuration']);
    
    $output = array();
    foreach(explode(',',$options['value']) as $item_id)
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
    
    
    if(isset($options['is_export']))
    {
      return implode(', ',$output);
    }
    else
    {
      return implode('<br>',$output);
    } 
  }  
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql = array();
    
    foreach(explode(',',$filters['filters_values']) as $v)
    {
      $sql[] = ($filters['filters_condition']=='include' ? '': '!') . "find_in_set(" . $v . ",field_" . $filters['fields_id'] . ")";
    }
    
    $sql_query[] = '(' . implode(' or ',$sql) . ')';
              
    return $sql_query;
  }  
}