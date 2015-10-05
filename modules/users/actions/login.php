<?php
  $app_layout = 'login_layout.php';
  
  switch($app_module_action)
  {
    case 'logoff':
        app_session_unregister('app_logged_users_id');
        app_session_unregister('app_current_version');
        
        setcookie('app_stay_logged','',time() - 3600,'/');
        setcookie('app_remember_user','',time() - 3600,'/'); 
        setcookie('app_remember_pass','',time() - 3600,'/');
        
        redirect_to('users/login');
      break;
    case 'login':                
        
          
        users::login($_POST['username'],$_POST['password'],(isset($_POST['remember_me']) ? 1 :0));
        
      break;
  }