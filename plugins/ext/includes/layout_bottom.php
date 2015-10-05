<?php if($app_module=='ganttchart' and $app_action=='view'): ?>

<script type="text/javascript" src="js/jqueryGantt/libs/jquery.livequery.min.js"></script>
<script type="text/javascript" src="js/jqueryGantt/libs/jquery.timers.js"></script>
<script type="text/javascript" src="js/jqueryGantt/libs/platform.js"></script>
<script type="text/javascript" src="js/jqueryGantt/libs/date.js"></script>
<?php require(component_path('ext/ganttchart/i18n.js')) ?>
<script type="text/javascript" src="js/jqueryGantt/libs/dateField/jquery.dateField.js"></script>
<script type="text/javascript" src="js/jqueryGantt/libs/JST/jquery.JST.js"></script>

<script type="text/javascript" src="js/jqueryGantt/libs/jquery.svg.min.js"></script>

<!--In case of jquery 1.8-->
<script type="text/javascript" src="js/jqueryGantt/libs/jquery.svgdom.1.8.js"></script>

<script type="text/javascript" src="js/jqueryGantt/ganttUtilities.js"></script>
<script type="text/javascript" src="js/jqueryGantt/ganttTask.js"></script>
<script type="text/javascript" src="js/jqueryGantt/ganttDrawerSVG.js"></script>
<script type="text/javascript" src="js/jqueryGantt/ganttGridEditor.js"></script>
<script type="text/javascript" src="js/jqueryGantt/ganttMaster.js"></script> 

<?php endif ?>

<?php if($app_module_path=='ext/calendar/personal' or $app_module_path=='ext/calendar/public' or $app_module_path=='ext/calendar/report'): ?>
<script type="text/javascript" src="js/fullcalendar-2.3.0/lib/moment.min.js"></script>
<script type="text/javascript" src="js/fullcalendar-2.3.0/fullcalendar.min.js"></script>
<?php 
if(is_file($language_file_path = 'js/fullcalendar-2.3.0/lang/' . APP_LANGUAGE_SHORT_CODE . '.js')) 
  echo '<script type="text/javascript" src="' . $language_file_path . '"></script>';
?>
<?php endif ?>


<script>
jQuery(document).ready(function() {        
  <?php 
  if(strlen(session::get('plugin_ext_current_version'))==0) echo "$.ajax({url: '" . url_for("ext/ext/check_version") ."'});" 
  ?>
});
</script>