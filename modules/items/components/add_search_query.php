<?php

$search_fields = fields::get_search_feidls($current_entity_id);

if(count($search_fields)>0)
{  
  $sql_query = array();
  foreach(explode(' ',$_POST['search_keywords']) as $keyword)
  {
    foreach($search_fields as $id)
    {
      $sql_query[] = "field_" . $id . " like '%" . db_input($keyword)  ."%'";   
    }
  }
  
  $listing_sql_query .= ' and (' . implode(' or ',$sql_query) . ')';
}