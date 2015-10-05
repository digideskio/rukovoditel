<?php if($app_module=='ganttchart' and $app_action=='view'): ?>
<link rel=stylesheet href="js/jqueryGantt/platform.css" type="text/css">
<link rel=stylesheet href="js/jqueryGantt/libs/dateField/jquery.dateField.css" type="text/css">
<link rel=stylesheet href="js/jqueryGantt/gantt.css" type="text/css">
<link rel=stylesheet href="js/jqueryGantt/ganttPrint.css" type="text/css" media="print">
<link rel="stylesheet" type="text/css" href="js/jqueryGantt/libs/jquery.svg.css">
<?php endif ?>


<?php if($app_module_path=='ext/calendar/personal' or $app_module_path=='ext/calendar/public' or $app_module_path=='ext/calendar/report'): ?>
<link rel=stylesheet href="js/fullcalendar-2.3.0/fullcalendar.min.css" type="text/css">
<?php endif ?>