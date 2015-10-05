<?php

class fieldtype_related_records
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_RELATED_RECORDS_TITLE);
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
                   'tooltip'=>TEXT_FIELDTYPE_RELATED_RECORDS_SELECT_ENTITY_TOOLTIP . ' ' . $entity_info['name'],
                   'type'=>'dropdown',
                   'choices'=>$choices,
                   'params'=>array('class'=>'form-control input-medium'));
                   
      
    
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
    return false;
  }
  
  function process($options)
  {        
    return false;
  }
  
  function output($options)
  {
    global $current_path_array, $current_entity_id, $current_item_id, $current_path,$app_user;
    
    $cfg = fields_types::parse_configuration($options['field']['configuration']);
    
    //output count of relation items in listing
    if(isset($options['is_listing']))
    {
      return $options['value'];
    }
    
    
    $access_schema = users::get_entities_access_schema($cfg['entity_id'],$app_user['group_id']);
    
    
    //output list of relation items on info page
    $html = '';
           
    $items_list = array();
    
    $related_items_query = db_query("select * from app_related_items where entities_id = '"  . db_input($current_entity_id) . "' and items_id = '" . db_input($current_item_id) . "' and related_entities_id='" . db_input($cfg['entity_id']) . "'");
    while($related_items = db_fetch_array($related_items_query))
    {         
    
      //check assigned access
      if(users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0)
      {
        if(!users::has_access_to_assigned_item($cfg['entity_id'],$related_items['related_items_id']))
        {
          continue; 
        }
      }
       
      $heading_field_id = fields::get_heading_id($related_items['related_entities_id']);
      $item_info = db_find('app_entity_' . $related_items['related_entities_id'],$related_items['related_items_id']);
    
      $name =  ($heading_field_id>0 ? $item_info['field_' . $heading_field_id] : $item_info['id']);
      
      $path_info = items::get_path_info($related_items['related_entities_id'],$related_items['related_items_id']);
    
      $items_list[] = array('path'=>$path_info['full_path'],'name'=>$name,'id'=>$related_items['id']);
    }
    
    $related_items_query = db_query("select * from app_related_items where related_entities_id='" . db_input($current_entity_id) . "' and related_items_id='" . db_input($current_item_id) . "' and entities_id = '"  . db_input($cfg['entity_id']) . "'");
    while($related_items = db_fetch_array($related_items_query))
    {    
      //check assigned access
      if(users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0)
      {
        if(!users::has_access_to_assigned_item($cfg['entity_id'],$related_items['items_id']))
        {
          continue; 
        }
      }
                    
      $heading_field_id = fields::get_heading_id($related_items['entities_id']);
      $item_info = db_find('app_entity_' . $related_items['entities_id'],$related_items['items_id']);
    
      $name =  ($heading_field_id>0 ? $item_info['field_' . $heading_field_id] : $item_info['id']);
      
      $path_info = items::get_path_info($related_items['entities_id'],$related_items['items_id']);
    
      $items_list[] = array('path'=>$path_info['full_path'],'name'=>$name,'id'=>$related_items['id']);
    }
        
    if(count($items_list)>0)    
    {      
      $html .= '<table class="table">';
      foreach($items_list as $v)
      {
        $html .= '
          <tr id="related-records-' . $v['id'] . '">
            <td class="related-records-name"><a href="' . url_for('items/info','path=' . $v['path']). '">' . $v['name']. '</a></td>
            <td align="right"> ' . (users::has_access('update',$access_schema) ? '<a onClick="return app_remove_related_item(' . $v['id'] . ')" href="" title="' . TEXT_BUTTON_DELETE_RELATION . '" class="btn btn-default btn-xs btn-xs-fa-centered"><i class="fa fa-chain-broken"></i></a>':'') . '</td>
          </tr>
        ';
      }
      $html .= '</table>';
    }
   
    if(count($current_path_array)>1)
    {     
      $path = app_get_path_to_parent_item($current_path_array) . '/' . $cfg['entity_id'];
    }
    else
    {
      $path = $cfg['entity_id'];
    } 
    
    
    if(users::has_access('create',$access_schema))
    {
      $html .= '
        <div class="action-button">
        
          ' . link_to_modalbox('<i class="fa fa-plus"></i> ' . TEXT_BUTTON_ADD,url_for('items/form','path=' . $path . '&related=' . $current_entity_id . '-' . $current_item_id),array('class'=>'btn btn-default btn-xs')) . ' &nbsp;          
          ' . link_to_modalbox('<i class="fa fa-link"></i> ' . TEXT_BUTTON_LINK,url_for('items/link_related_item','path=' . $current_path . '&related_entities=' . $cfg['entity_id']),array('class'=>'btn btn-default btn-xs')) . ' &nbsp;        
        </div>
        <script>
          function app_remove_related_item(id)
          {
            if(confirm(i18n["TEXT_ARE_YOU_SURE"]))
            {
              $.ajax({
                type: "POST",
                url: "' . url_for('items/items','action=remove_related_item&path=' . $current_path). '&id="+id,
                data: { id: id}
              }).done(function(){
                $("#related-records-"+id).fadeOut();
              })
            }
            return false;
          }
        </script>
        ';
      }
    
    
    return $html;
  }  
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql = array();
    
    if(strlen($filters['filters_values'])>0)
    {
      $sql_query[] = ($filters['filters_values']=='include' ? "field_" . $filters['fields_id'] . ">0": "(field_" . $filters['fields_id'] . "=0 or length(field_" . $filters['fields_id'] . ")=0)") ;
    }
                      
    return $sql_query;
  }  
}