<?php
$fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
$current_entity_info = db_find('app_entities',$current_entity_id);
$entity_cfg = entities::get_cfg($current_entity_id);

$user_has_comments_access = users::has_comments_access('view');
      
$html = '';

$listing_sql_query = '';
$listing_sql_query_join = '';

//add search query
if(strlen($_POST['search_keywords'])>0)
{
  $html .= '<div class="note note-info search-notes">' . sprintf(TEXT_SEARCH_RESULT_FOR,htmlspecialchars($_POST['search_keywords'])) . ' <span onClick="listing_reset_search(\'' . $_POST['listing_container'] . '\')" class="reset_search">' . TEXT_RESET_SEARCH . '</span></div>';
  require(component_path('items/add_search_query'));
}

//add filters query
if(isset($_POST['reports_id']))
{
  $listing_sql_query = reports::add_filters_query($_POST['reports_id'],$listing_sql_query);    
}

//filter items by paretn
if($parent_entity_item_id>0)
{
  $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
}

//check view assigned only access
$listing_sql_query = items::add_access_query($current_entity_id,$listing_sql_query);


//add order_query
$listing_order_fields_id = array();
$listing_order_fields = array();
$listing_order_clauses = array();
if(strlen($_POST['listing_order_fields'])>0)
{
  require(component_path('items/add_order_query'));
}

      
$html .= '
<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th>' . input_checkbox_tag('select_all_items',$_POST['reports_id'],array('class'=>'select_all_items')) . '</th>';

//render listing heading
$listing_fields = array();   
$listing_numeric_fields = array();
$fields_query = db_query("select f.*,if(length(f.short_name)>0,f.short_name,f.name) as name  from app_fields f where f.listing_status=1 and  f.entities_id='" . db_input($current_entity_id) . "' order by f.listing_sort_order, f.name");
while($v = db_fetch_array($fields_query))
{      
  //check field access
  if(isset($fields_access_schema[$v['id']]))
  {
    if($fields_access_schema[$v['id']]=='hide') continue;
  }
      
  if($v['type']!='fieldtype_action')
  {
    if(!isset($listing_order_clauses[$v['id']])) 
    {
      $listing_order_clauses[$v['id']] = 'asc';
    }
    
    $listing_order_action = 'onClick="listing_order_by(\'' . $_POST['listing_container'] . '\',\'' . $v['id'] . '\',\'' . (($listing_order_clauses[$v['id']]=='asc' and in_array($v['id'],$listing_order_fields_id)) ? 'desc':'asc'). '\')"';
  }
  else
  {
    $listing_order_action = '';
  }
  
  if(in_array($v['id'],$listing_order_fields_id))
  {
    $listing_order_css_class = 'class="listing_order listing_order_' . $listing_order_clauses[$v['id']] .'"';
  }
  else
  {
    $listing_order_css_class = 'class="listing_order"';
  }   
       
  $html .= '
      <th ' . $listing_order_action . ' ' . $listing_order_css_class . '><div>' . fields_types::get_option($v['type'],'name',$v['name']). '</div></th>
  ';
  
  $listing_fields[] = $v;
  
  if(in_array($v['type'],array('fieldtype_input_numeric','fieldtype_formula','fieldtype_input_numeric_comments')))
  {
    $listing_numeric_fields[] = $v['id']; 
  }
}  


  if(isset($_POST['reports_entities_id']) and $current_entity_info['parent_id']>0 and strlen($app_redirect_to)>0)
  {
    $html .= '
      <th>' . TEXT_RELATIONSHIP_HEADING . '</th>
    ';
  }
  
      
$html .= '
    </tr>
  </thead>
  <tbody>        
';


$reports_entities_id = (isset($_POST['reports_entities_id']) ? $_POST['reports_entities_id'] : 0);

if(!isset($app_selected_items[$_POST['reports_id']]))
{
  $app_selected_items[$_POST['reports_id']] = array();
}

//render listing body
$listing_sql = "select e.* from app_entity_" . $current_entity_id . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query;
$listing_split = new split_page($listing_sql,$_POST['listing_container']);
$items_query = db_query($listing_split->sql_query);
while($item = db_fetch_array($items_query))
{
  $html .= '
      <tr>
        <td>' . input_checkbox_tag('items_' . $item['id'],$item['id'],array('class'=>'items_checkbox','checked'=>in_array($item['id'],$app_selected_items[$_POST['reports_id']]))) . '</td>
  ';
  
  if($reports_entities_id>0  and $current_entity_info['parent_id']>0)
  {
    $path_info_in_report = items::get_path_info($_POST['reports_entities_id'],$item['id']);        
  }

  
  foreach($listing_fields as $field)
  {
  
    //check field access
    if(isset($fields_access_schema[$field['id']]))
    {
      if($fields_access_schema[$field['id']]=='hide') continue;
    }
    
    switch($field['type'])
    {
      case 'fieldtype_created_by':
          $value = $item['created_by'];
        break;
      case 'fieldtype_date_added':
          $value = $item['date_added'];                
        break;
      case 'fieldtype_action':                
      case 'fieldtype_id':
          $value = $item['id'];
        break;
      default:
          $value = $item['field_' . $field['id']]; 
        break;
    }
    
    
    $output_options = array('class'=>$field['type'],
                            'value'=>$value,
                            'field'=>$field,
                            'item'=>$item,
                            'is_listing'=>true,
                            'choices_cache'=>$app_choices_cache,
                            'users_cache'=>$app_users_cache,
                            'redirect_to' => $app_redirect_to,
                            'reports_id'=> ($reports_entities_id>0 ? $_POST['reports_id']:0),
                            'path'=> (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path']  :$current_path));
                            
    
    
                            
    if($field['is_heading']==1)
    {     
      $path = (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path']  :$current_path . '-' . $item['id']);
            
      $html .= '
          <td class="item_heading_td"><a href="' . url_for('items/info', 'path=' . $path . '&redirect_to=subentity') . '">' . fields_types::output($output_options) . '</a>
      ';
      
      if($entity_cfg['use_comments']==1 and $user_has_comments_access)
      {
        $html .= comments::get_last_comment_info($current_entity_id,$item['id'],$path);
      }
      
      $html .= '</td>';
    }
    else
    {
      $td_class = (in_array($field['type'],array('fieldtype_action','fieldtype_date_added','fieldtype_input_datetime')) ? 'class="' . $field['type'] . ' nowrap"':'class="' . $field['type'] . '"');      
      $html .= '
          <td ' . $td_class . '>' . fields_types::output($output_options) . '</td>
      ';
    } 
  }
  
  if($reports_entities_id>0  and $current_entity_info['parent_id']>0 and strlen($app_redirect_to)>0)
  {
    $html .= '
      <td><a href="' . url_for('items/info', 'path=' . $path_info_in_report['parent_path']) . '">' . $path_info_in_report['parent_name']. '</a></td>
    ';
  }
    
  
  $html .= '
      </tr>
  ';
}

if($listing_split->number_of_rows==0)
{
  $html .= '
    <tr>
      <td colspan="' . (count($listing_fields)+1) . '">' . TEXT_NO_RECORDS_FOUND . '</td>
    </tr>
  '; 
}
                
$html .= '
  </tbody>';

if(count($listing_numeric_fields)>0)
{
  require(component_path('items/calculate_fields_totals'));
}

$html .= '  
</table>
</div>
';


//add pager
$html .= '
<div class="row">
  <div class="col-md-3 col-sm-12">' . $listing_split->display_count() . '</div>
  <div class="col-md-9 col-sm-12">' . $listing_split->display_links(). '</div>
</div>      
';

echo $html;

