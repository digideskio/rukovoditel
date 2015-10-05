<?php

function app_set_title($title)
{
  global $app_title;
  
  return $app_title . ' | ' . $title;
}

function format_date($d)
{            
  if(strlen($date_format=CFG_APP_DATE_FORMAT)==0)
  {
    $date_format = 'm/d/Y';
  }
  
  if(strlen($d)>0)
  {
    return i18n_date($date_format,$d);
  }
  else
  {
    return '';
  }
}

function format_date_time($d)
{
  if(strlen($date_format=CFG_APP_DATETIME_FORMAT)==0)
  {
    $date_format = 'm/d/Y H:i';
  }
  
  if(strlen($d)>0)
  {
    return i18n_date($date_format,$d);
  }
  else
  {
    return '';
  }
}

function get_date_timestamp($date)
{
  if(strlen($date)>0)
  {
    $v = date_parse($date);
   
    return mktime($v['hour'], $v['minute'], $v['second'], $v['month'], $v['day'], $v['year']);
  }
  else
  {
    return '';
  }    
}

function day_diff($start, $end, $exclude = array())
{
  $count = 0;

  while(date('Y-m-d', $start) < date('Y-m-d', $end))
  {    
    if(!in_array(date('N', $start),$exclude)) $count++;
    
    $start = strtotime("+1 day", $start);
  }
  
  return $count;
}

function render_bool_value($v)
{
  return ($v==1 ? TEXT_YES : TEXT_NO);
}

function render_listing_search_form($entity_id,$listing_container)
{
  if(count(fields::get_search_feidls($entity_id))==0)
  {
    return '';
  }
  
  $html = '
  <form id="' . $listing_container . '_search_form" class="navbar-search pull-right" onSubmit="load_items_listing(\'' . $listing_container . '\',1); return false;">
    
    <div class="input-group input-medium">
  		<input id="' . $listing_container. '_search_keywords" type="text" placeholder="' . TEXT_SEARCH . '" class="form-control input-medium">
  		<span class="input-group-btn">  			
        <a href="#" ' .  tag_attributes_to_html(array('title'=>TEXT_BUTTON_SEARCH))  . ' class="btn btn-info" onClick="$(\'#' . $listing_container . '_search_form\').submit()"><i class="fa fa-search"></i></a>
  		</span>
  	</div>
  </form>';
  
  return $html;
}

function tooltip_text($text)
{
  if(strlen($text)>0)
  {
    return '<span class="help-block">' . $text . '</span>';
  }
  else
  {
    return '';
  }
} 

function tooltip_icon($text)
{
  if(strlen($text)>0)
  {
    return '<label><a class="tooltips" title="' . htmlspecialchars($text) . '"><i  class="icon-question-sign"></i></a></label>';
  }
  else
  {
    return '';
  }
}  

function app_error_handler($errno, $errmsg, $filename, $linenum,  $vars) 
{
 $time=date("d M Y H:i:s"); 
 
 // Get the error type from the error number 
 $errortype = array (1    => "Error",
                     2    => "Warning",
                     4    => "Parsing Error",
                     8    => "Notice",
                     16   => "Core Error",
                     32   => "Core Warning",
                     64   => "Compile Error",
                     128  => "Compile Warning",
                     256  => "User Error",
                     512  => "User Warning",
                     1024 => "User Notice",
                     2048 => "Error Strict",
                     8192 => "Deprecated");
   
  
  if(isset($errortype[$errno]))
  {                   
    $errlevel=$errortype[$errno];
  }
  else
  {
    $errlevel=$errortype[2];
  }
     
  $errfile=fopen("log/Errors_" . date("M_Y"). ".txt","a+"); 
  fputs($errfile,$time . " " . $errlevel . "\n" . $errmsg . "\n"  . $filename . ':' . $linenum . "\n\n"); 
  fclose($errfile);
  
}

function print_rp($v)
{
  return '<table><tr><td style="white-space:pre-wrap; ">' . print_r($v,true) . '</td></tr></table>';    
}
  

function is_image($path)
{
  if(is_file($path))
  {
    $a = getimagesize($path);
    $image_type = $a[2];
     
    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG )))
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
    return false;
  }
}

function is_pdf($filename)
{  
  return substr($filename,-4)=='.pdf';
}

function is_excel($filename)
{  
  
  return (substr($filename,-4)=='.xls' || substr($filename,-5)=='.xlsx');
}

function image_resize($filename, $filename_small, $resize_image_widht = 150, $resize_image_height = '')
{                         
  if(file_exists($filename))
  {
    $image = getimagesize($filename);
    
    switch($image[2])
    {
			 case 1: $src_img = imagecreatefromgif($filename);
        break;       
			 case 2: $src_img = imagecreatefromjpeg($filename);
			  break;			 
			 case 3: $src_img = imagecreatefrompng($filename);
			  break;
		}
					
    $width = $image[0];
    $height = $image[1];
            
    if($resize_image_widht>0 && $resize_image_height == '' and $width>$resize_image_widht)
    {
     $cof =  $width/$resize_image_widht;
     $width_small=$resize_image_widht;
     $height_small = $height/$cof;
    }
    elseif($resize_image_height>0 && $resize_image_widht == '' and $height>$resize_image_height)
    {
      $height_small = $resize_image_widht;
      $cof =  $height/$resize_image_height;
      $width_small=$width/$cof;
		}
    else
    {
      $height_small  = $height;
      $width_small  = $width;
		}
						
    $tmp_img_small = imagecreatetruecolor($width_small,$height_small);
    ImageCopyResampled($tmp_img_small, $src_img, 0, 0, 0, 0, $width_small, $height_small, $width, $height);
          
    @touch($filename_small); @chmod($filename_small,0777);
      
    switch($image[2])
    {
			 case 1: imagegif($tmp_img_small, $filename_small);
        break;       
			 case 2: imagejpeg($tmp_img_small, $filename_small,100);
			  break;			 
			 case 3: imagepng($tmp_img_small, $filename_small,9);
			  break;
		}                 		    			   
  }
} 

function ajax_modal_template($title, $content)
{
  $html = '  		
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">' . $title . '</h4>
			</div>
			<div >
				 ' .  $content . '
			</div>				
  ';
  
  return $html;
}

function ajax_modal_template_header($title)
{
  $html = '  		
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">' . $title . '</h4>
			</div>							
  ';
  
  return $html;
}

function ajax_modal_template_footer($action_button_tille = false,$extra_buttons = '')
{
  $html = '              
  <div class="modal-footer">
    <div id="form-error-container"></div>
    ' . $extra_buttons . '
    ' . ($action_button_tille!='hide-save-button' ? '<button type="submit" class="btn btn-primary">' .  ($action_button_tille ? $action_button_tille:TEXT_BUTTON_SAVE) . '</button>':'') . '
    <button type="button" class="btn btn-default" data-dismiss="modal">' .  TEXT_BUTTON_CLOSE . '</button>    
  </div>';
      
  $html .= '
  <script>
    jQuery(document).ready(function() {                  
       appHandleUniform()                     
    });
  </script>';
  
  return $html;  
}

function ajax_modal_template_footer_simple()
{
  $html = '
  <div class="modal-footer">    
    <button type="button" class="btn btn-default" data-dismiss="modal">' .  TEXT_BUTTON_CLOSE . '</button>
  </div>';
      
  $html .= '
  <script>
    jQuery(document).ready(function() {                  
       appHandleUniform()                     
    });
  </script>';
  
  return $html;  
}

function render_bg_color_block($color,$value=false)
{
   if(strlen($color)>0)
   {
    if(!$value)
    {
      $value = $color;
    }
    
    $rgb = convert_html_color_to_RGB($color);
        
    if(($rgb[0]+$rgb[1]+$rgb[2])<480)
    {
      return '<div class="bg-color-value" style="background: ' . $color . '; color: white;">' . $value . '</div>';
    }
    else
    {
      return '<div class="bg-color-value" style="background: #' . $color . ';">' . $value . '</div>';
    }        
   }
   else
   {
     return '';
   }
}

function convert_html_color_to_RGB($color)
{
    if ($color[0] == '#') $color = substr($color, 1);

    if (strlen($color) == 6)
    {
      list($r, $g, $b) = array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
    }
    elseif (strlen($color) == 3)
    {
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    }
    else
    {
        return array();
    }

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
}

function app_get_backup_filename($file)
{ 
  if(is_numeric(str_replace('.sql','',$file)))
  {
    return date('Y-m-d_h:i:s',str_replace('.sql','',$file)) . '.sql';
  }
  else
  {
    return $file;
  }
}

function app_db_restore_fp_read_str($fp)
{
  global $file_cache;
  
	$string = '';
	$file_cache = ltrim($file_cache);
	$pos = strpos($file_cache, "\n", 0);
	if ($pos < 1) 
  {
		while (!$string && ($str = fread($fp,4096)))
    {
			$pos = strpos($str, "\n", 0);
			if ($pos === false) 
      {
			    $file_cache .= $str;
			}
			else
      {
				$string = $file_cache . substr($str, 0, $pos);
				$file_cache = substr($str, $pos + 1);
			}
  	}
  	
		if (!$str) 
    {
	    if ($file_cache) 
      {
				$string = $file_cache;
				$file_cache = '';
			  
        return trim($string);
		  }
		  
	    return false;
		}
	}
	else 
  {
		$string = substr($file_cache, 0, $pos);
		$file_cache = substr($file_cache, $pos + 1);
	}
	
	return trim($string);
}

function app_create_db_backup()
{
  set_time_limit(0);
  
  
  $tables_list = array();
  
  $tables_query = db_query("show tables");
  while($tables = db_fetch_array($tables_query))
  {
    $tables_list[] = current($tables);    
  }  
          
  $fp = fopen(DIR_FS_BACKUPS . time(). '.sql', 'w+'); 
    
  foreach($tables_list as $table)
  {      
    fwrite($fp, "DROP TABLE IF EXISTS " . $table . ";\n");
  }
  
  fwrite($fp, "\n\n");
        
  foreach($tables_list as $table)
  {          
    $show_query = db_query('SHOW CREATE TABLE ' . $table);
    $show = db_fetch_array($show_query);
            
    fwrite($fp, $show['Create Table'] . ";\n\n");
                    
    $count_query = db_query('SELECT COUNT(*) as total FROM  ' . $table);
    $count = db_fetch_array($count_query);
    
    if($count['total']>0)
    { 
      $columns_null = array();
            
      $columns_query = db_query('SHOW COLUMNS FROM  ' . $table);
      while($columns = db_fetch_array($columns_query))
      {                       
        if($columns['Null']=='YES')
        {
          $columns_null[] =  $columns['Field'];
        }
      }                
        
      fwrite($fp, "INSERT INTO " . $table . " VALUES");
      
      $limit = 1000;
      $from = 0;
      $i=0;
      
      do
      {                                    
        $items_query = db_query('SELECT * FROM  ' . $table . ' LIMIT ' . $from . ', ' . $limit); 
                      
        while($items = db_fetch_array($items_query))
        {          
          $i++;
                                      
          foreach($items as $k=>$v)
          {
            if(strlen($v)==0 and in_array($k,$columns_null))
            {
              $items[$k] = "NULL";
            }
            else
            {              
              $items[$k] =  "'" . db_input($v) . "'";
            } 
          }
                       
          fwrite($fp,($i > 1 ? "," : "") . "\n(" . implode(",", $items) . ")");              
        }
             
      
        $from+=$limit;
      }
      while($from<($count['total']+$limit));
      
      fwrite($fp, ";\n\n");
    }
           
  }  
}

function render_user_photo($filename)
{
  if(is_file(DIR_WS_USERS . $filename))
  {
     $photo = image_tag(url_for_file(DIR_WS_USERS . $filename),array('class'=>'user-photo-content','width'=>50));
  }                                                                                    
  else
  {
    $photo = image_tag(url_for_file('images/' . 'no_photo.png'),array('class'=>'user-photo-content'));
  }
  
  return $photo;
}

function app_get_languages_choices()
{
  $list = array();
            
  $dir = 'includes/languages/';
  
  if ($handle = opendir($dir)) 
  {             
    while (false !== ($file = readdir($handle))) 
    {
      if ($file != "." && $file != ".." && is_file($dir . $file) and substr($file,-4)=='.php') 
      {    
        $name = ucfirst(substr($file,0,-4));                       
        $list[$file] = $name;
      }  
    }  
  }
  
  return $list;
}

function app_get_skins_choices($add_empty=true)
{
  $list = array();
  
  if($add_empty)
  {
    $list['']='';
  }
            
  $dir = 'css/skins/';
  
  if ($handle = opendir($dir)) 
  {             
    while (false !== ($file = readdir($handle))) 
    {
      if ($file != "." && $file != ".." && is_dir($dir . $file)) 
      {                                   
        $list[$file] = ucfirst($file);
      }  
    }  
  }
  
  return $list;
}

function app_send_new_comment_notification($comments_id,$item_id,$entity_id)
{
  global $app_user;
    
  $fields = array();
  $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_grouped_users','fieldtype_users') and  f.entities_id='" . db_input($entity_id) . "' ");
  while($field = db_fetch_array($fields_query))
  {
    $fields[] = $field['id'];
  }
  
  if(count($fields)>0)
  {         
    $item = db_find('app_entity_' . $entity_id,$item_id);
             
    $send_to = array();
    foreach($fields as $field_id)
    {
      $field_value = $item['field_' . $field_id];
      
      if($pos = strpos($field_value,'|'))
      {
        $field_value = substr($field_value,$pos+1);
      }
                  
      if(strlen($field_value)>0)
      {
        $send_to = array_merge($send_to,explode(',',$field_value));
      }
    } 
    
    if(CFG_EMAIL_COPY_SENDER==1)
    {
      $send_to[] = $app_user['id'];
    }
          
    if(count($send_to)>0)
    {
      $heading_field_id = fields::get_heading_id($entity_id);
      $item_name = ($heading_field_id>0 ? $item['field_' . $heading_field_id] : $item['id']);
      
      $cfg = entities::get_cfg($entity_id);
      
      $path_info = items::get_path_info($entity_id,$item_id);
                 
      $subject = (strlen($cfg['email_subject_new_comment'])>0 ? $cfg['email_subject_new_comment'] . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_NEW_COMMENT . ' ' . $item_name);
      $heading = users::use_email_pattern_style('<div><a href="' . url_for('items/info','path=' . $path_info['full_path'],true) . '"><h3>' . $subject . '</h3></a></div>','email_heading_content');
                    
      foreach(array_unique($send_to) as $user_id)
      {      
        $body = users::use_email_pattern('single',array('email_body_content'=>comments::render_content_box($entity_id,$item_id,$user_id),'email_sidebar_content'=>items::render_info_box($entity_id,$item_id,$user_id)));
        users::send_to(array($user_id),$subject,$heading . $body);                                                                       
      }    
    }
             
  }      
}

function app_reset_selected_items()
{
  global $app_selected_items;
  
  $app_selected_items = array();
}


function i18n_date() {

  $days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $daysshort = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
  $daysmin = array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su");
  $month = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $monthshort = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
  
  $translate = array();
  
  $days_i18n = explode(',',str_replace('"','',TEXT_DATEPICKER_DAYS));
  foreach($days as $k=>$v)
  {
   $translate[$v] = $days_i18n[$k];
  }  
  
  $daysshort_i18n = explode(',',str_replace('"','',TEXT_DATEPICKER_DAYSSHORT));
  foreach($daysshort as $k=>$v)
  {
   $translate[$v] = $daysshort_i18n[$k];
  }
  
  $daysmin_i18n = explode(',',str_replace('"','',TEXT_DATEPICKER_DAYSMIN));
  foreach($daysmin as $k=>$v)
  {
   $translate[$v] = $daysmin_i18n[$k];
  } 
  
  $month_i18n = explode(',',str_replace('"','',TEXT_DATEPICKER_MONTHS));
  foreach($month as $k=>$v)
  {
   $translate[$v] = $month_i18n[$k];
  }
  
  $monthshort_i18n = explode(',',str_replace('"','',TEXT_DATEPICKER_MONTHSSHORT));
  foreach($monthshort as $k=>$v)
  {
   $translate[$v] = $monthshort_i18n[$k];
  }      
                           
  if (func_num_args() > 1) {
      $timestamp = func_get_arg(1);
      return strtr(date(func_get_arg(0), $timestamp), $translate);
  } else {
      return strtr(date(func_get_arg(0)), $translate);
  }
}

function i18n_js()
{
  $list = array();
  $list['TEXT_NO_RESULTS_MATCH'] = TEXT_NO_RESULTS_MATCH;
  $list['TEXT_ARE_YOU_SURE'] = TEXT_ARE_YOU_SURE;
  $list['TEXT_SELECT_AN_OPTION'] = TEXT_SELECT_AN_OPTION;
  $list['TEXT_SELECT_SOME_OPTIONS'] = TEXT_SELECT_SOME_OPTIONS;
  
  $html = '
    <script>
     var i18n = new Array()
  '; 
  
  foreach($list as $k=>$v)
  {
    $html .= 'i18n[\'' . $k . '\']="' . addslashes($v). '"' . "\n";
  }
  
  $html .= '</script>';
  
  return $html;
}

function app_get_path_to_parent_item($path_array)
{  
  unset($path_array[count($path_array)-1]);
  
  return implode('/',$path_array);
}



