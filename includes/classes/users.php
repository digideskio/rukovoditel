<?php

class users
{
  static public function get_cache()
  {
    $public_profile_fields = array();
    
    //get public profile fields
    if(defined('CFG_PUBLIC_USER_PROFILE_FIELDS'))
    if(strlen(CFG_PUBLIC_USER_PROFILE_FIELDS)>0)
    {
      $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list() . "," . fields_types::get_users_types_list(). ") and f.id in (" . CFG_PUBLIC_USER_PROFILE_FIELDS . ") and  f.entities_id='1' and f.forms_tabs_id=t.id order by  field(f.id," . CFG_PUBLIC_USER_PROFILE_FIELDS . ")");
      while($v = db_fetch_array($fields_query))
      {
        $public_profile_fields[$v['id']] = $v['name'];
      }
    }
            
    $cache = array();
    $users_query = db_query("select u.* from app_entity_1 u order by u.field_8, u.field_7");
    while($users = db_fetch_array($users_query))
    {
      $profiel_fields = array();
      
      //generate public profile data
      foreach($public_profile_fields as $id=>$name)
      {
        if(strlen($users['field_' . $id])>0)
        {
          $profiel_fields[$name] = $users['field_' . $id];
        }
      }
      
      if(strlen($users['field_10'])>0)
      {        
        $file = attachments::parse_filename($users['field_10']);
        $photo = $file['file_sha1'];
      }
      else
      {
        $photo = '';
      } 
                  
      $cache[$users['id']] = array('name'=>$users['field_8'] . ' ' . $users['field_7'],
                                  'photo'=>$photo,
                                  'profile'=>$profiel_fields);
    }
        
    return $cache;
  }
  
  static public function render_publi_profile($users_cache,$is_photo_display=false)
  {  
    if(strlen($users_cache['photo']) and is_file(DIR_WS_USERS . $users_cache['photo']))
    {      
      $photo = '<img src=' . url_for_file(DIR_WS_USERS . $users_cache['photo']) . ' width=50>';
    }
    else
    {
      $photo = '<img src=' . url_for_file('images/' . 'no_photo.png') . ' width=50>';
    }
            
    $content = '<table align=center><tr><td valign=top width=60>' . $photo. '</td><td valign=top><table>';
      
    foreach($users_cache['profile'] as $name=>$value)
    {
      $content .= '<tr><td>' . htmlspecialchars(strip_tags($name)) . ': </td><td>' . htmlspecialchars(strip_tags($value)) .'</td></tr>';
    }
    
    $content .= '</table></td></tr></table>';
       
    $display_profile = false;
              
    if(count($users_cache['profile'])>0 and $is_photo_display)
    {
      $display_profile = true;   
    }
    elseif(!$is_photo_display)
    {
      $display_profile = true;
    }
    
    if($display_profile)
    {
      return 'data-toggle="popover" title="' . addslashes(htmlspecialchars($users_cache['name'])) . '" data-content="' . addslashes(str_replace(array("\n","\r","\n\r"),' ',$content)) . '"';
    }
    else
    {
      return '';
    }
  }
  
  
  static public function get_choices($options=array())
  {
    $choices = array();
    $users_query = db_query("select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 order by u.field_8, u.field_7");
    while($users = db_fetch_array($users_query))
    {
      $group_name = (strlen($users['group_name'])>0 ? $users['group_name'] : TEXT_ADMINISTRATOR);
      $choices[$group_name][$users['id']] = $users['field_8'] . ' ' . $users['field_7']; 
    }
    
    return $choices;
  }
  
  static public function use_email_pattern($pattern,$blocks = array())
  {
    $content = file_get_contents('includes/patterns/email/' . $pattern . '.html');
            
    foreach($blocks as $k=>$v)
    {
      $v = users::use_email_pattern_style($v,$k);
      $content = str_replace('[' . $k . ']',$v,$content);
    }
    
    return $content;
  }
  
  static public function use_email_pattern_style($content,$style)
  {
    $content = preg_replace('/data-content="(.*)"/','',$content);
    
    require('includes/patterns/email/styles.php');
    
    foreach($styles[$style] as $tag=>$css)
    {
      $content = str_replace(array('<' . $tag . ' ','<' . $tag . '>'),array('<' . $tag . ' style="' . $css . '" ','<' . $tag . ' style="' . $css . '">'),$content);            
    }
    
    return $content;
  }
  
  static public function send_to($send_to,$subject,$body)
  {
    global $app_user;
    
    foreach($send_to as $users_id)
    {
      if(CFG_EMAIL_COPY_SENDER==0 and $users_id==$app_user['id']) continue;
      
      $users_info_query = db_query("select * from app_entity_1 where id='" . db_input($users_id) . "' and field_5=1");
      if($users_info = db_fetch_array($users_info_query))
      {
        $options = array('to'       =>$users_info['field_9'],
                         'to_name'  =>$users_info['field_8'] . ' ' . $users_info['field_7'],
                         'subject'  =>$subject,
                         'body'     =>$body,
                         'from'     =>$app_user['email'],
                         'from_name'=>$app_user['name']);
                         
        users::send_email($options);
      }
      
    }
  } 
  
  
  static public function send_email($options = array())
  {
    global $alerts;
    
    if(CFG_EMAIL_USE_NOTIFICATION==0)
    {
      return false;
    }
    
    $mail = new PHPMailer;
    
    $mail->CharSet = "UTF-8";
    
    if(CFG_EMAIL_USE_SMTP==1)
    {
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = CFG_EMAIL_SMTP_SERVER;  // Specify main and backup server
      $mail->Port = CFG_EMAIL_SMTP_PORT;
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = CFG_EMAIL_SMTP_LOGIN;                            // SMTP username
      $mail->Password = CFG_EMAIL_SMTP_PASSWORD;                           // SMTP password
      $mail->SMTPSecure = CFG_EMAIL_SMTP_ENCRYPTION;                            // Enable encryption, 'ssl' also accepted
    }
    
    if(CFG_EMAIL_SEND_FROM_SINGLE==1)
    {
      $mail->From = CFG_EMAIL_ADDRESS_FROM;
      $mail->FromName = CFG_EMAIL_NAME_FROM;
    }
    else
    {  
      $mail->From = $options['from'];
      $mail->FromName = $options['from_name'];
    }
                
    $mail->addAddress($options['to'], $options['to_name']);  // Add a recipient
    
    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    if(isset($options['attachments']))
    {  
      foreach($options['attachments'] as $filename=>$name)
      {  
        $mail->addAttachment($filename, $name);
      }
    }
    $mail->isHTML(true);                                  // Set email format to HTML
    
    $mail->Subject = (strlen(CFG_EMAIL_SUBJECT_LABEL)>0 ? CFG_EMAIL_SUBJECT_LABEL . ' ': '') . $options['subject'];
    $mail->Body    = $options['body'];
    
    $h2t = new html2text($options['body']);    
    $mail->AltBody = $h2t->get_text();;
    
    if(!$mail->send()) 
    {                     
       $alerts->add('Message to ' . $options['to'] . ' could not be sent. <br>Mailer Error: ' . $mail->ErrorInfo,'error');
    }
  }
  
  
  static public function get_random_password($length = CFG_PASSWORD_MIN_LENGTH)
  {        
    $chars = "~!@#$%^&*()_+abcdefghijkmnopqrstuvwxyz023456789ABCDEFGHIJKMNOPQRSTUVWXYZ";

    $password = '' ;
    
    for($i=0; $i<$length; $i++)
    {
        $password .= $chars[rand(0,71)];
    }
    
    return $password;
  }
  
  static public function get_fields_access_schema($entities_id, $access_groups_id)
  {
    $access_schema = array();
    $acess_info_query = db_query("select * from app_fields_access where entities_id='" . db_input($entities_id) . "' and access_groups_id='" . db_input($access_groups_id) . "'");
    while($acess_info = db_fetch_array($acess_info_query))
    {
      $access_schema[$acess_info['fields_id']] = $acess_info['access_schema'];
    }
    
    return $access_schema;
  }
  
  static public function get_entities_access_schema($entities_id, $access_groups_id)
  {
    $access_schema = array();
    
    $acess_info_query = db_query("select access_schema from app_entities_access where entities_id='" . db_input($entities_id) . "' and access_groups_id='" . db_input($access_groups_id) . "'");
    if($acess_info = db_fetch_array($acess_info_query))
    {
      $access_schema = explode(',',$acess_info['access_schema']);
    }
    
    return $access_schema;
  }
  
  static public function get_entities_access_schema_by_groups($entities_id)
  {
    $access_schema = array();
    
    $acess_info_query = db_query("select access_schema,access_groups_id  from app_entities_access where entities_id='" . db_input($entities_id) . "'");
    while($acess_info = db_fetch_array($acess_info_query))
    {
      $access_schema[$acess_info['access_groups_id']] = explode(',',$acess_info['access_schema']);
    }
    
    return $access_schema;
  }
  
  static public function has_access($access,$access_schema = null)
  {
    global $current_access_schema,$app_user;
    
    //administrator have full access
    if($app_user['group_id']==0)
    {
      return true;
    }
    
    if(isset($access_schema))
    {
      $schema = $access_schema;
    }
    else
    {
      $schema = $current_access_schema;
    }
    
    return in_array($access,$schema);
  }
  
  static public function has_access_to_assigned_item($entities_id, $items_id)
  {
    global $app_user;
    
    $users_fields = array(); 
    $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_users') and  f.entities_id='" . db_input($entities_id) . "'");
    while($fields = db_fetch_array($fields_query))
    {    
      $users_fields[] = $fields['id'];
    }
    
    $grouped_users_fields = array(); 
    $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_grouped_users') and  f.entities_id='" . db_input($entities_id) . "'");
    while($fields = db_fetch_array($fields_query))
    {    
      $grouped_users_fields[] = $fields['id'];
    }    
    
    if(count($users_fields)>0 or count($grouped_users_fields)>0)
    {
      $sql_query_array = array();
      foreach($users_fields as $id)
      {
        $sql_query_array[] = "find_in_set('" . $app_user['id']. "', e.field_" . $id. ")";
      }
      
      foreach($grouped_users_fields as $id)
      {
        $sql_query_array[] = "find_in_set('" . $app_user['id']. "', SUBSTRING(e.field_" . $id. ",locate('|',e.field_" . $id . ")+1))";
      }
      
      $sql_query_array[] = "e.created_by='" . $app_user['id'] . "'";
      
      $item_query = db_query("select e.* from app_entity_" . $entities_id . " e where id='" . db_input($items_id) . "'and (" . implode(' or ', $sql_query_array). ") ");
      if($item = db_fetch_array($item_query))
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      return true;
    } 
  }
  
  static public function get_comments_access_schema($entities_id, $access_groups_id)
  {
    $access_schema = array();
    
    $acess_info_query = db_query("select access_schema from app_comments_access where entities_id='" . db_input($entities_id) . "' and access_groups_id='" . db_input($access_groups_id). "'");
    if($acess_info = db_fetch_array($acess_info_query))
    {
      $access_schema = explode(',',$acess_info['access_schema']);
    }
    
    return $access_schema;
  }
  
  static public function has_comments_access($access)
  {
    global $current_comments_access_schema, $app_user;
        
    //administrator have full access
    if($app_user['group_id']==0)
    {
      return true;
    }
        
    return in_array($access,$current_comments_access_schema);        
  }
  
  static public function has_reports_access()
  {
    global $app_user;  
    
    //administrator have full access
    if($app_user['group_id']==0)
    {
      return true;
    }
    else
    {
      $acccess_query = db_query("select * from app_entities_access where access_groups_id='" . db_input($app_user['group_id']) . "' and find_in_set('reports',access_schema)");
      if($acccess = db_fetch_array($acccess_query))
      {
        return true;
      }
      else
      {
        return false;
      }
    }
  }
  
  
  static public function login($username, $password, $remember_me,$password_hashed=null,$redirect_to=null)
  {
    global $alerts,$_GET;
    
    $user_query = db_query("select * from app_entity_1 where field_12='" . db_input($username) . "' " . (isset($password_hashed) ? " and password='" . db_input($password_hashed) . "'":""));
    if($user = db_fetch_array($user_query))
    {
       if($user['field_5']==1)
       {
         $hasher = new PasswordHash(11, false);


         if(isset($password_hashed))
         {
           app_session_register('app_logged_users_id',$user['id']);
           
           if(!isset($_GET['action']))
           {
             redirect_to($_GET['module'],get_all_get_url_params());
           }
           else
           {
             redirect_to('dashboard/');
           } 
         }
         elseif($hasher->CheckPassword($password, $user['password']))
         {
                                                             
           app_session_register('app_logged_users_id',$user['id']);
           
            if($remember_me==1) 
            { 
              setcookie('app_remember_me', 1, time()+60*60*24*30,'/');                
              setcookie('app_stay_logged', 1, time()+60*60*24*30,'/');
              setcookie('app_remember_user', base64_encode($user['field_12']), time()+60*60*24*30,'/'); 
              setcookie('app_remember_pass', base64_encode($user['password']), time()+60*60*24*30,'/'); 
            }
            else
            {
              setcookie('app_remember_me','',time() - 3600,'/');                    
              setcookie('app_stay_logged','',time() - 3600,'/');
              setcookie('app_remember_user','',time() - 3600,'/'); 
              setcookie('app_remember_pass','',time() - 3600,'/');
            } 
            
            if(isset($_COOKIE['app_login_redirect_to']))
            {
              setcookie('app_login_redirect_to','',time() - 3600,'/');
              redirect_to(str_replace('module=','',$_COOKIE['app_login_redirect_to']));
            }
            else
            {
              redirect_to('dashboard/'); 
            }
                                                                                                  
           
         }
         else
         {
           $alerts->add(TEXT_USER_NOT_FOUND,'error');
           redirect_to('users/login');
         }
       
       }
       else
       {
         $alerts->add(TEXT_USER_IS_NOT_ACTIVE,'error');
         redirect_to('users/login');
       }
    }
    else
    {
      $alerts->add(TEXT_USER_NOT_FOUND,'error');
      redirect_to('users/login');
    }
  }
}