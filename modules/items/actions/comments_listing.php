<?php if(!users::has_comments_access('view')) exit() ?>

<?php

$listing_sql_query = '';

if(strlen($_POST['search_keywords'])>0)
{
  echo '<div class="alert alert-info">' . sprintf(TEXT_SEARCH_RESULT_FOR,htmlspecialchars($_POST['search_keywords'])) . ' <span onClick="reset_search()" class="reset_search">' . TEXT_RESET_SEARCH . '</span></div>';
  
  require(component_path('items/add_search_comments_query'));
}

?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>  
  <tr>
    <?php if(users::has_comments_access('update') and users::has_comments_access('delete')) echo '<th>' . TEXT_ACTION . '</th>' ?>    
    <th width="100%"><?php echo TEXT_COMMENTS ?></th>
    <th><?php echo TEXT_DATE_ADDED ?></th>
  </tr>
</thead>
<tbody>  
<?php 

$fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
$choices_cache = fields_choices::get_cache();


$html = '';
$listing_sql = "select * from app_comments where entities_id='" . db_input($current_entity_id) . "' and items_id='" . db_input($current_item_id) . "' " . $listing_sql_query . " order by date_added desc";
$listing_split = new split_page($listing_sql,'items_comments_listing');
$items_query = db_query($listing_split->sql_query);
while($item = db_fetch_array($items_query))
{
  $html_action_column = '';
  if(users::has_comments_access('update') and users::has_comments_access('delete'))
  {
    $html_action_column = '
      <td class="nowrap">
      ' . (users::has_comments_access('update') ? button_icon_delete(url_for('items/comments_delete','id=' .$item['id'] . '&path=' . $_POST['path'])):'') . '
      ' . (users::has_comments_access('delete') ? button_icon_edit(url_for('items/comments_form','id=' .$item['id'] . '&path=' . $_POST['path'])):'') . '
      </td>
    ';
  }
  
  $html_fields = '';
  $comments_fields_query = db_query("select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input($item['id']) . "' and f.id=ch.fields_id order by ch.id");
  while($field = db_fetch_array($comments_fields_query))
  {
    //check field access
    if(isset($fields_access_schema[$field['id']]))
    {
      if($fields_access_schema[$field['id']]=='hide') continue;
    }
        
    $output_options = array('class'=>$field['type'],
                            'value'=>$field['fields_value'],
                            'field'=>$field, 
                            'path'=>$_POST['path'],                           
                            'choices_cache'=>$choices_cache);
          
    $html_fields .='                      
        <tr><th>&bull;&nbsp;' . $field['name'] . ':&nbsp;</th><td>' . fields_types::output($output_options). '</td></tr>           
    ';
  }
  
  if(strlen($html_fields)>0)
  {
    $html_fields = '<table class="comments-history">' . $html_fields . '</table>';
  }
  
  $attachments = fields_types::output(array('class'=>'fieldtype_attachments','value'=>$item['attachments'],'path'=>$_POST['path'],'field'=>array('entities_id'=>$current_entity_id),'item'=>array('id'=>$current_item_id)));

   $html .= '
    <tr>
      ' . $html_action_column . '    
      <td style="white-space: normal;">' . auto_link_text($item['description']) . $attachments . $html_fields .  '</td>
      <td class="nowrap">' . format_date_time($item['date_added']) . '<br><span ' . users::render_publi_profile($app_users_cache[$item['created_by']],true). '>' . $app_users_cache[$item['created_by']]['name']. '</span><br>' . render_user_photo($app_users_cache[$item['created_by']]['photo']). '</td>
    </tr>
  '; 
}

if($listing_split->number_of_rows==0)
{
  $html .= '
    <tr>
      <td colspan="3">' . TEXT_NO_RECORDS_FOUND . '</td>
    </tr>
  '; 
}

$html .= '
  </tbody>
</table>
</div>
';

//add pager
$html .= '
  <table width="100%">
    <tr>
      <td>' . $listing_split->display_count() . '</td>
      <td align="right">' . $listing_split->display_links(). '</td>
    </tr>
  </table>
';

echo $html;  

exit();
