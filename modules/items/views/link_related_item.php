
<?php echo ajax_modal_template_header(TEXT_LINK_RECORD) ?>

<?php
  $entity_info = db_find('app_entities',$_GET['related_entities']);
?>
    
<div class="modal-body">    

<?php echo form_tag('search_item_by_id_form', url_for('items/','action=search_item_by_id&path=' . $_GET['path']), array('class'=>'form-horizontal','onSubmit' => 'return app_search_item_by_id()')) ?>
  <div class="form-group">
  	<label class="col-md-6 control-label"><?php echo TEXT_ENTITY ?></label>
    <div class="col-md-6">	  	        
       <p class="form-control-static"><?php echo $entity_info['name'] ?></p>
    </div>			
  </div>
  <div class="form-group">
  	<label class="col-md-6 control-label"><?php echo TEXT_SEARCH_RECORD_BY_ID ?></label>
    <div class="col-md-6">	  	        
      <div class="input-group input-small">
    		<input id="search_item_by_id" type="text" placeholder="<?php echo TEXT_SEARCH ?>" class="form-control input-small">
    		<span class="input-group-btn">  			
          <!--a onClick="return app_search_item_by_id()" data-related-entities-id="<?php echo $_GET['related_entities'] ?>" href="<?php echo url_for('items/','action=search_item_by_id&path=' . $_GET['path']) ?>" title="<?php echo TEXT_SEARCH ?>" id="search_item_by_id_button" class="btn btn-info" onclick=""><i class="fa fa-search"></i></a-->
          <button type="submit" class="btn btn-info" data-related-entities-id="<?php echo $_GET['related_entities'] ?>"  title="<?php echo TEXT_SEARCH ?>" id="search_item_by_id_button"><i class="fa fa-search"></i></button>
    		</span>
    	</div>      
    </div>			
  </div>
</form>  
  
<?php echo form_tag('add_related_item', url_for('items/','action=add_related_item&path=' . $_GET['path']), array('class'=>'form-horizontal')) ?>

<?php echo input_hidden_tag('related_entities_id',$_GET['related_entities']) ?>
  
  <div id="search_item_by_id_result"></div>
  
</form>  

</div>
 
<?php echo ajax_modal_template_footer('hide-save-button') ?>

    
    
 
