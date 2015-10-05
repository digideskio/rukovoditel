<?php
      
  require('includes/application_top.php');
      
//include overall action for whole module        
  if(is_file($path = $app_plugin_path . 'modules/' . $app_module . '/module_top.php'))
  {
    require($path);
  }
    
//include module action      
  if(is_file($path = $app_plugin_path . 'modules/' . $app_module . '/actions/' . $app_action . '.php'))
  {
    require($path);
  }
  
  if(IS_AJAX)
  {
    if(is_file($path = $app_plugin_path . 'modules/' . $app_module . '/views/' . $app_action . '.php'))
    {    
      require($path);
    }
  }
  else
  {
    require('template/' . $app_layout);
  }
        
  require('includes/application_bottom.php');