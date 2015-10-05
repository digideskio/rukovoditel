<?php echo ajax_modal_template_header(TEXT_HEADING_EXPORT) ?>

<?php echo form_tag('export', url_for('items/export','action=export&path=' . $_GET['path'])) . input_hidden_tag('reports_id',$_GET['reports_id']) ?>

<?php
if(!isset($app_selected_items[$_GET['reports_id']])) $app_selected_items[$_GET['reports_id']] = array();

if(count($app_selected_items[$_GET['reports_id']])==0)
{
  echo '
    <div class="modal-body">    
      <div>' . TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
}
else
{
?>

<div class="modal-body">    
<div><?php echo TEXT_SELECT_FIELD_TO_EXPORT ?></div>
<p>
<?php

$fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);

$tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
while($tabs = db_fetch_array($tabs_query))
{
  $fileds_html = '';
  
  $fields_query = db_query("select f.* from app_fields f where  f.type not in ('fieldtype_action') and f.entities_id='" . db_input($current_entity_id) . "' and forms_tabs_id='" . db_input($tabs['id']) . "' order by f.sort_order, f.name");
  while($v = db_fetch_array($fields_query))
  {      
    //check field access
    if(isset($fields_access_schema[$v['id']]))
    {
      if($fields_access_schema[$v['id']]=='hide') continue;
    }
    
    if(in_array($v['type'],array('fieldtype_attachments','fieldtype_textarea','fieldtype_textarea_wysiwyg','fieldtype_input_file','fieldtype_attachments')))
    {
      $checked = '';
    }
    else
    {
      $checked = 'checked';
    }
        
    $fileds_html .= '<div><label>' . input_checkbox_tag('fields[]',$v['id'],array('id'=>'fields_' . $v['id'],'class'=>'fields_tabs_' . $tabs['id'],'checked'=>$checked)) . ' ' . fields_types::get_option($v['type'],'name',$v['name']) . '</label></div>'; 
  }
  
  if(strlen($fileds_html)>0)
  {
    echo '<p><div><label><b>' . input_checkbox_tag('all_tab_fields_' . $tabs['id'],'',array('checked'=>'checked','onChange'=>'select_all_by_classname(\'all_tab_fields_' . $tabs['id'] . '\',\'fields_tabs_' . $tabs['id'] . '\')')) . $tabs['name']. '</b></label></div>' . $fileds_html . '</p>';
  }
} 

?>
</p>


<p><?php 
$current_entity_info = db_find('app_entities',$current_entity_id);

echo TEXT_FILENAME . '<br>' . input_tag('filename',$current_entity_info['name'],array('class'=>'form-control input-medium')) 
?></p>

</div> 
<?php echo ajax_modal_template_footer(TEXT_BUTTON_EXPORT) ?>

<?php } ?>
</form>  