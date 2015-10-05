<?php require(component_path('items/navigation')) ?>


<div class="row">
    <div class="col-md-8 project-info">
      
    <div class="panel panel-default item-description">
			<div class="panel-heading">
				<h3 class="panel-title">        
          <?php echo $app_breadcrumb[count($app_breadcrumb)-1]['title'] ?>             
        </h3>
			</div>
			<div class="panel-body">
      
        <div class="panel-body-actions">        
          <ul class="list-inline">
          
            <?php if(users::has_comments_access('create') and $entity_cfg['use_comments']==1): ?>
              <li><?php echo button_tag(TEXT_BUTTON_ADD_COMMENT,url_for('items/comments_form','path=' . $_GET['path']),true,array('class'=>'btn btn-default btn-sm'),'fa-comment-o') ?></li>
            <?php endif ?>
            
            <?php if(users::has_access('update')): ?>
              <li><?php echo button_tag(TEXT_BUTTON_EDIT,url_for('items/form','id=' . $current_item_id. '&entity_id=' . $current_entity_id . '&path=' . $_GET['path'] . '&redirect_to=items_info'),true,array('class'=>'btn btn-default btn-sm'),'fa-edit') ?></li>
            <?php endif ?>
                                    
            <li>
              <div class="btn-group">
									<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
									<?php echo TEXT_MORE_ACTIONS ?> <i class="fa fa-angle-down"></i>
									</button>
									<ul class="dropdown-menu" role="menu">
                      <li><?php echo link_to_modalbox('<i class="fa fa-file-pdf-o"></i> ' . TEXT_BUTTON_EXPORT,url_for('items/single_export','path=' . $_GET['path'])) ?></li>
                    
                    <?php if(users::has_access('update') and $current_entity_id==1): ?>
                       <li><?php echo link_to('<i class="fa fa-unlock-alt"></i> ' .TEXT_CHANGE_PASSWORD, url_for('items/change_user_password','path=' . $_GET['path']))?></li>
                    <?php endif ?>  
                      
                    <?php if(users::has_access('delete')): ?>
                      <li><a href="#" onClick="open_dialog('<?php echo url_for('items/delete','id=' .$current_item_id . '&entity_id=' . $current_entity_id . '&path=' . $_GET['path']) ?>'); return false;"><i class="fa fa-trash-o"></i> <?php echo TEXT_BUTTON_DELETE?></a></li>
                    <?php endif ?>
																					
									</ul>
								</div>
            </li>
                        
          </ul>
        </div>
          
        <div class="itemDescription">
          <?php echo items::render_content_box($current_entity_id,$current_item_id) ?>
        </div>
        
      </div>
    </div>

    <?php if(users::has_comments_access('view') and $entity_cfg['use_comments']==1) require(component_path('items/comments')); ?>
    
    </div>
    
    <div class="col-md-4" style="position:static">

    <div class="panel panel-info item-details">
  		<div class="panel-body item-details">            
      <?php echo items::render_info_box($current_entity_id,$current_item_id) ?>
      </div>
    </div>
    </div>
</div>    

