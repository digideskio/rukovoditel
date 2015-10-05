<?php echo ajax_modal_template_header(TEXT_CONFIGURE_DASHBOARD) ?>

<div class="modal-body ajax-modal-width-790">

<div><?php echo TEXT_CONFIGURE_DASHBOARD_INFO ?></div><br>

<table style="width: 100%; max-width: 960px;">
  <tr>
    <td valign="top" width="50%">
      <fieldset>
        <legend><?php echo TEXT_REPORTS_ON_DASHBOARD ?></legend>
<div class="cfg_listing">        
  <ul id="reports_on_dashboard" class="sortable">
  <?php
  $reports_query = db_query("select r.*,e.name as entities_name from app_reports r, app_entities e where e.id=r.entities_id and r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' and r.in_dashboard=1 order by r.dashboard_sort_order, r.name");
  while($v = db_fetch_array($reports_query))
  {
    echo '<li id="report_' . $v['id'] . '"><div>' . $v['name'] . '</div></li>';
  }
  ?> 
  </ul>         
</div>
              
      </fieldset>
    
    </td>
    <td style="padding-left: 25px;" valign="top">
    
      <fieldset>
        <legend><?php echo TEXT_MY_REPORTS ?></legend>
<div class="cfg_listing">        
<ul id="reports_excluded_from_dashboard" class="sortable">
<?php
  $reports_query = db_query("select r.*,e.name as entities_name from app_reports r, app_entities e where e.id=r.entities_id and r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' and r.in_dashboard!=1 order by e.name, r.name");
  while($v = db_fetch_array($reports_query))
  {
    echo '<li id="report_' . $v['id'] . '"><div>' . $v['name'] . '</div></li>';
  }
?> 
</ul>
</div>                     
      </fieldset>
      
      
    </td>
  </tr>
</table>

</div>

<?php echo form_tag('dashboard', url_for('dashboard/')) ?>

<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() {         
    	$( "ul.sortable" ).sortable({
    		connectWith: "ul",
    		update: function(event,ui){  
          data = '';  
          $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("dashboard/","action=sort_reports")?>',data: data});
        }
    	});
      
  });  
</script>