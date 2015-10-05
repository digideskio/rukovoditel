<?php echo ajax_modal_template_header(TEXT_HEADING_EXPORT) ?>

<?php echo form_tag('export', url_for('items/single_export','action=export&path=' . $_GET['path'])) ?>

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
            
    $fileds_html .= '<div><label>' . input_checkbox_tag('fields[]',$v['id'],array('id'=>'fields_' . $v['id'],'class'=>'fields_tabs_' . $tabs['id'],'checked'=>'checked')) . ' ' . fields_types::get_option($v['type'],'name',$v['name']) . '</label></div>'; 
  }
  
  if(strlen($fileds_html)>0)
  {
    echo '<p><div><label><b>' . input_checkbox_tag('all_tab_fields_' . $tabs['id'],'',array('checked'=>'checked','onChange'=>'select_all_by_classname(\'all_tab_fields_' . $tabs['id'] . '\',\'fields_tabs_' . $tabs['id'] . '\')')) . $tabs['name']. '</b></label></div>' . $fileds_html . '</p>';
  }
} 

  
?>
</p>

  
<?php if(users::has_comments_access('view') and $entity_cfg['use_comments']==1): ?>  
  <p><label><?php echo input_checkbox_tag('export_comments') . ' ' . TEXT_EXPORT_COMMENTS ?></label></p>
<?php endif ?>  

  <p><?php echo TEXT_FILENAME . '<br>' . input_tag('filename',items::get_heading_field($current_entity_id,$current_item_id) ,array('class'=>'form-control input-medium')) ?></p>

</div>
 
<?php echo ajax_modal_template_footer(TEXT_BUTTON_EXPORT) ?>

</form>  