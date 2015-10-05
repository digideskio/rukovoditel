
<h3 class="page-title"><?php echo TEXT_EXT_MY_СALENDAR ?></h3>

<div id="calendar_loading" class="loading_data"></div>
<div id="calendar"></div>

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
        open_dialog('<?php echo url_for("ext/calendar/personal_form")?>'+'&date='+start) 
			},
      eventClick: function(calEvent, jsEvent, view) {
        if(calEvent.url.length>0)
        {
          open_dialog(calEvent.url)
        }        
        return false;
      },
      eventResize: function(event, delta, revertFunc) {
        $.ajax({type: "POST",url: "<?php echo url_for('ext/calendar/personal','action=resize')?>",data: {id:event.id,end:event.end.format()}});
      },
      eventDrop: function(event, delta, revertFunc) {
        if(event.end)
        {
          $.ajax({type: "POST",url: "<?php echo url_for('ext/calendar/personal','action=drop')?>",data: {id:event.id,start:event.start.format(),end:event.end.format()}});
        }
        else
        {
          $.ajax({type: "POST",url: "<?php echo url_for('ext/calendar/personal','action=drop')?>",data: {id:event.id,start:event.start.format()}});
        }
      },
      eventMouseover: function(calEvent, jsEvent, view) {        
        if(calEvent.title.length>23 || calEvent.description.length>0)
          $(this).popover({html:true,title:calEvent.title,content:calEvent.description,placement:'top',container:'body'}).popover('show');        
      },
      eventMouseout:function(calEvent, jsEvent, view) {
        $(this).popover('hide');
      },			
			events: {
				url: '<?php echo url_for("ext/calendar/personal","action=get_events")?>',
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