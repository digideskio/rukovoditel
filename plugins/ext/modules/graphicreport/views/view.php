<h3 class="page-title"><?php echo $reports['name'] ?></h3>

<?php
  $chart_type_list = array('line'   =>  '<a href="' . url_for('ext/graphicreport/view','id=' . $_GET['id'] . '&chart_type=line') . '">' . TEXT_EXT_CHART_TYPE_LINE . '</a>',
                           'column' =>  '<a href="' . url_for('ext/graphicreport/view','id=' . $_GET['id'] . '&chart_type=column') . '">' . TEXT_EXT_CHART_TYPE_COLUMN . '</a>');
                           
  echo select_button_tag($chart_type_list,($chart_type=='line' ? TEXT_EXT_CHART_TYPE_LINE:TEXT_EXT_CHART_TYPE_COLUMN));
  
  $period_list = array('daily'=>'<a href="' . url_for('ext/graphicreport/view','id=' . $_GET['id'] . '&period=daily') . '">' .TEXT_DAILY. '</a>',
                       'monthly'=>'<a href="' . url_for('ext/graphicreport/view','id=' . $_GET['id'] . '&period=monthly') . '">' .TEXT_MONTHLY. '</a>',
                       'yearly'=>'<a href="' . url_for('ext/graphicreport/view','id=' . $_GET['id'] . '&period=yearly') . '">' .TEXT_YEARLY. '</a>');
                       
  $period_name_list = array('daily'=>TEXT_DAILY,
                            'monthly'=>TEXT_MONTHLY,
                            'yearly'=>TEXT_YEARLY);                      
  
  echo select_button_tag($period_list, $period_name_list[$period]);
   
  if($period=='daily')
  {       
    echo select_button_tag($years_list,$year_filter) . ' ' . select_button_tag($months_list,$months_array[$month_filter-1]);
  }
  elseif($period=='monthly')
  {       
    echo select_button_tag($years_list,$year_filter);
  }    
?>

<div class="btn-group btn-filters">
  <?php echo '<button type="button" class="btn btn-default">' . TEXT_BUTTON_CONFIGURE_FILTERS . '</button>' ?>
  
  <div class="btn-group btn-group-solid">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>
	 <?php echo reports::render_filters_dropdown_menu($fiters_reports_id,'','graphicreport' . $_GET['id']) ?>
  </div>
  
  <?php
    foreach(reports::get_parent_reports($reports_info['id']) as $parent_reports_id)
    {                          
      echo '
       <div class="btn-group btn-group-solid"> 
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>' . 
        reports::render_filters_dropdown_menu($reports_info['id'],'','graphicreport' . $_GET['id'],$parent_reports_id) . 
      '</div>';
    }
    
    
  ?>		
</div> 

<p>
  <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</p>

<script type="text/javascript">

$(function () {
    $('#container').highcharts({
        chart: {
            type: '<?php echo $chart_type ?>'
        },
        title: {
            text: '<?php echo (count($yaxis_html)==0 ? TEXT_NO_RECORDS_FOUND:"")?>'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [<?php echo implode(',',$xaxis)?>],
            labels: {
                rotation: -90
            }
        },
        yAxis: {
            title: {
                text: ''
            },
             min: 0,

            labels: {
                formatter: function () {
                    return this.axis.defaultLabelFormatter.call(this);
                }            
            }
        },
        tooltip: {            
            valueDecimals: 2,
            valuePrefix: '',
        },

        series: [<?php echo implode(',',$yaxis_html)?>]
    });
});

</script>