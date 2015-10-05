<?php

switch($app_module_action)
{  
  case 'select':  
        if(isset($_POST['checked']))
        {
          $app_selected_items[$_POST['reports_id']][] = $_POST['id'];
        }
        else
        {
          $key = array_search($_POST['id'], $app_selected_items[$_POST['reports_id']]);
          if($key!==false)
          {
            unset($app_selected_items[$_POST['reports_id']][$key]);
          }
        }        
        
        $app_selected_items[$_POST['reports_id']] =  array_unique($app_selected_items[$_POST['reports_id']]);        
      exit();
    break;
  case 'select_all':
  
      if(isset($_POST['checked']))
      {
        $listing_sql_query = '';
        $listing_sql_query_join = '';
        
        if(strlen($_POST['search_keywords'])>0)
        {
          require(component_path('items/add_search_query'));
        }
        
        $listing_sql_query = reports::add_filters_query($_POST['reports_id'],$listing_sql_query);        
                
        if($parent_entity_item_id>0)
        {
          $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
        }
                
        $listing_sql_query = items::add_access_query($current_entity_id,$listing_sql_query);
        
        if(strlen($_POST['listing_order_fields'])>0)
        {
          require(component_path('items/add_order_query'));
        }
        
        $app_selected_items[$_POST['reports_id']] = array();
        $listing_sql = "select e.* from app_entity_" . $current_entity_id . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query;        
        $items_query = db_query($listing_sql);
        while($item = db_fetch_array($items_query))
        {
          $app_selected_items[$_POST['reports_id']][] = $item['id'];
        }        
      }
      else
      {
        $app_selected_items[$_POST['reports_id']] = array();
      }
      
      print_r($app_selected_items);
      
    break;
    
}    