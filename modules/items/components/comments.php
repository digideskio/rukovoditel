<div class="row">
  <div class="col-md-5">
    <div class="entitly-listing-buttons-left">
      <?php if(users::has_comments_access('create')) echo button_tag(TEXT_BUTTON_ADD_COMMENT, url_for('items/comments_form','path=' . $_GET['path'])) ?>
    </div>
  </div>
  <div class="col-md-7">
    <div class="entitly-listing-buttons-right">    
      <?php echo render_listing_search_form($entity_info['id'],'items_comments_listing') ?>
    </div>                    
  </div>
</div> 

<div class="row">
  <div class="col-md-12">
    <div id="items_comments_listing"></div>
  </div>
</div>

<script>
  function load_items_listing(listing_container,page,search_keywords)
  {      
    $('#'+listing_container).append('<div class="data_listing_processing"></div>');
    $('#'+listing_container).css("opacity", 0.5);
    
    $('#'+listing_container).load('<?php echo url_for("items/comments_listing")?>',{path:'<?php echo $_GET["path"]?>',page:page,search_keywords:$('#'+listing_container+'_search_keywords').val()},
      function(response, status, xhr) {
        if (status == "error") {                                 
           $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
        }
        $('#'+listing_container).css("opacity", 1);    
        
        appHandlePopover();                                                                                            
      }
    );
  }
  
  function reset_search()
  {
    $('#items_comments_listing_search_keywords').val('')
    load_items_listing('items_comments_listing',1)
  }   

  $(function() {     
    load_items_listing('items_comments_listing',1,'');                                                                         
  });
  
    
</script> 