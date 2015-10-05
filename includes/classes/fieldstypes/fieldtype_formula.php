<?php

class fieldtype_formula
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_FORMULA_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_FORMULA, 'name'=>'formula','type'=>'textarea','tooltip'=>TEXT_FORMULA_TIP,'params'=>array('class'=>'form-control'),);    
    
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
    return $obj['field_' . $field['id']] . input_hidden_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']]);
  }
  
  function process($options)
  { 
    $fields_cache = $options['fields_cache'];
    $field = $options['field'];
    
    $cfg = fields_types::parse_configuration($options['field']['configuration']);    
    
    $formula = $cfg['formula'];
      
    foreach($fields_cache as $id=>$value)
    {
      $formula = str_replace('[' . $id . ']',$value,$formula);
    } 
    
    if(strstr($formula,'{'))
    {
      $eval_str = 'function field_' . $field['id']. '_formula()' . $formula . '; $formula_value=field_' . $field['id']. '_formula();';
    }
    else
    {                  
      $eval_str = '$formula_value=' . $formula . ';';
    }        
    
    @$result = eval($eval_str);
            
    if($result!==null)
    {        
      $formula_value = TEXT_ERROR_FORMULA_CALCULATION;
    } 
                 
    return $formula_value;
  }
  
  function output($options)
  {
    return $options['value'];
  }
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
                
    $sql = reports::prepare_numeric_sql_filters($filters);
    
    if(count($sql)>0)
    {
      $sql_query[] =  implode(' and ', $sql);
    }
                
    return $sql_query;
  }  
}