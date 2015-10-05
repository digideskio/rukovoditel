<?php

switch($app_module_action)
{
  case 'save':
      foreach($_POST['cfg'] as $k=>$v)
      {
                
        $cfq_query = db_query("select * from app_entities_configuration where configuration_name='" . db_input($k) . "' and entities_id='" . db_input($_GET['entities_id']) . "'");
        if(!$cfq = db_fetch_array($cfq_query))
        {
          db_perform('app_entities_configuration',array('configuration_value'=>$v,'configuration_name'=>$k,'entities_id'=>$_GET['entities_id']));
        }
        else
        {
          db_perform('app_entities_configuration',array('configuration_value'=>$v),'update',"configuration_name='" . db_input($k) . "' and entities_id='" . db_input($_GET['entities_id']) . "'");
        }
      }
      
      $alerts->add(TEXT_CONFIGURATION_UPDATED,'success');
                      
      redirect_to('entities/entities_configuration','entities_id=' . $_GET['entities_id']);
      
    break;
}

require(component_path('entities/check_entities_id'));

$cfg = entities::get_cfg($_GET['entities_id']);

