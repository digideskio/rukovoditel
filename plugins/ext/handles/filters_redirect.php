<?php

if(strstr($app_redirect_to,'graphicreport'))
{
  $id = str_replace('graphicreport','',$app_redirect_to);
  redirect_to('ext/graphicreport/view','id=' . $id);
}

if(strstr($app_redirect_to,'calendarreport'))
{
  $id = str_replace('calendarreport','',$app_redirect_to);
  redirect_to('ext/calendar/report','id=' . $id . (strlen($app_path) ? '&path=' . $app_path:''));
}

