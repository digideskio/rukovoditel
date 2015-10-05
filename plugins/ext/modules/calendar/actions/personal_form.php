<?php

if(!calendar::user_has_personal_access())
{
  exit();
}

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_ext_calendar_events',$_GET['id']);
  
  $obj['start_date'] = str_replace(' 00:00','',date('Y-m-d H:i',$obj['start_date']));  
  $obj['end_date'] =  str_replace(' 00:00','',date('Y-m-d H:i',$obj['end_date']));
  
  if($obj['repeat_end']>0)
  {
    $obj['repeat_end'] = date('Y-m-d',$obj['repeat_end']);
  }
  else
  {
    $obj['repeat_end'] = '';
  }
  
}
else
{
  $obj = db_show_columns('app_ext_calendar_events');
  
  $date_milliseconds = $_GET['date'];
  $date_timestamp = ($date_milliseconds+14400000)/1000;
    
  $obj['start_date'] = str_replace(' 00:00','',date('Y-m-d H:i',$date_timestamp));
  $obj['end_date'] = str_replace(' 00:00','',date('Y-m-d H:i',$date_timestamp));
  
  if($obj['start_date']==$obj['end_date'] and strstr($obj['end_date'],':'))
  {    
    $obj['end_date'] = str_replace(' 00:00','',date('Y-m-d H:i',strtotime("+30 minute",$date_timestamp)));
  }  
  
  $obj['bg_color'] = '#3a87ad';
  $obj['repeat_interval'] = 1;
   
}