<?php

class calendar
{
  static public function get_events($date_from, $date_to,$calendar_type)
  {
    global $app_user;
    
    $list = array();
    
    
    $where_sql = " where ( (FROM_UNIXTIME(start_date,'%Y-%m-%d')>='" . $date_from . "' and  FROM_UNIXTIME(end_date,'%Y-%m-%d')<='" . $date_to . "') or 
                           (FROM_UNIXTIME(start_date,'%Y-%m-%d')<'" . $date_from . "' and  FROM_UNIXTIME(end_date,'%Y-%m-%d')>'" . $date_to . "') or
                           (FROM_UNIXTIME(start_date,'%Y-%m-%d')<'" . $date_from . "' and  FROM_UNIXTIME(end_date,'%Y-%m-%d')<='" . $date_to . "' and  FROM_UNIXTIME(end_date,'%Y-%m-%d')>='" . $date_from . "') or
                           (FROM_UNIXTIME(start_date,'%Y-%m-%d')>='" . $date_from . "' and FROM_UNIXTIME(start_date,'%Y-%m-%d')<='" . $date_to . "' and  FROM_UNIXTIME(end_date,'%Y-%m-%d')>'" . $date_to . "') 
                           ) ";
    
    switch($calendar_type)
    {
      case 'personal':
          $where_sql .= " and event_type='personal' and users_id='" . db_input($app_user['id']) . "' "; 
        break;
      case 'public':
          $where_sql .= " and (event_type='public' or (event_type='personal' and is_public=1)) "; 
        break;
    }
                 
    $events_query = db_query("select * from app_ext_calendar_events " . $where_sql . " order by start_date");
    while($events = db_fetch_array($events_query))
    {
      $list[] = $events; 
    }
    
    //check if we get evetns for single day (required for repeat events)
    $is_single_day = ($date_from==$date_to);
                    
    if(count($repeat_events_list = calendar::get_repeat_events($date_to,$calendar_type,$is_single_day)))
    {
      $list = array_merge($list,$repeat_events_list);
    }
            
    return $list;
  }
  
  public static function weeks_dif($start, $end)
  {
    $year_start = date('Y',$start);
    $year_end = date('Y',$end);
    
    $week_start = date('W',$start); 
    $week_end = date('W',$end);
    
    $dif_years = $year_end - $year_start;
    $dif_weeks = $week_end - $week_start;
    
    if($dif_years==0 and $dif_weeks==0)
    {
      return 0;
    }
    elseif($dif_years==0 and $dif_weeks>0)
    {
      return $dif_weeks;
    }
    elseif($dif_years==1)
    {
      return (42-$week_start)+$week_end;
    }
    elseif($dif_years>1)
    {
      return (42-$week_start)+$week_end+(($dif_years-2)*42);
    }
  
    
  }
  
  public static function months_dif($start, $end)
  {
    // Assume YYYY-mm-dd - as is common MYSQL format
    $splitStart = explode('-', date('Y-n',$start));
    $splitEnd = explode('-', date('Y-n',$end));
          
    if (is_array($splitStart) && is_array($splitEnd)) 
    {
        $startYear = $splitStart[0];
        $startMonth = $splitStart[1];
        $endYear = $splitEnd[0];
        $endMonth = $splitEnd[1];
  
        $difYears = $endYear - $startYear;
        $difMonth = $endMonth - $startMonth;
  
        if (0 == $difYears && 0 == $difMonth) 
        { // month and year are same
            return 0;
        }
        else if (0 == $difYears && $difMonth > 0) 
        { // same year, dif months
            return $difMonth;
        }
        else if (1 == $difYears) 
        {
            $startToEnd = 13 - $startMonth; // months remaining in start year(13 to include final month
            return ($startToEnd + $endMonth); // above + end month date
        }
        else if ($difYears > 1) 
        {
            $startToEnd = 13 - $startMonth; // months remaining in start year 
            $yearsRemaing = $difYears - 2;  // minus the years of the start and the end year
            $remainingMonths = 12 * $yearsRemaing; // tally up remaining months
            $totalMonths = $startToEnd + $remainingMonths + $endMonth; // Monthsleft + full years in between + months of last year
            return $totalMonths;
        }
    }
    else 
    {
      return false;
    }
  }
  
  public static function get_repeat_events($date_to,$calendar_type,$is_single_day)
  {
    global $app_user;
    
    //convert date to timestamp
    $date_to_timestamp = get_date_timestamp($date_to);
            
    $list = array();
    
    switch($calendar_type)
    {
      case 'personal':
          $where_sql = " and event_type='personal' and users_id='" . db_input($app_user['id']) . "' "; 
        break;
      case 'public':
          $where_sql = " and (event_type='public' or (event_type='personal' and is_public=1)) "; 
        break;
    }
    
    //get all events that already started (start_date<=date_to)      
    $events_query = db_query("select * from app_ext_calendar_events where length(repeat_type)>0 and FROM_UNIXTIME(start_date,'%Y-%m-%d')<='" . $date_to . "'" . $where_sql);
    while($events = db_fetch_array($events_query))
    {
      $start_date = $events['start_date'];
      
      //set repeat end      
      $repeat_end = false;
      if($events['repeat_end']>0)
      {
        $repeat_end = $events['repeat_end'];
      } 
      
      //get repeat events by type                       
      switch($events['repeat_type'])
      {
        case 'daily': 
            //check repeat events day bay day       
            for($date = $start_date; $date<=$date_to_timestamp; $date+=86400)            
            {
              if($date>$start_date)
              {
                $dif = round(abs($date-$start_date)/86400);
                
                if($dif>0)
                {  
                  $event_obj = $events;                                                
                  $event_obj['start_date'] = strtotime('+' . $dif . ' day',$event_obj['start_date']);                                                          
                  $event_obj['end_date'] = strtotime('+' . $dif . ' day',$event_obj['end_date']);
                                          
                  if(calendar::check_repeat_event_dif($dif,$event_obj,$repeat_end))
                  {                
                    $list[] = $event_obj;                
                  }
                }
              }
            }                                                          
          break;
        case 'weekly':  
            //check repeat events day bay day    
            for($date = $start_date; $date<=$date_to_timestamp; $date+=86400)            
            {
              if($date>$start_date)
              {
                //find days dif
                $dif = round(abs($date-$start_date)/86400);
                //find week dif
                $week_dif = calendar::weeks_dif($start_date, $date);
                
                if($dif>0 and (in_array(date('N',$date),explode(',',$events['repeat_days']))))
                {  
                  $event_obj = $events;                                                
                  $event_obj['start_date'] = strtotime('+' . $dif . ' day',$event_obj['start_date']);                                                          
                  $event_obj['end_date'] = strtotime('+' . $dif . ' day',$event_obj['end_date']);
                                          
                  if(calendar::check_repeat_event_dif($week_dif,$event_obj,$repeat_end))
                  {                
                    $list[] = $event_obj;                
                  }
                }
              }
            }                       
                                                                                  
          break;            
        case 'monthly':                        
            /**
             *in calendar we display 3 month in one view
             *so we have to check difference for each month
             */
                                                   
               //check 1                                                  
              $date_to_timestamp2 = strtotime('-2 month',$date_to_timestamp);
              
              $dif =  calendar::months_dif($start_date, $date_to_timestamp2);
                            
              if($dif>0)
              {  
                $event_obj = $events;                                                
                $event_obj['start_date'] = strtotime('+' . $dif . ' month',$event_obj['start_date']);                                                          
                $event_obj['end_date'] = strtotime('+' . $dif . ' month',$event_obj['end_date']);
                                        
                if(calendar::check_repeat_event_dif($dif,$event_obj,$repeat_end))
                {                
                  $list[] = $event_obj;                
                }
              }  
              
              //check 2
              $date_to_timestamp1 = strtotime('-1 month',$date_to_timestamp);
              
              $dif =  calendar::months_dif($start_date, $date_to_timestamp1);
                            
              if($dif>0)
              {  
                $event_obj = $events;                                                
                $event_obj['start_date'] = strtotime('+' . $dif . ' month',$event_obj['start_date']);                                                          
                $event_obj['end_date'] = strtotime('+' . $dif . ' month',$event_obj['end_date']);
                                        
                if(calendar::check_repeat_event_dif($dif,$event_obj,$repeat_end))
                {                
                  $list[] = $event_obj;                
                }
              }
         
            
            //check 3
            $dif =  calendar::months_dif($start_date, $date_to_timestamp);
                          
            if($dif>0)
            {     
              $event_obj = $events;                                             
              $event_obj['start_date'] = strtotime('+' . $dif . ' month',$event_obj['start_date']);                                                          
              $event_obj['end_date'] = strtotime('+' . $dif . ' month',$event_obj['end_date']);
                                      
              if(calendar::check_repeat_event_dif($dif,$event_obj,$repeat_end))
              {                                
                $list[] = $event_obj;
                               
              }
            }
                        
          break;
        case 'yearly':                        
            $dif =  date('Y',$date_to_timestamp)-date('Y',$start_date);
            
            if($dif>0)
            {             
              $events['start_date'] = strtotime('+' . $dif . ' year',$events['start_date']);                                                          
              $events['end_date'] = strtotime('+' . $dif . ' year',$events['end_date']);
                                      
              if(calendar::check_repeat_event_dif($dif,$events,$repeat_end))
              {
                $list[] = $events;
              }
            }                                                                                               
          break;
      }            
                            
    }
    
    //if we check events for single day then we have to re check repeat events
    if($is_single_day)
    {
      $list_tmp = array();
      foreach($list as $k=>$v)
      {
        if($date_to_timestamp>=$v['start_date'] and $date_to_timestamp<=$v['end_date'])
        {
          $list_tmp[] = $list[$k];
        }
      }
      
      $list = $list_tmp;
    } 
    
    return $list;
    
  }
  
  static public function check_repeat_event_dif($dif,$events,$repeat_end)
  {
    $check = true;
        
    if($dif>0)
    {
      //check interval
      if($dif/$events['repeat_interval']!=floor($dif/$events['repeat_interval'])) $check=false;            
      
      //check repeat limit
      if($events['repeat_limit']>0)            
      if(floor($dif/$events['repeat_interval'])>$events['repeat_limit']) $check=false;
    }
    else
    {
      $check=false; 
    } 
    
    //check repeat end date            
    if($repeat_end>0)
    {
      if($repeat_end<$events['start_date'])
      {
         $check=false;
      }
    }
    
    return $check;
  }  
  
  static public function get_access_by_report($calendar_id,$groups_id)
  {
    $info_query = db_query("select * from app_ext_calendar_access where calendar_id='" . db_input($calendar_id) . "' and access_groups_id='" . db_input($groups_id) . "'");
    if($info = db_fetch_array($info_query))
    {
      return $info['access_schema'];
    }
    else
    {
      return '';
    }
  }
  
  static public function get_personal_access()
  {
    $list = array();
    $access_query = db_query("select * from app_ext_calendar_access where calendar_type='personal'");
    while($access = db_fetch_array($access_query))
    {
      $list[] = $access['access_groups_id']; 
    }
    
    return $list;
  }
  
  static public function get_public_access($group_id)
  {
    $access_query = db_query("select * from app_ext_calendar_access where calendar_type='public' and access_groups_id='" . db_input($group_id) . "'");
    if($access = db_fetch_array($access_query))
    {
      return $access['access_schema'];
    }
    else
    {
      return '';
    }
  }
  
  static public function user_has_access($calendar_type)
  {
    global $app_user;
    
    if($app_user['group_id']==0) return true;
        
    $access_query = db_query("select * from app_ext_calendar_access where calendar_type='" . $calendar_type . "' and access_groups_id='" . db_input($app_user['group_id']) . "'");
    if($access = db_fetch_array($access_query))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  
  static public function user_has_personal_access()
  {
    return calendar::user_has_access('personal');
  }
  
  static public function user_has_public_access()
  {
    return calendar::user_has_access('public');
  }
  
  static public function user_has_public_full_access()
  {
    global $app_user;
                
    if($app_user['group_id']==0) return true;
        
    $access_query = db_query("select * from app_ext_calendar_access where calendar_type='public' and access_groups_id='" . db_input($app_user['group_id']) . "' and access_schema='full'");
    if($access = db_fetch_array($access_query))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  
  static public function user_has_reports_access($reports,$access_schema='')
  {
    global $app_user;
    
    if($app_user['group_id']==0) return true;
    
    $where_sql = '';
    
    if($access_schema=='full')
    {
      $where_sql = " and access_schema='full'";
    }
        
    $access_query = db_query("select * from app_ext_calendar_access where calendar_type='report' and calendar_id='" . db_input($reports['id']) . "' and access_groups_id='" . db_input($app_user['group_id']) . "'" . $where_sql);
    if($access = db_fetch_array($access_query))
    {
      if($access_schema=='full')
      {
        $access_schema = users::get_entities_access_schema($reports['entities_id'],$app_user['group_id']);
            
        if(users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0)
        {
          return false;
        }
        else
        {      
          return true;
        }
      }
      else
      {
        return true;
      }
    }
    else
    {
      return false;
    }
  }
  
  static public function get_events_repeat_types()
  {
    $list = array(''=>'',
                  'daily'=>TEXT_EXT_EVENT_REPEAT_DAILY,
                  'weekly'=>TEXT_EXT_EVENT_REPEAT_WEEKLY,
                  'monthly'=>TEXT_EXT_EVENT_REPEAT_MONTHLY,
                  'yearly'=>TEXT_EXT_EVENT_REPEAT_YEARLY,
                  );
    return $list;
  }
  
  static public function get_events_repeat_days()
  {
    $days = explode(',',str_replace('"','',TEXT_DATEPICKER_DAYS));
    $days[7] = $days[0];
    unset($days[0]);
    return $days;
  }
  
  
}