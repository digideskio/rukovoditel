<?php

class plugins
{
  static public function include_menu($key,$menu = array())
  {
    global $app_plugin_menu,$app_user,$app_redirect_to,$app_path;
    
    if(isset($app_plugin_menu[$key]))
    {
      $menu = array_merge($menu,$app_plugin_menu[$key]);
    }
    
    return $menu;  
  }
  
  static public function handle_action($action)
  {
    global $app_module, $app_action, $app_module_path,$app_user,$app_redirect_to,$app_path;
    
    if(defined('AVAILABLE_PLUGINS'))
    {
      foreach(explode(',',AVAILABLE_PLUGINS) as $plugin)
      {                    
        //include plugin
        if(is_file('plugins/' . $plugin .'/handles/' . $action . '.php'))
        {
          require('plugins/' . $plugin .'/handles/' . $action . '.php');
        }
      }
    }  
  }
  
  static public function include_part($part)
  {
    global $app_module, $app_action, $app_module_path,$app_user,$app_redirect_to,$app_path;
    
    if(defined('AVAILABLE_PLUGINS'))
    {
      foreach(explode(',',AVAILABLE_PLUGINS) as $plugin)
      {                            
        //include plugin
        if(is_file('plugins/' . $plugin .'/includes/' . $part . '.php'))
        {          
          require('plugins/' . $plugin .'/includes/' . $part . '.php');
        }
      }
    }  
  }
}  