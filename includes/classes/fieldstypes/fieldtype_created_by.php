<?php

class fieldtype_created_by
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name'=>TEXT_FIELDTYPE_CREATEDBY_TITLE);
  }
  
  function output($options)
  {
    if(isset($options['is_export']) and isset($options['users_cache'][$options['value']]))
    {
      return $options['users_cache'][$options['value']]['name'];
    }
    elseif(isset($options['users_cache'][$options['value']]))
    {
      return '<span ' . users::render_publi_profile($options['users_cache'][$options['value']]). '>' .$options['users_cache'][$options['value']]['name'] . '</span>';
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
  
    $sql = array();
    
    foreach(explode(',',$filters['filters_values']) as $v)
    {
      $sql[] = ($filters['filters_condition']=='include' ? '': '!') . "find_in_set(" . $v . ",created_by)";
    }
    
    $sql_query[] = '(' . implode(' or ',$sql) . ')';
              
    return $sql_query;
  }
}