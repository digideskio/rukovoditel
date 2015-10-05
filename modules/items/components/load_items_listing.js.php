<script>
  function load_items_listing(listing_container,page)
  {
    //parse listing id
    listing_data = listing_container.replace('entity_items_listing','').split('_');
    
    //set default redirect
    redirect_to = 'report_'+listing_data[0];
    
    //set default path
    path = listing_data[1];
    
    //replace default path by current path    
    if($('#entity_items_listing_path').length)
    {
      path = $('#entity_items_listing_path').val()
      redirect_to = '';
    }
    
    //set redirect to dashboard if it's dashboard page    
    if($('#dashboard-reports-container').length)
    {      
      redirect_to = 'dashboard';
    }
                    
    $('#'+listing_container).append('<div class="data_listing_processing"></div>');
    
    $('#'+listing_container).css("opacity", 0.5);
    
    $('#'+listing_container).load('<?php echo url_for("items/listing")?>',{redirect_to: redirect_to, path:path,reports_entities_id:listing_data[1],reports_id:listing_data[0],listing_container:listing_container,page:page,search_keywords:$('#'+listing_container+'_search_keywords').val(), listing_order_fields:$('#'+listing_container+'_order_fields').val()},
      function(response, status, xhr) {
        if (status == "error") {                                 
           $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
        }
        
        $('#'+listing_container).css("opacity", 1); 
        
        appHandleUniformInListing()  
        
        handle_itmes_select(listing_container)                                                                             
      }
    );                
  }   
  
  function handle_itmes_select(listing_container)
  {  
    $('#'+listing_container+' .items_checkbox').click(function(){   
    
      listing_data = listing_container.replace('entity_items_listing','').split('_');
      
      if($('#entity_items_listing_path').length){
        listing_data[1] = $('#entity_items_listing_path').val()
      }
         
      $.ajax({type: "POST",url: '<?php echo url_for("items/select_items","action=select")?>',data: {id:$(this).val(),checked: $(this).attr('checked'),reports_id: listing_data[0],path:listing_data[1]}});
    })
    
    $('#'+listing_container+' .select_all_items').click(function(){

      listing_data = listing_container.replace('entity_items_listing','').split('_');
      
      if($('#entity_items_listing_path').length){
        listing_data[1] = $('#entity_items_listing_path').val()
      }
            
      $.ajax({type: "POST",url: '<?php echo url_for("items/select_items","action=select_all")?>',data: {id:$(this).val(),checked: $(this).attr('checked'),reports_id: $(this).val(),search_keywords:$('#'+listing_container+'_search_keywords').val(),path:listing_data[1], listing_order_fields:$('#'+listing_container+'_order_fields').val()}});
      
      if($(this).attr('checked'))
      {
        
        $('#'+listing_container+' .items_checkbox').each(function(){            
          $(this).attr('checked',true)
          $('#'+listing_container+' #uniform-items_'+$(this).val()+' span').addClass('checked')          
        })
      }
      else
      {
        $('#'+listing_container+' .items_checkbox').each(function(){
          $(this).attr('checked',false)
          $('#'+listing_container+' #uniform-items_'+$(this).val()+' span').removeClass('checked')
        })
      }            
    })
  }            
</script> 