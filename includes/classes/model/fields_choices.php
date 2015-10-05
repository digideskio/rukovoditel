<?php

class fields_choices
{

  public static function check_before_delete($id)
  {     
    return '';
  }
  
  public static function get_name_by_id($id)
  {
    $obj = db_find('app_fields_choices',$id);
    
    return $obj['name'];
  }
  
  public static function get_default_id($fields_id)
  {
    $obj_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($fields_id). "' and is_default=1 limit 1");
    
    if($obj = db_fetch_array($obj_query))
    {
      return $obj['id'];
    }
    else
    {
      return 0;
    } 
        
  }  
  
  public static function get_tree($fields_id,$parent_id = 0,$tree = array(),$level=0)
  {
    $choices_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($fields_id). "' and parent_id='" . db_input($parent_id). "' order by sort_order, name");
    
    while($v = db_fetch_array($choices_query))
    {
      $tree[] = array_merge($v,array('level'=>$level));
      
      $tree = fields_choices::get_tree($fields_id,$v['id'],$tree,$level+1);
    }
    
    return $tree;
  }
  
  public static function get_choices($fields_id,$add_empty = true)
  {
    $choices = array();
    
    $tree = fields_choices::get_tree($fields_id);
            
    if(count($tree)>0)
    {
      if($add_empty)
      {
        $choices[''] = '';
      }
      
      foreach($tree as $v)
      {
        $choices[$v['id']] = str_repeat(' - ',$v['level']) . $v['name'];
      }            
    }
    
    return $choices;        
  }
  
  public static function get_cache()
  {
    $list = array();
    
    $choices_query = db_query("select * from app_fields_choices");
    
    while($v = db_fetch_array($choices_query))
    {
      $list[$v['id']] = $v;
    }
    
    return $list;
  }
  
  public static function render_value($values = array(),$choices_cache, $is_export=false)
  {
    if(!is_array($values))
    {
      $values = explode(',',$values);
    }
    
    $html  = '';
    foreach($values as $id)
    {
      if(isset($choices_cache[$id]))
      {
        if($is_export)
        {
          $html .= (strlen($html)==0 ? $choices_cache[$id]['name'] : ', ' . $choices_cache[$id]['name']);
        }
        elseif(strlen($choices_cache[$id]['bg_color'])>0)
        {
          $html .= render_bg_color_block($choices_cache[$id]['bg_color'],$choices_cache[$id]['name']);
        }
        else
        {
          $html .= '<div>' . $choices_cache[$id]['name'] . '</div>';
        } 
      }
    }
    
    return $html;
  }
    
}