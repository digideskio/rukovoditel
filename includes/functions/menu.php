<?php

  function build_user_menu()
  {
    global $app_user;
    
    $menu = array();
    
    $menu = array();
    $menu[] = array('title'=>TEXT_MY_ACCOUNT,'url'=>url_for('users/account'),'class'=>'fa-user');
    
    
    if(count($plugin_menu = plugins::include_menu('account_menu'))>0)
    {
      $menu = array_merge($menu,$plugin_menu);
    }
    
    if(strlen(CFG_APP_SKIN)==0)
    {    
      $menu[] = array('title'=>TEXT_CHANGE_SKIN,'url'=>url_for('users/change_skin'),'modalbox'=>true,'class'=>'fa-picture-o');
    }
    
    $menu[] = array('title'=>TEXT_CHANGE_PASSWORD,'url'=>url_for('users/change_password'),'class'=>'fa-unlock-alt');
    $menu[] = array('title'=>TEXT_LOGOFF,'url'=>url_for('users/login&action=logoff'),'is_hr'=>true,'class'=>'fa-sign-out');
            
    return $menu;
  }
  
  function build_entities_menu($menu)
  {
    global $app_user;
    
    if($app_user['group_id']==0)
    {
      $entities_query = db_query("select * from app_entities e where e.parent_id = 0 or display_in_menu=1 order by e.sort_order, e.name");
    }
    else
    {      
      $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and (e.parent_id = 0 or display_in_menu=1) order by e.sort_order, e.name");
    }
    
    while($entities = db_fetch_array($entities_query))
    {
      if($entities['parent_id']==0)
      {
        $s = array();
        $s[] = array('title'=>TEXT_VIEW_ALL,'url'=>url_for('items/items','path=' . $entities['id']));
        
        if(users::has_access('create',users::get_entities_access_schema($entities['id'],$app_user['group_id'])))
        {
          $s[] = array('title'=>TEXT_ADD,'url'=>url_for('items/form','path=' . $entities['id']),'modalbox'=>true);
        }
              
        $entity_cfg = entities::get_cfg($entities['id']);
        $menu_title = (strlen($entity_cfg['menu_title'])>0 ? $entity_cfg['menu_title'] : $entities['name']);                
        $menu_icon = (strlen($entity_cfg['menu_icon'])>0 ? $entity_cfg['menu_icon'] : ($entities['id']==1 ? 'fa-user':'fa-reorder'));        
               
        $menu[] = array('title'=>$menu_title,'url'=>url_for('items/items','path=' . $entities['id']), 'submenu'=>$s,'class'=>$menu_icon);
      }
      else
      {
        $reports_info = reports::create_default_entity_report($entities['id'], 'entity_menu');
        
        //check if parent reports was not set
        if($reports_info['parent_id']==0)
        {
          reports::auto_create_parent_reports($reports_info['id']);
        }
        
        $s = array();
        $s[] = array('title'=>TEXT_VIEW_ALL,'url'=>url_for('reports/view','reports_id=' . $reports_info['id']));
        
        if(users::has_access('create',users::get_entities_access_schema($entities['id'],$app_user['group_id'])))
        {
          $s[] = array('title'=>TEXT_ADD,'url'=>url_for('reports/prepare_add_item','reports_id=' . $reports_info['id']),'modalbox'=>true);
        }
              
        $entity_cfg = entities::get_cfg($entities['id']);
        $menu_title = (strlen($entity_cfg['menu_title'])>0 ? $entity_cfg['menu_title'] : $entities['name']);
        $menu_icon = (strlen($entity_cfg['menu_icon'])>0 ? $entity_cfg['menu_icon'] : 'fa-reorder');
               
        $menu[] = array('title'=>$menu_title,'url'=>url_for('reports/view','reports_id=' . $reports_info['id']), 'submenu'=>$s,'class'=>$menu_icon);
      }
    }
    
    return $menu;
  }
  
  function build_reports_menu($menu)
  {
    global $app_logged_users_id;
    
    if(users::has_reports_access())
    {
      $reports_query = db_query("select * from app_reports where created_by='" . db_input($app_logged_users_id) . "' and in_menu=1 and reports_type in ('standard') order by name");
      while($v = db_fetch_array($reports_query))
      {
        $menu[] = array('title'=>$v['name'],'url'=>url_for('reports/view','reports_id=' . $v['id']),'class'=>'fa-list-alt');
      }
      
      $ss = array();
      
      $reports_query = db_query("select * from app_reports where created_by='" . db_input($app_logged_users_id) . "' order by name");
      while($v = db_fetch_array($reports_query))
      {
        $ss[] = array('title'=>$v['name'],'url'=>url_for('reports/view','reports_id=' . $v['id']));
      }
      
      
                        
      $s = array();
      $s[] = array('title'=>TEXT_STANDARD_REPORTS,'url'=>url_for('reports/reports'));
      
      if(count($plugin_menu = plugins::include_menu('reports'))>0)
      {
        $s = array_merge($s,$plugin_menu);
      }
      
      $menu[] = array('title'=>TEXT_REPORTS,'url'=>url_for('reports/reports'), 'submenu'=>$s,'class'=>'fa-bar-chart-o');
    }  
  
    return $menu;
  }
  
  function build_main_menu()
  {
    global $app_user;
    
    $menu = array();
    
    $menu[] = array('title'=>TEXT_MENU_DASHBOARD,'url'=>url_for('dashboard/'),'class'=>'fa-home');
            
    $menu = build_entities_menu($menu);
    
    $menu = build_reports_menu($menu);
    
    if(count($plugin_menu = plugins::include_menu('menu'))>0)
    {
      $menu = array_merge($menu,$plugin_menu);
    }
                
    //only administrators have access to configurations
    if($app_user['group_id']==0)
    {
      //menu Configuration
      
      
      $s = array();
      $s[] = array('title'=>TEXT_MENU_APPLICATION,'url'=>url_for('configuration/application'));
      $s[] = array('title'=>TEXT_MENU_USERS_ACCESS_GROUPS,'url'=>url_for('configuration/users_groups'));
      $s[] = array('title'=>TEXT_MENU_EMAIL_OPTIONS,'url'=>url_for('configuration/emails'));
      $s[] = array('title'=>TEXT_MENU_LDAP,'url'=>url_for('configuration/ldap'));
      $s[] = array('title'=>TEXT_MENU_LOGIN_PAGE,'url'=>url_for('configuration/login_page'));
      $s[] = array('title'=>TEXT_MENU_USER_REGISTRATION_EMAIL,'url'=>url_for('configuration/registration_email'));
      
      $menu[] = array('title'=>TEXT_MENU_CONFIGURATION,'url'=>url_for('configuration/application'),'submenu'=>$s,'class'=>'fa-gear');
                             
                                   
      $s = array();    
      $s[] = array('title'=>TEXT_MENU_ENTITIES_LIST,'url'=>url_for('entities/'));
      
      foreach(entities::get_tree() as $v)
      {
        $s[] = array('title'=>str_repeat('&nbsp;-&nbsp;', $v['level']) . $v['name'],'url'=>url_for('entities/entities_configuration','entities_id=' . $v['id']));
      }
                  
      $menu[] = array('title'=>TEXT_MENU_APPLICATION_STRUCTURE,'url'=>url_for('entities/'),'submenu'=>$s,'class'=>'fa-sitemap');
      
      
      $s = plugins::include_menu('extension');
      
      if(count($s)>0)
      {
        $menu[] = array('title'=>TEXT_MENU_EXTENSION,'url'=>url_for('ext/ext/'),'submenu'=>$s,'class'=>'fa-puzzle-piece');
      }
      else
      {
        $menu[] = array('title'=>TEXT_MENU_EXTENSION,'url'=>url_for('tools/extension'),'class'=>'fa-puzzle-piece');
      }
          
          
      //Menu Tools
      $s = array();          
      $s[] = array('title'=>TEXT_MENU_IMPORT_DATA,'url'=>url_for('tools/import_data'));
      $s[] = array('title'=>TEXT_MENU_BACKUP,'url'=>url_for('tools/db_backup'));
      $s[] = array('title'=>TEXT_MENU_CHECK_VERSION,'url'=>url_for('tools/check_version'));        
      $s[] = array('title'=>TEXT_MENU_SERVER_INFO,'url'=>url_for('tools/server_info'));            
      $menu[] = array('title'=>TEXT_MENU_TOOLS,'url'=>url_for('tools/db_backup'), 'submenu'=>$s,'class'=>'fa-wrench');
      
      $s = array();
      $s[] = array('title'=>TEXT_MENU_REPORT_FORUM,'url'=>'http://rukovoditel.net/forum.php','target'=>'_balnk');      
      $s[] = array('title'=>TEXT_MENU_CONTACT_US,'url'=>'http://rukovoditel.net/contact_us.php','target'=>'_balnk');
      $s[] = array('title'=>TEXT_MENU_DONATE,'url'=>'http://rukovoditel.net/donate.php','target'=>'_balnk');
      $menu[] = array('title'=>TEXT_MENU_SUPPORT,'url'=>'http://rukovoditel.net/forum.php','submenu'=>$s,'class'=>'fa-envelope-o');
      
    }
    
    return $menu;
  }
  
  function renderSidebarMenu($menu = array(), $html='',$level=0)
  { 
    if($level>0)
    {     
      $html .= '
        <ul class="sub-menu">';
    }
              
    foreach($menu as $v)
    {
        
      if(isset($v['is_hr']))
      {
        if($v['is_hr']==true)
        {
          $html .= '<li class="divider"></li>';
        }
      }
      
      $is_active = isSidebarMenuItemActive(array(),$v['url'],$level);
      
      if(strlen($html)==0)
      {
        $html .= '<li class="start ' .($is_active ? 'active':'') . '">';
      }
      else
      {
        $html .= '<li ' .  ($is_active ? 'class="active"':'') . ' >';
      }
      
      
      $url = '';
      
      if(isset($v['url']))
      {
        if(isset($v['modalbox']))
        {
          $url = 'onClick="open_dialog(\'' . $v['url']. '\')" class="cursor-pointer"';
        }
        else
        {
          $url = 'href="' . $v['url'] . '"';          
        }
      }
      elseif(isset($v['onClick']))
      {
        $url = 'onClick="' . $v['onClick'] . '" class="cursor-pointer"';
      }
      
      if(!isset($v['target'])) $v['target'] = false;
      
      $html .= '
        <a ' . ($v['target'] ? 'target="' . $v['target'] . '"':''). ' ' . $url . '>' . (isset($v['badge']) ? '<span class="badge ' . $v['badge'] . '">' . $v['badge_content'] . '</span> ':''). (isset($v['class']) ? '<i class="fa ' . $v['class'] . '"></i> ':'') . '<span class="title ' . (isset($v['submenu']) ? 'submenu':''). '">' . $v['title'] . '</span>' . (isset($v['submenu']) ? '<span class="arrow ' . ($is_active ? 'open':'') . '"></span>':''). '</a>';
            
        
      if(isset($v['submenu']))
      {
        $html = renderSidebarMenu($v['submenu'],$html,$level+1);
      }

      
      $html .= '
        </li>' . "\n";  
    }  
    
    if($level>0)
    {  
      $html .= '
        </ul>';
    }
    
    return $html;    
  } 
  
  function isSidebarMenuItemActive($menu,$menu_url,$menu_level,$check_level = 0)
  {
    global $sidebarMenu;   
    
    if(count($menu)==0)
    {
      $menu = $sidebarMenu;
    }     
        
    $current_url = (ENABLE_SSL ? 'https://':'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    if(strstr($current_url,'module=entities/') and strstr($current_url,'entities_id='))
    {
      $current_url = preg_replace('/module=(.*)&entities_id=/','module=entities/entities_configuration&entities_id=',$current_url);
      $current_url = preg_replace('/&fields_id=(.*)&/','&',$current_url);
    }
                
    if($menu_url == $current_url)
    {            
      return true;
    }
    else
    {                                      
      foreach($menu as $v)      
      {                                                                                      
        if(isset($v['submenu']))
        { 
                                                                        
          $url_list = array();
          $url_list[] = $v['url'];
          $url_list = getSidebarLevelUrls($v['submenu'],$url_list);
                                        
          if($menu_level==$check_level and in_array($current_url,$url_list) and in_array($menu_url,$url_list))
          {                                  
            return true;
          } 
          
          if(isSidebarMenuItemActive($v['submenu'],$menu_url,$menu_level,$check_level+1))
          {
            return true;
          }                                                 
        }                    
      }
                        
      return false;
    }
  } 
  
  function getSidebarLevelUrls($submenu,$url_list)
  {
    foreach($submenu as $v)      
    {
       $url_list[] = $v['url'];
        
       if(isset($v['submenu']))       
       {          
         $url_list = getSidebarLevelUrls($v['submenu'],$url_list);
       }
    }
    
    return $url_list;
  } 
  
  function hasSidebarLevelUrl($submenu,$url)
  {
    foreach($submenu as $v)      
    {
      if($v['url']==$url)
      {
        return true;
      }
    
      if(isset($v['submenu']))
      {          
        hasSidebarLevelUrl($v['submenu'],$url);
      }
    }
    
    return false;
  }
  
  function renderDropDownMenu($menu = array(), $html='',$level=0)
  { 
    if($level==0)
    {     
      $html .= '
        <ul class="dropdown-menu">';
    }
      
    foreach($menu as $v)
    {
        
      if(isset($v['is_hr']))
      {
        if($v['is_hr']==true)
        {
          $html .= '<li class="divider"></li>';
        }
      }
      
      
      if(isset($v['modalbox']))
      {
        $url = 'onClick="open_dialog(\'' . $v['url']. '\')" class="cursor-pointer"';
      }
      else
      {
        $url = 'href="' . $v['url'] . '"';
      }
      
      $html .= '
        <li><a ' . $url . '><i class="fa ' . $v['class'] . '"></i> ' . $v['title'] . '</a>';
            
        
      if(isset($v['submenu']))
      {
        $html = renderDropDownMenu($v['submenu'],$html,$level+1);
      }

      
      $html .= '
        </li>' . "\n";  
    }  
      
    $html .= '
      </ul>';
    
    return $html;    
  }
  
  function renderNavbarMenu($menu = array(), $html='',$level=0)
  {
    if(strlen($html)==0)
    {
      $html = '<ul class="nav navbar-nav">';
    }
    elseif($level==1)
    {
      $html .= '<ul class="dropdown-menu">';
    }
    
    foreach($menu as $v)
    {
      if(isset($v['modalbox']))
      {
        $url = 'onClick="open_dialog(\'' . $v['url']. '\')" class="cursor-pointer"';
      }
      elseif(isset($v['url']))
      {
        $url = 'href="' . $v['url'] . '"';
      }
      
      if(isset($v['submenu']))
      {    
        $html .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $v['title'] . ' <i class="fa fa-angle-down"></i></a>';        
      }
      else
      {
        $html .= '<li><a ' . $url . '>' . $v['title'] . '</a>';  
      }  
                            
      if(isset($v['submenu']))
      {
        $html = renderNavbarMenu($v['submenu'],$html,$level+1);
      }

      
      $html .= '
        </li>' . "\n";
    }
    
    $html .= '
      </ul>';
      
    return $html;  
  
  }   