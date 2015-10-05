<?php

class fieldtype_input_date
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_INPUT_DATE_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_DATE_BACKGROUND, 
                   'name'=>'background',
                   'type'=>'colorpicker',                   
                   'tooltip'=>TEXT_DATE_BACKGROUND_TOOLTIP);
                             
    return $cfg;
  } 
    
  function render($field,$obj,$params = array())
  {
    if(strlen($obj['field_' . $field['id']])>0)
    {
      $value = date('Y-m-d',$obj['field_' . $field['id']]);
    }
    else
    {
      $value = '';
    }
    
    return '<div class="input-group input-medium date datepicker">' . input_tag('fields[' . $field['id'] . ']',$value,array('class'=>'form-control fieldtype_input_date'. ($field['is_required']==1 ? ' required':''))) . '<span class="input-group-btn"><button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button></span></div>';
  }
  
  function process($options)
  {
    return get_date_timestamp($options['value']);
  }
  
  function output($options)
  {
    if(isset($options['is_export']))
    {
      return format_date($options['value']);
    }
    elseif(strlen($options['value'])>0)
    {
      $cfg = fields_types::parse_configuration($options['field']['configuration']);
      
      if(!isset($cfg['background'])) $cfg['background']='';
          
      if((date('Y-m-d',$options['value'])==date('Y-m-d') or $options['value']<time()) and strlen($cfg['background'])>0)
      {                
        return render_bg_color_block($cfg['background'],format_date($options['value']));
      }
      else
      {
        return format_date($options['value']);
      }
        
    }
    else
    {
      return '';
    }
  }
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql = reports::prepare_dates_sql_filters($filters);
        
    if(count($sql)>0)
    {
      $sql_query[] =  implode(' and ', $sql);
    }
              
    return $sql_query;
  }
}