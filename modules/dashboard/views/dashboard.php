<?php

require(component_path('dashboard/style_custumizer'));

app_reset_selected_items();

$reports_query = db_query("select * from app_reports where created_by='" . db_input($app_logged_users_id) . "' and in_dashboard=1 and reports_type in ('standard') order by dashboard_sort_order, name");
while($reports = db_fetch_array($reports_query))
{
  //get report entity info
  $entity_info = db_find('app_entities',$reports['entities_id']);
  $entity_cfg = entities::get_cfg($reports['entities_id']);
  
  //check if parent reports was not set
  if($entity_info['parent_id']>0 and $reports['parent_id']==0)
  {
    reports::auto_create_parent_reports($reports['id']);
  }
  
  //get report entity access schema
  $access_schema = users::get_entities_access_schema($reports['entities_id'],$app_user['group_id']);
  
  $add_button = ''; 
  if(users::has_access('create',$access_schema))
  { 
    if($entity_info['parent_id']==0)
    {
      $url = url_for('items/form','path=' . $reports['entities_id'] . '&redirect_to=report_' . $reports['id']); 
    }
    else
    {
      $url = url_for('reports/prepare_add_item','reports_id=' . $reports['id']);
    }
    $add_button = button_tag((strlen($entity_cfg['insert_button'])>0 ? $entity_cfg['insert_button'] : TEXT_ADD), $url) . ' ';
  } 
      


  $listing_container = 'entity_items_listing' . $reports['id'] . '_' . $reports['entities_id']; 
  echo '
                            
    <div class="row dashboard-reports-container" id="dashboard-reports-container">
      <div class="col-md-12">
      
      <div class="row">
        <div class="col-md-12"><h3 class="page-title">' . $reports['name'] . '</h3></div>
      </div>
      
      <div class="row">
        <div class="col-md-6">   
             ' . $add_button . '      
            <div class="btn-group">
      				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
      				' . TEXT_WITH_SELECTED . '<i class="fa fa-angle-down"></i>
      				</button>
      				<ul class="dropdown-menu" role="menu">
      					<li>
      						' . link_to_modalbox(TEXT_EXPORT,url_for('items/export','path=' . $reports["entities_id"]  . '&reports_id=' . $reports['id'] ))  .'
      					</li>
      				</ul>
      			</div> 
            
        <div class="btn-group btn-filters">        
        ' . button_tag(TEXT_BUTTON_CONFIGURE_FILTERS,url_for('reports/filters','reports_id=' . $reports['id']),false,array('class'=>'btn btn-default')) . '
        
        <div class="btn-group btn-group-solid">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>
				  ' . reports::render_filters_dropdown_menu($reports['id'])  . '
        </div>
        ';
        
          foreach(reports::get_parent_reports($reports['id']) as $parent_reports_id)
          {                          
            echo '
             <div class="btn-group btn-group-solid"> 
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>' . 
              reports::render_filters_dropdown_menu($reports['id'],'','report',$parent_reports_id) . 
            '</div>';
          }
          
          
echo '        	                
      </div>             
             
        </div>        
        <div class="col-md-6">                        
         ' . render_listing_search_form($reports["entities_id"],$listing_container) . '                         
        </div>
      </div> 
            
      <div id="' . $listing_container . '" class="entity_items_listing"></div>
      ' . input_hidden_tag($listing_container . '_order_fields',$reports['listing_order_fields']) . '
        
      </div>
    </div>
    
    
    <script>
      $(function() {     
        load_items_listing("' . $listing_container . '",1);                                                                         
      });    
    </script> 
  ';
}


require(component_path('items/load_items_listing.js'));

?>

