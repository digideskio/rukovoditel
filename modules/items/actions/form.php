<?php

//checking access
if(isset($_GET['id']) and !users::has_access('update'))
{            
  echo ajax_modal_template_header(TEXT_WARNING) . '<div class="modal-body">' . TEXT_NO_ACCESS . '</div>' . ajax_modal_template_footer_simple();
  exit();
}
elseif(!users::has_access('create') and !isset($_GET['id']))
{    
  echo ajax_modal_template_header(TEXT_WARNING) . '<div class="modal-body">' . TEXT_NO_ACCESS . '</div>' . ajax_modal_template_footer_simple();
  exit();
}

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_entity_' . $current_entity_id,$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_entity_' . $current_entity_id);
}

$entity_cfg = entities::get_cfg($current_entity_id);