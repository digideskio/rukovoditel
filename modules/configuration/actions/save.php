<?php

if($_POST['delete_logo'])
{
  if(is_file(DIR_FS_UPLOADS . CFG_APP_LOGO))
  {
    unlink(DIR_FS_UPLOADS . CFG_APP_LOGO);
  }
}

if($_POST['delete_login_page_background'])
{
  if(is_file(DIR_FS_UPLOADS . CFG_APP_LOGIN_PAGE_BACKGROUND))
  {
    unlink(DIR_FS_UPLOADS . CFG_APP_LOGIN_PAGE_BACKGROUND);
  }
}

if(isset($_POST['CFG']))
{
  foreach($_POST['CFG'] as $k=>$v)
  {
    $k = 'CFG_' . $k;
        
    if($k=='CFG_APP_LOGO')
    {                                                                            
      if(strlen($_FILES['APP_LOGO']['name'])>0)
      {                        
        if(is_image($_FILES['APP_LOGO']['tmp_name']))
        {
          move_uploaded_file($_FILES['APP_LOGO']['tmp_name'], DIR_FS_UPLOADS  . $_FILES['APP_LOGO']['name']);
          $v = $_FILES['APP_LOGO']['name'];
        }
      }      
    }
    
    
    if($k=='CFG_APP_LOGIN_PAGE_BACKGROUND')
    {                                                                            
      if(strlen($_FILES['APP_LOGIN_PAGE_BACKGROUND']['name'])>0)
      {                        
        if(is_image($_FILES['APP_LOGIN_PAGE_BACKGROUND']['tmp_name']))
        {
          move_uploaded_file($_FILES['APP_LOGIN_PAGE_BACKGROUND']['tmp_name'], DIR_FS_UPLOADS  . $_FILES['APP_LOGIN_PAGE_BACKGROUND']['name']);
          $v = $_FILES['APP_LOGIN_PAGE_BACKGROUND']['name'];
        }
      }      
    }
            
    $cfq_query = db_query("select * from app_configuration where configuration_name='" . $k . "'");
    if(!$cfq = db_fetch_array($cfq_query))
    {
      db_perform('app_configuration',array('configuration_value'=>trim($v),'configuration_name'=>$k));
    }
    else
    {
      db_perform('app_configuration',array('configuration_value'=>trim($v)),'update',"configuration_name='" . $k . "'");
    }
  }
  
  $alerts->add(TEXT_CONFIGURATION_UPDATED,'success');
  
  
  
  if(isset($_GET['redirect_to']))
  {
    redirect_to($_GET['redirect_to']);
  }
}