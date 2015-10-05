<?php
  app_reset_selected_items();
   
  $listing_container = 'entity_items_listing' . $reports_info['id'] . '_' . $reports_info['entities_id'];
  
  //get report entity info
  $entity_info = db_find('app_entities',$reports_info['entities_id']);
  $entity_cfg = entities::get_cfg($reports_info['entities_id']);
  
  //check if parent reports was not set
  if($entity_info['parent_id']>0 and $reports_info['parent_id']==0)
  {
    reports::auto_create_parent_reports($reports_info['id']);
  }
  
  //get report entity access schema
  $access_schema = users::get_entities_access_schema($reports_info['entities_id'],$app_user['group_id']);
  
  
  if($reports_info['reports_type']=='entity_menu')
  {
    $page_title = (strlen($entity_cfg['menu_title'])>0 ? $entity_cfg['menu_title'] : $entities['name']);
  }
  else
  {
    $page_title = $reports_info['name'];
  }
   
  
     
?>

<h3 class="page-title"><?php echo $page_title ?></h3>

<div class="row">
  <div class="col-md-5">
    <div class="entitly-listing-buttons-left">
    
      <?php 
        if(users::has_access('create',$access_schema))
        { 
          if($entity_info['parent_id']==0)
          {
            $url = url_for('items/form','path=' . $reports_info['entities_id'] . '&redirect_to=report_' . $reports_info['id']); 
          }
          else
          {
            $url = url_for('reports/prepare_add_item','reports_id=' . $reports_info['id']);
          }
          echo button_tag((strlen($entity_cfg['insert_button'])>0 ? $entity_cfg['insert_button'] : TEXT_ADD), $url) . ' ';
        } 
      ?>
      
      <div class="btn-group">
				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
				<?php echo TEXT_WITH_SELECTED ?> <i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>
						<?php echo link_to_modalbox(TEXT_EXPORT,url_for('items/export','path=' . $reports_info["entities_id"]  . '&reports_id=' . $reports_info['id'] )) ?>
					</li>
				</ul>
			</div>
      
      <div class="btn-group btn-filters">        
        <?php echo button_tag(TEXT_BUTTON_CONFIGURE_FILTERS,url_for('reports/filters','reports_id=' . $reports_info['id']),false,array('class'=>'btn btn-default')) ?>
        
        <div class="btn-group btn-group-solid">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>
				  <?php echo reports::render_filters_dropdown_menu($reports_info['id']) ?>
        </div>
        
        <?php
          foreach(reports::get_parent_reports($reports_info['id']) as $parent_reports_id)
          {                          
            echo '
             <div class="btn-group btn-group-solid"> 
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>' . 
              reports::render_filters_dropdown_menu($reports_info['id'],'','report',$parent_reports_id) . 
            '</div>';
          }
          
          
        ?>	                
      </div> 
      
      
      <?php echo button_tag(TEXT_BUTTON_CONFIGURE_SORTING,url_for('reports/sorting','reports_id=' . $reports_info['id']),true,array('class'=>'btn btn-default'))?>    
      
    </div>
  </div>
  <div class="col-md-7">
    <div class="entitly-listing-buttons-right">    
      <?php echo render_listing_search_form($reports_info["entities_id"],$listing_container) ?>            
    </div>                    
  </div>
</div> 

<div class="row">
  <div class="col-xs-12">
    <div id="<?php echo $listing_container;  ?>" class="entity_items_listing"></div>
  </div>
</div>

<?php echo input_hidden_tag($listing_container . '_order_fields',$reports_info['listing_order_fields']) ?>

<?php require(component_path('items/load_items_listing.js')); ?>

<script>
  
  $(function() {     
    load_items_listing('<?php echo $listing_container  ?>',1);                                                                         
  });      
    
</script> 

