<?php

class fields_types
{
  public static function get_reserved_types()
  {
    return array('fieldtype_action',
                 'fieldtype_id',
                 'fieldtype_date_added',
                 'fieldtype_created_by'
                 );
  }
  
  public static function get_reserved_data_types()
  {
    return array('fieldtype_id',
                 'fieldtype_date_added',
                 'fieldtype_created_by',
                 );
  }
  
  public static function get_users_types()
  {
    return array('fieldtype_user_status',
                 'fieldtype_user_accessgroups',
                 'fieldtype_user_firstname',
                 'fieldtype_user_lastname',
                 'fieldtype_user_email',
                 'fieldtype_user_photo',                 
                 'fieldtype_user_username',
                 'fieldtype_user_language',
                 'fieldtype_user_skin'
                 );
  }
  
  public static function get_types_for_filters()
  {
    return array('fieldtype_checkboxes',
                 'fieldtype_radioboxes', 
                 'fieldtype_created_by',
                 'fieldtype_date_added',
                 'fieldtype_dropdown',
                 'fieldtype_dropdown_mulitple',                 
                 'fieldtype_formula',
                 'fieldtype_input_date',
                 'fieldtype_input_datetime',
                 'fieldtype_input_numeric',
                 'fieldtype_input_numeric_comments',
                 'fieldtype_grouped_users',
                 'fieldtype_users',
                 'fieldtype_entity',
                 'fieldtype_related_records'
                 );
  }
  
  public static function get_types_excluded_in_form()
  {
    return array('fieldtype_related_records',                 
                 );
  }
  
  public static function get_types_for_filters_list()
  {
    return "'" . implode("','", fields_types::get_types_for_filters()) . "'";
  }
  
  public static function get_users_types_list()
  {
    return "'" . implode("','", fields_types::get_users_types()) . "'";
  }
  
  public static function get_reserverd_types_list()
  {
    return "'" . implode("','", fields_types::get_reserved_types()) . "'";
  }
  
  public static function get_reserverd_data_types_list()
  {
    return "'" . implode("','", fields_types::get_reserved_data_types()) . "'";
  }
  
  public static function get_type_list_excluded_in_form()
  {
    return "'" . implode("','", fields_types::get_types_excluded_in_form())   . "',". fields_types::get_reserverd_types_list();
  }
  
  public static function get_tooltip($fieldtype)  
  {
    $tooltip = '';
    
    switch($fieldtype)
    {
      case 'fieldtype_input':
          $tooltip = TEXT_FIELDTYPE_INPUT_TOOLTIP;
        break;
      case 'fieldtype_input_numeric':
          $tooltip = TEXT_FIELDTYPE_INPUT_NUMERIC_TOOLTIP;
        break;
      case 'fieldtype_input_numeric_comments':
          $tooltip = TEXT_FIELDTYPE_INPUT_NUMERIC_COMMENTS_TOOLTIP;
        break;  
      case 'fieldtype_input_url':
          $tooltip = TEXT_FIELDTYPE_INPUT_URL_TOOLTIP;
        break;
      case 'fieldtype_input_date':
          $tooltip = TEXT_FIELDTYPE_INPUT_DATE_TOOLTIP;
        break;
      case 'fieldtype_input_datetime':
          $tooltip = TEXT_FIELDTYPE_INPUT_DATETIME_TOOLTIP;
        break;
      case 'fieldtype_input_file':
          $tooltip = TEXT_FIELDTYPE_INPUT_FILE_TOOLTIP;
        break;
      case 'fieldtype_attachments':
          $tooltip = TEXT_FIELDTYPE_ATTACHMENTS_TOOLTIP;
        break;
      case 'fieldtype_textarea':
          $tooltip = TEXT_FIELDTYPE_TEXTAREA_TOOLTIP;
        break;
      case 'fieldtype_textarea_wysiwyg':
          $tooltip = TEXT_FIELDTYPE_TEXTAREA_WYSIWYG_TOOLTIP;
        break;
      case 'fieldtype_dropdown':
          $tooltip = TEXT_FIELDTYPE_DROPDOWN_TOOLTIP;
        break;
      case 'fieldtype_dropdown_multiple':
          $tooltip = TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TOOLTIP;
        break;
      case 'fieldtype_checkboxes':
          $tooltip = TEXT_FIELDTYPE_CHECKBOXES_TOOLTIP;
        break;
      case 'fieldtype_radioboxes':
          $tooltip = TEXT_FIELDTYPE_RADIOBOXES_TOOLTIP;
        break;
      case 'fieldtype_formula':
          $tooltip = TEXT_FIELDTYPE_FORMULA_TOOLTIP;
        break;
      case 'fieldtype_users':
          $tooltip = TEXT_FIELDTYPE_USERS_TOOLTIP;
        break;
      case 'fieldtype_grouped_users':
          $tooltip = TEXT_FIELDTYPE_GROUPEDUSERS_TOOLTIP;
        break;
      case 'fieldtype_entity':
          $tooltip = TEXT_FIELDTYPE_ENTITY_TOOLTIP;
        break;
      case 'fieldtype_progress':
          $tooltip = TEXT_FIELDTYPE_PROGRESS_TOOLTIP;
        break;
      case 'fieldtype_related_records':
          $tooltip = TEXT_FIELDTYPE_RELATED_RECORDS_TOOLTIP;
        break;
    }
    
    return $tooltip;
  }
   
    
  public static function get_choices()
  {  
    $fieldtypes = array('fieldtype_input',
                        'fieldtype_input_numeric',
                        'fieldtype_input_numeric_comments',
                        'fieldtype_input_url',
                        'fieldtype_input_date',
                        'fieldtype_input_datetime',
                        'fieldtype_input_file',
                        'fieldtype_attachments',
                        'fieldtype_textarea',
                        'fieldtype_textarea_wysiwyg',
                        'fieldtype_dropdown',
                        'fieldtype_dropdown_multiple',
                        'fieldtype_progress',
                        'fieldtype_checkboxes',
                        'fieldtype_radioboxes',
                        'fieldtype_formula',
                        'fieldtype_users',
                        'fieldtype_grouped_users',
                        'fieldtype_entity',
                        'fieldtype_related_records',
                        );     
    
    foreach($fieldtypes as $class) 
    {                  
      $fieldtype = new $class;
      
      $choices[$class] = $fieldtype->options['title'];          
    }        
                 
    return $choices;
  }
  
  public static function get_title($class)
  {
    $fieldtype = new $class;
    
    return $fieldtype->options['title'];
  }
  
  public static function render_field_name($name, $class, $fields_id)
  {
    global $_GET; 
    
    $fieldtype = new $class;
    
    if(!isset($fieldtype->options['has_choices']))
    {
      $fieldtype->options['has_choices'] = false;
    }
    
    if($fieldtype->options['has_choices'])
    {
      return '<a href="' . url_for('entities/fields_choices','entities_id=' . $_GET['entities_id'] .  '&fields_id=' . $fields_id). '"><i class="fa fa-list"></i>&nbsp;' . $name . '</a>';
    }
    else
    {
      return $name;
    }
  }
  
  public static function render_configuration($cfg,$id)
  {
    $configuration = array();
    
    $obj = db_find('app_fields',$id);
    
    if(strlen($obj['configuration'])>0)
    {
      $configuration = fields_types::parse_configuration($obj['configuration']);
    }
            
    $html = '<table  class="form_table">';
    foreach($cfg as $v)
    {
      $field = '';
      switch($v['type'])
      {
        case 'dropdown':
            $field = select_tag('fields_configuration[' . $v['name'] . ']',$v['choices'],(isset($configuration[$v['name']]) ? $configuration[$v['name']] : ''), (isset($v['params']) ? $v['params']:array()));
          break;
        case 'checkbox':
            $field = '<div class="checkbox-list"><label class="checkbox-inline">' . input_checkbox_tag('fields_configuration[' . $v['name'] . ']',1,array('checked'=>(isset($configuration[$v['name']]) ? $configuration[$v['name']] : false))) . '</label></div>';
          break;
        case 'colorpicker':
            $field ='
              <div class="input-group input-small color colorpicker-default" data-color="' . (isset($configuration[$v['name']]) ? $configuration[$v['name']] : '#ff0000') . '" >
          	   ' . input_tag('fields_configuration[' . $v['name'] . ']',(isset($configuration[$v['name']]) ? $configuration[$v['name']] : ''),array('class'=>'form-control input-small')) . '
                <span class="input-group-btn">
          				<button class="btn btn-default" type="button"><i style="background-color: #3865a8;"></i>&nbsp;</button>
          			</span>
          		</div>
            ';
          break;  
        case 'input':
            $field = input_tag('fields_configuration[' . $v['name'] . ']',(isset($configuration[$v['name']]) ? $configuration[$v['name']] : ''), (isset($v['params']) ? $v['params']:array()));
          break;  
        case 'textarea':
            $field = textarea_tag('fields_configuration[' . $v['name'] . ']',(isset($configuration[$v['name']]) ? $configuration[$v['name']] : ''), (isset($v['params']) ? $v['params']:array()));
          break;
      }
      
      
      $html .= '
      
      <div class="form-group">
      	<label class="col-md-3 control-label" for="' . generate_id_from_name('fields_configuration[' . $v['name'] . ']') . '">' . $v['title'] . '</label>
        <div class="col-md-9">' .	
      	   $field . 
           (isset($v['tooltip']) ? tooltip_text($v['tooltip']):'')  . '
        </div>			
      </div>
      ';
    }
    
    return $html;
  }
  
  public static function prepare_configuration($v)
  {
    return json_encode($v);
  }
  
  public static function parse_configuration($v)
  {
    if(strlen($v)>0)
    {
      return json_decode($v,true);
    }
    else
    {
      return array();
    }
  } 
  
  public static function render($class,$field,$obj,$params=array())
  {
    $fieldtype = new $class;
    
    return $fieldtype->render($field,$obj,$params);
  }
  
  public static function process($options = array())
  {
    $fieldtype = new $options['class'];
    
    return $fieldtype->process($options);
  }
  
  public static function output($options = array())
  {
    $fieldtype = new $options['class'];
    
    return $fieldtype->output($options);
  }
  
  public static function reports_query($options = array())
  {
    $fieldtype = new $options['class'];
    
    if(method_exists($fieldtype,'reports_query'))
    { 
      return $fieldtype->reports_query($options);
    }
    else
    {
      return $options['sql_query'];
    }                  
  }
  
  public static function get_option($class,$key,$default = '')
  {
    $fieldtype = new $class;
    
    if(isset($fieldtype->options[$key]))
    {
      return $fieldtype->options[$key];
    }
    else
    {
      return $default;
    }
  }
  
  public static function recalculate_numeric_comments_sum($entity_id,$item_id)
  {
    $fields_query = db_query("select f.* from app_fields f where f.type  in ('fieldtype_input_numeric_comments') and  f.entities_id='" . db_input($entity_id) . "' and f.comments_status=1 order by f.comments_sort_order, f.name");
    while($fields = db_fetch_array($fields_query))
    {
      $total = 0;
    
      $comments_query = db_query("select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input($item_id) . "'");
      while($comments = db_fetch_array($comments_query))
      {
        $history_query = db_query("select * from app_comments_history where comments_id='" . db_input($comments['id']) . "' and fields_id='" . $fields['id']. "'");
        while($history = db_fetch_array($history_query))
        {        
          $total +=$history['fields_value'];        
        }      
      }
      
      $sql_data = array('field_' . $fields['id']=>$total);
      
      db_perform('app_entity_' . $entity_id,$sql_data,'update',"id='" . db_input($item_id) . "'");
    }
  }
  
  public static function get_types_wich_choices()
  {
    return array('fieldtype_dropdown','fieldtype_dropdown_multiple','fieldtype_radioboxes','fieldtype_grouped_users','fieldtype_checkboxes');
  }
}