<?php
  define('PROJECT_VERSION','1.5');
  
//check if installed
  if(!is_file('config/database.php'))
  {
    header('Location: install/index.php');    
    exit();
  }  
  
// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime(true));

// set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);
                        
  require('config/server.php');
  require('config/database.php');
    
  $app_db_query_log = array();
    
//is AJAX request
  define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        
//include classes    
  require('includes/classes/alerts.php');
  require('includes/classes/attachments.php');
  require('includes/classes/cache.php');
  require('includes/classes/fields_types.php');
  require('includes/classes/items.php');
  require('includes/classes/ldap_login.php');
  require('includes/classes/split_page.php');
  require('includes/classes/users.php');
  require('includes/classes/plugins.php');
  require('includes/classes/users_cfg.php');
  require('includes/classes/session.php');
    
//include field types  
  require('includes/classes/fieldstypes/fieldtype_action.php');
  require('includes/classes/fieldstypes/fieldtype_attachments.php');
  require('includes/classes/fieldstypes/fieldtype_checkboxes.php');
  require('includes/classes/fieldstypes/fieldtype_created_by.php');
  require('includes/classes/fieldstypes/fieldtype_date_added.php');
  require('includes/classes/fieldstypes/fieldtype_dropdown.php');
  require('includes/classes/fieldstypes/fieldtype_dropdown_multiple.php');
  require('includes/classes/fieldstypes/fieldtype_progress.php');
  require('includes/classes/fieldstypes/fieldtype_entity.php');
  require('includes/classes/fieldstypes/fieldtype_formula.php');
  require('includes/classes/fieldstypes/fieldtype_grouped_users.php');
  require('includes/classes/fieldstypes/fieldtype_id.php');
  require('includes/classes/fieldstypes/fieldtype_input.php');
  require('includes/classes/fieldstypes/fieldtype_input_date.php');
  require('includes/classes/fieldstypes/fieldtype_input_datetime.php');
  require('includes/classes/fieldstypes/fieldtype_input_file.php');
  require('includes/classes/fieldstypes/fieldtype_input_numeric.php');
  require('includes/classes/fieldstypes/fieldtype_input_numeric_comments.php');
  require('includes/classes/fieldstypes/fieldtype_input_url.php');
  require('includes/classes/fieldstypes/fieldtype_radioboxes.php');
  require('includes/classes/fieldstypes/fieldtype_textarea.php');
  require('includes/classes/fieldstypes/fieldtype_textarea_wysiwyg.php');
  require('includes/classes/fieldstypes/fieldtype_users.php');
  require('includes/classes/fieldstypes/fieldtype_user_accessgroups.php');
  require('includes/classes/fieldstypes/fieldtype_user_email.php');
  require('includes/classes/fieldstypes/fieldtype_user_firstname.php');
  require('includes/classes/fieldstypes/fieldtype_user_language.php');
  require('includes/classes/fieldstypes/fieldtype_user_lastname.php');
  require('includes/classes/fieldstypes/fieldtype_user_photo.php');
  require('includes/classes/fieldstypes/fieldtype_user_skin.php');
  require('includes/classes/fieldstypes/fieldtype_user_status.php');
  require('includes/classes/fieldstypes/fieldtype_user_username.php');
  require('includes/classes/fieldstypes/fieldtype_related_records.php');
  
  
//include models  
  require('includes/classes/model/access_groups.php');
  require('includes/classes/model/comments.php');
  require('includes/classes/model/entities.php');
  require('includes/classes/model/fields.php');
  require('includes/classes/model/fields_choices.php');
  require('includes/classes/model/forms_tabs.php');
  require('includes/classes/model/reports.php');
  
//include functions  
  require('includes/functions/app.php');
  require('includes/functions/database.php');  
  require('includes/functions/html.php');
  require('includes/functions/menu.php');
  require('includes/functions/sessions.php');
  require('includes/functions/urls.php');
  require('includes/functions/validations.php');
  
//include libs  
  require('includes/libs/PasswordHash.php');  
  require('includes/libs/PHPMailer/PHPMailerAutoload.php');
  require('includes/libs/PHPMailer/extras/Html2Text.php');
  
//set custom error handler  
  if(DEV_MODE)
  {
    set_error_handler('app_error_handler');
  }
  
// make a connection to the database...  
  db_connect();
  
// run check before start  
  require('includes/check.php');  
  
  // set the application parameters
  $cfg_query = db_fetch_all('app_configuration');   
  while ($v = db_fetch_array($cfg_query)) 
  {    
    define($v['configuration_name'], $v['configuration_value']);
  }
      
  //configuration added in version 1.4
  if(!defined('CFG_APP_FIRST_DAY_OF_WEEK')) define('CFG_APP_FIRST_DAY_OF_WEEK',0);
  if(!defined('CFG_APP_LOGIN_PAGE_BACKGROUND')) define('CFG_APP_LOGIN_PAGE_BACKGROUND','');
        
  date_default_timezone_set(CFG_APP_TIMEZONE);
          
// set the session name and save path
  app_session_name(SESSION_NAME);
  app_session_save_path(SESSION_WRITE_DIRECTORY);
  
// set the session cookie parameters
   if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, SESSION_COOKIE_PATH, SESSION_COOKIE_DOMAIN);
  } elseif (function_exists('ini_set')) {
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.cookie_path', SESSION_COOKIE_PATH);
    ini_set('session.cookie_domain', SESSION_COOKIE_DOMAIN);
  }

  @ini_set('session.use_only_cookies', (SESSION_FORCE_COOKIE_USE) ? 1 : 0);

// set the session ID if it exists
   if(isset($_GET[app_session_name()]) ) {
     app_session_id($_GET[app_session_name()]);
   }

// start the session
  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE) 
  {
    setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, SESSION_COOKIE_PATH, SESSION_COOKIE_DOMAIN);

    if (isset($_COOKIE['cookie_test'])) {
      app_session_start();
      $session_started = true;
    }
  } 
  else 
  {
    app_session_start();
    $session_started = true;
  }

  if ( ($session_started == true) && function_exists('ini_get') && (ini_get('register_globals') == false) ) {
    extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
  }
  

  if (!app_session_is_registered('uploadify_attachments')) 
  {
    $uploadify_attachments = array();
    app_session_register('uploadify_attachments');    
  }  
  
  
// create the alerts object
  if (!app_session_is_registered('alerts') || !is_object($alerts)) 
  {
    app_session_register('alerts');
    $alerts = new alerts;
  }    
  
  if (!app_session_is_registered('app_send_to')) 
  {
    app_session_register('app_send_to');
    $app_send_to = array();
  } 
        

  if(!isset($_GET['module']))
  {
    redirect_to('dashboard/');
  }
  
//get module info  
  $module_array = explode('/',$_GET['module']);
  if(count($module_array)==2)
  {
    $app_plugin_path = '';
    $app_module = $module_array[0]; 
    $app_action = (strlen($module_array[1])>0 ? $module_array[1]:$module_array[0]);
    $app_module_path = $app_module . '/' . $app_action;     
  }
  elseif(count($module_array)==3)
  {
    $app_plugin_path = 'plugins/' . $module_array[0] . '/'; 
    $app_module = $module_array[1]; 
    $app_action = (strlen($module_array[2])>0 ? $module_array[2]:$module_array[1]);
    $app_module_path = $module_array[0] . '/' . $app_module . '/' . $app_action;
  }
  else
  {
    redirect_to('dashboard/');
  }
    
    
  
//set page title
  $app_title = (strlen(CFG_APP_SHORT_NAME)>0 ? CFG_APP_SHORT_NAME:CFG_APP_NAME);
  
//set module action
  $app_module_action = (isset($_GET['action']) ? $_GET['action'] : '');
  
//set module redirect  
  $app_redirect_to = (isset($_GET['redirect_to']) ? $_GET['redirect_to'] : (isset($_POST['redirect_to']) ? $_POST['redirect_to'] : ''));    
  
//set app rapth  
  $app_path = (isset($_GET['path']) ? $_GET['path'] : (isset($_POST['path']) ? $_POST['path'] : ''));  
  
//set default layout
  $app_layout  = 'layout.php';   

    
//check if user logged
  if (!app_session_is_registered('app_logged_users_id') and !in_array($_GET['module'],array('users/login','users/restore_password','users/ldap_login'))) 
  { 
    //allows redirect user to info page after login     
    if(strstr($_SERVER['QUERY_STRING'],'module=items/info'))
    {
      setcookie('app_login_redirect_to', $_SERVER['QUERY_STRING'], time()+10*60,'/');
    }
            
    if(isset($_COOKIE["app_remember_me"]) and isset($_COOKIE["app_stay_logged"]))
    {      
      users::login(base64_decode($_COOKIE["app_remember_user"]),'',1,base64_decode($_COOKIE["app_remember_pass"]));
    }
    else
    {                          
      redirect_to('users/login');
    }
  }
  elseif(app_session_is_registered('app_logged_users_id'))
  {            
    $user_query = db_query("select * from app_entity_1 where id='" . db_input($app_logged_users_id) . "' and field_5=1");
    if($user = db_fetch_array($user_query))
    {
      if(strlen($user['field_10'])>0)
      {        
        $file = attachments::parse_filename($user['field_10']);
        $photo = $file['file_sha1'];
      }
      else
      {
        $photo = '';
      } 
      
      $app_user = array('id'=>$user['id'],
                        'group_id'=>(int)$user['field_6'],
                        //'name'=>$user['field_7'] . ' ' . $user['field_8'],
                        'name'=>$user['field_8'] . ' ' . $user['field_7'],
                        'username'=>$user['field_12'],
                        'email'=>$user['field_9'],
                        'photo'=>$photo,
                        'language'=>$user['field_13'],
                        'skin'=>$user['field_14'],
                        );                                                      
    }
    else
    {
      app_session_unregister('app_logged_users_id');
      redirect_to('users/login');
    }
  }
  
  //get users configuration
  $app_users_cfg = new users_cfg();
  
  if (!app_session_is_registered('app_current_version')) 
  {
    $app_current_version = '';
    app_session_register('app_current_version');    
  } 
  
  
  if (!app_session_is_registered('app_selected_items')) 
  {
    $app_selected_items = array();
    app_session_register('app_selected_items');    
  } 
    
      
  //include language file   
  if(isset($app_user))
  { 
    if(is_file($v = 'includes/languages/' . $app_user['language'] ))
    {
      require($v);
    }
    elseif(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE ))
    {
      require($v);
    }
  }
  elseif(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE ))
  {    
    require($v);
  } 
  
  //set default language short code if not defined in language file
  if(!defined('APP_LANGUAGE_SHORT_CODE')) define('APP_LANGUAGE_SHORT_CODE','en');  
  
    
  //set skin
  if(strlen(CFG_APP_SKIN)>0)
  {
    $app_skin = CFG_APP_SKIN . '/' . CFG_APP_SKIN .'.css';
  }
  elseif(isset($app_user))
  {
    if(strlen($app_user['skin'])>0)
    {
      $app_skin = $app_user['skin'] . '/' . $app_user['skin'] . '.css';
    }
    else
    {
      $app_skin = 'default/default.css';  
    }
  }
  elseif(isset($_COOKIE['user_skin']))
  {
    $app_skin = $_COOKIE['user_skin'] . '/' . $_COOKIE['user_skin'] . '.css';
  }
  else
  {
    $app_skin = 'default/default.css';
  }
    
  $app_choices_cache = fields_choices::get_cache();  
  $app_users_cache  = users::get_cache(); 

//to include menus from plugins
  $app_plugin_menu = array();
  
//include available plugins  
  if(defined('AVAILABLE_PLUGINS'))
  {
    foreach(explode(',',AVAILABLE_PLUGINS) as $plugin)
    {            
      //include language file   
      if(isset($app_user))
      {         
        if(is_file($v = 'plugins/' . $plugin .'/languages/' . $app_user['language'] ))
        {          
          require($v);
        }
        elseif(is_file($v = 'plugins/' . $plugin .'/languages/' . CFG_APP_LANGUAGE ))
        {
          require($v);
        }
      }
      
      //include plugin
      if(is_file('plugins/' . $plugin .'/application_top.php'))
      {
        require('plugins/' . $plugin .'/application_top.php');
      }
      
      //include menu if not ajax query
      if(is_file('plugins/' . $plugin .'/menu.php') and !IS_AJAX)
      {
        require('plugins/' . $plugin .'/menu.php');
      }
    }
  }    
  
         