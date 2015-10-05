<?php

foreach(explode(',',$_POST['listing_order_fields']) as $order_field)
{
  if(strlen($order_field)==0) continue; 
  
  $order = explode('_',$order_field);
  
  $field_id = $order[0];
  $order_cause =  $order[1]; 
  
  $listing_order_fields_id[]=$field_id;
  $listing_order_clauses[$field_id] = $order_cause;
  
  $field_info = db_find('app_fields',$field_id);
  
  if(in_array($field_info['type'],array('fieldtype_created_by','fieldtype_date_added','fieldtype_id')))
  {
    $listing_order_fields[] = 'e.' . str_replace('fieldtype_','',$field_info['type']) . ' ' . $order_cause;
  }
  elseif(in_array($field_info['type'],array('fieldtype_dropdown')))
  {
    $listing_sql_query_join .= " left join app_fields_choices fc on fc.id=e.field_" . $field_id;
    $listing_order_fields[] = "fc.sort_order " . $order_cause . ", fc.name " . $order_cause;
  }
  else
  {
    $listing_order_fields[] = 'field_' . $field_id . ' ' . $order_cause;
  }
}



if(count($listing_order_fields)>0)
{
  $listing_sql_query .= " order by " . implode(',',$listing_order_fields);
}
else
{
  $listing_sql_query .= " order by e.id ";
}