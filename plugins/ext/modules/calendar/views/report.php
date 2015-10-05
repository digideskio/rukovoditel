<?php
  if(isset($_GET['path']))
  { 
    $path_info = items::parse_path($_GET['path']);
    $current_path = $_GET['path']; 
    $current_entity_id = $path_info['entity_id'];
    $current_item_id = true; // set to true to set off default title     
    $current_path_array = $path_info['path_array'];
    $app_breadcrumb = items::get_breadcrumb($current_path_array);
    
    require(component_path('items/navigation'));
  } 
?>

<h3 class="page-title"><?php echo $reports['name'] ?></h3>

<div class="btn-group btn-filters btn-clandar-filters">
  <?php echo '<button type="button" class="btn btn-default">' . TEXT_BUTTON_CONFIGURE_FILTERS . '</button>' ?>
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"><i class="fa fa-angle-down"></i></button>
	<?php echo reports::render_filters_dropdown_menu($fiters_reports_id,(isset($_GET['path']) ? $_GET['path']:''),'calendarreport' . $_GET['id']) ?>	
</div> 


<div id="calendar_loading" class="loading_data"></div>
<div id="calendar"></div>

<?php if(calendar::user_has_reports_access($reports,'full')): ?>
 
<script>

	$(document).ready(function() {
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: '<?php echo date("Y-m-d")?>',
      firstDay: '<?php echo CFG_APP_FIRST_DAY_OF_WEEK ?>',
      timezone: false,
			selectable: true,
			selectHelper: true,
      editable: true,
			eventLimit: true, // allow "more" link when too many events
			select: function(start, end) {				
         
			},
      eventClick: function(calEvent, jsEvent, view) {
        $(this).attr('target','_new')
      },
      eventResize: function(event, delta, revertFunc) {
        $.ajax({type: "POST",url: "<?php echo url_for('ext/calendar/report','action=resize&id=' . $reports['id'])?>",data: {id:event.id,end:event.end.format()}});
      },
      eventDrop: function(event, delta, revertFunc) {
        if(event.end)
        {
          $.ajax({type: "POST",url: "<?php echo url_for('ext/calendar/report','action=drop&id=' . $reports['id'])?>",data: {id:event.id,start:event.start.format(),end:event.end.format()}});
        }
        else
        {
          $.ajax({type: "POST",url: "<?php echo url_for('ext/calendar/report','action=drop&id=' . $reports['id'])?>",data: {id:event.id,start:event.start.format()}});
        }
      },
      eventMouseover: function(calEvent, jsEvent, view) {        
        if(calEvent.title.length>23)
          $(this).popover({html:true,title:calEvent.title,content:calEvent.description,placement:'top',container:'body'}).popover('show');        
      },
      eventMouseout:function(calEvent, jsEvent, view) {
        $(this).popover('hide');
      },			
			events: {
				url: '<?php echo url_for("ext/calendar/report","action=get_events&id=" . $reports["id"] . (isset($_GET["path"])? "&path=" . $_GET["path"]:"") )?>',
        error: function() {
				  alert('<?php echo TEXT_ERROR_LOADING_DATA ?>')
				}				
			},
      loading: function(bool) {
				$('#calendar_loading').toggle(bool);

			}
      
		});
		
	});  

</script>

<?php else: ?>

<script>

	$(document).ready(function() {
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: '<?php echo date("Y-m-d")?>',
      firstDay: '<?php echo CFG_APP_FIRST_DAY_OF_WEEK ?>',
      timezone: false,
			selectable: false,
			selectHelper: false,      
      editable: false,
			eventLimit: true, // allow "more" link when too many events

      eventMouseover: function(calEvent, jsEvent, view) {        
        if(calEvent.title.length>23)
          $(this).popover({html:true,title:calEvent.title,content:calEvent.description,placement:'top',container:'body'}).popover('show');        
      },
      eventMouseout:function(calEvent, jsEvent, view) {
        $(this).popover('hide');
      },			
			events: {
				url: '<?php echo url_for("ext/calendar/report","action=get_events&id=" . $reports["id"] . (isset($_GET["path"])? "&path=" . $_GET["path"]:"") )?>',
        error: function() {
				  alert('<?php echo TEXT_ERROR_LOADING_DATA ?>')
				}				
			},
      loading: function(bool) {
				$('#calendar_loading').toggle(bool);

			}
      
		});
		
	});  

</script>

<?php endif ?>