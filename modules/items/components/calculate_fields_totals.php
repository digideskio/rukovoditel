<?php

$sum_sql_query = array();
foreach($listing_numeric_fields as $id)
{
  $sum_sql_query[] = "sum(field_" . $id . ") as total_" . $id;
}

$totals_query = db_query("select " . implode(', ',$sum_sql_query) . " from app_entity_" . $current_entity_id . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query);
$totals = db_fetch_array($totals_query);

$html .= '
  <tfoot>
    <tr>
      <td></td>
';
foreach($listing_fields as $field)
{
  if(in_array($field['id'],$listing_numeric_fields))
  {
    $html .= '<td>' . $totals['total_' . $field['id']] . '</td>';
  }
  else
  {
    $html .= '<td></td>'; 
  }
}

if($reports_entities_id>0  and $current_entity_info['parent_id']>0 and strlen($app_redirect_to)>0)
{
  $html .= '<td></td>';
}

$html .= '
    </tr>
  </tfoot>   
';