var is_mobile = navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i);

function validate_user_form(form,url)
{    
  $.ajax({
    type: "POST",
    url: url,
    data: { username: $('#fields_12').val(), useremail: $('#fields_9').val() }
  })
  .done(function( msg ) {
      msg = msg.trim()      
      if(msg=='success')
      {
        form.submit();
      }
      else
      {
        $("div#form-error-container").html('<div class="note note-danger">'+msg+'</div>');
  			$("div#form-error-container").show();
        $("div#form-error-container").delay(5000).fadeOut();	
      }
  });      
}

function use_editor(id,is_focus)
{     
  if(!is_mobile)
  {
    CKEDITOR.config.baseFloatZIndex = 20000;
    CKEDITOR.config.height = 150;
    CKEDITOR_holders[id] = CKEDITOR.replace(id,{startupFocus:is_focus,language: app_language_short_code});//
  
    CKEDITOR_holders[id].on("instanceReady",function() {
      jQuery(window).resize();
  
      $(".cke_button__maximize").bind('click', function() {
      	$('#ajax-modal').css('display','block')
      })
    });
  }   
}  

function rukovoditel_app_init()
{
  $('.datepicker').datepicker({
            rtl: App.isRTL(),
            autoclose: true,
            weekStart: app_cfg_first_day_of_week,
            format: 'yyyy-mm-dd',
        });
        
 $(".datetimepicker").datetimepicker({
        autoclose: true,
        isRTL: App.isRTL(),
        format: "yyyy-mm-dd hh:ii",
        weekStart: app_cfg_first_day_of_week,
        pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left")
    });      
      
                     
 $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner = 
          '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
              '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
          '</div>';
        
            
  $( "textarea.editor" ).each(function() { use_editor($(this).attr('id'),false) });
  
  appHandlePopover();          
} 

function open_dialog(url)
{
  //destroy uploadify (IE9 required to open modalbox)
  if($("#uploadify_file_upload").length>0 && !is_mobile) $('#uploadify_file_upload').uploadify('destroy');
           
  var $modal = $('#ajax-modal');
    
  // create the backdrop and wait for next modal to be triggered
  if(!$('body').hasClass('modal-open'))
    $('body').modalmanager('loading');
    
  setTimeout(function(){
      $modal.load(url, '', function(response, status, xhr){
                                                          
      if($('#ajax-modal textarea').hasClass('editor') || $('#ajax-modal div').hasClass('ajax-modal-width-790') )          
      {        
        width = 790
      }
      else
      {
        width = 590        
      }
                
      $modal.modal({width:width}); 
            
      if((response.search('app_db_error')>0 || response.search('Fatal error')>0) && response.search('modal-header')==-1)
      {
        $('#ajax-modal').html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button><h4 class="modal-title">Error</h4></div>'+response+'<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>');
      }
                                               
    });
  }, 1); 
}

function appHandleUniformInListing()
{
  var test = $("input[type=checkbox]:not(.toggle), input[type=radio]:not(.toggle, .star)");
  if (test.size() > 0) {
      test.each(function () {
          if ($(this).parents(".checker").size() == 0) {
              $(this).show();
              $(this).uniform();
          }
      });
  }
      
  appHandlePopover();
}  

function appHandlePopover()
{
  $('[data-toggle="popover"]').popover({trigger:'hover',html:true,
     placement: function (context, source) {
        var position = $(source).position();
        
        //alert(position.left);
        
        if (position.left < 350) {
            return "right";
        }
        
        if (position.left > 350) {
            return "left";
        }
        
        if (position.top < 200){
            return "bottom";
        }
  
        return "top";
    }  
  })
}

function appHandleUniform()
{
  var test = $("input[type=checkbox]:not(.toggle), input[type=radio]:not(.toggle, .star)");
  if (test.size() > 0) {
      test.each(function () {
          if ($(this).parents(".checker").size() == 0) {
              $(this).show();
              $(this).uniform();
          }
      });
  }
  
 $('.datepicker').datepicker({
              rtl: App.isRTL(),
              autoclose: true,
              weekStart: app_cfg_first_day_of_week,
              format: 'yyyy-mm-dd',
          });
          
 $(".datetimepicker").datetimepicker({
        autoclose: true,
        isRTL: App.isRTL(),
        format: "yyyy-mm-dd hh:ii",
        weekStart: app_cfg_first_day_of_week,
        pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left")
    }); 
    
    
  $( "textarea.editor" ).each(function() { use_editor($(this).attr('id'),false) });
  $( "textarea.editor-auto-focus" ).each(function() { use_editor($(this).attr('id'),true) });
  
   $('.colorpicker-default').colorpicker()      
   
   $('.chosen-select').each(function(){      
      width = '90%';

      if($(this).hasClass('input-small')) width = '120px';
      if($(this).hasClass('input-medium')) width = '240px';
      if($(this).hasClass('input-large')) width = '320px';
      if($(this).hasClass('input-xlarge')) width = '480px';
      
      $(this).chosen({width: width,
                      include_group_label_in_selected: true,
                      no_results_text:i18n['TEXT_NO_RESULTS_MATCH'],
                      placeholder_text_single:i18n['TEXT_SELECT_AN_OPTION'],
                      placeholder_text_multiple:i18n['TEXT_SELECT_SOME_OPTIONS']
                      });
   })
                
}

function update_crud_checkboxes(view_access,group_id)
{
  if(view_access=='')
  {    
    $('.crud_'+group_id).css('display','none')
  }
  else
  {
    $('.crud_'+group_id).css('display','block')
  }
}

function set_access_to_all_fields(access, group_id)
{
  if(access!='')
  {
    $( ".access_group_"+group_id).each(function() {
      $(this).val(access) 
    });
  }
}

function listing_reset_search(listing_container)
{
  $('#'+listing_container+'_search_keywords').val('')
  load_items_listing(listing_container,1)
}  

function listing_order_by(listing_container,fields_id,clause)
{
  if(app_key_ctrl_pressed)
  {
    order_fields = $('#'+listing_container+'_order_fields').val().split(',');
    is_in_order = false;
    for(var i=0;i<order_fields.length;i++)
    {
      if(order_fields[i]==fields_id+'_asc' || order_fields[i]==fields_id+'_desc')
      {
        order_fields[i]=fields_id+'_'+clause;
        is_in_order = true;
      }
    }
    
    if(is_in_order)
    {
      $('#'+listing_container+'_order_fields').val(order_fields.join(','))    
    }
    else
    {
      $('#'+listing_container+'_order_fields').val($('#'+listing_container+'_order_fields').val()+','+fields_id+'_'+clause)
    }
  }
  else
  {
    $('#'+listing_container+'_order_fields').val(fields_id+'_'+clause)
  }
  
  load_items_listing(listing_container, 1);
} 

function select_all_by_classname(id,class_name)
{
  if($('#'+id).attr('checked'))
  {      
    $('.'+class_name).each(function(){            
      $(this).attr('checked',true)
      $('#uniform-'+$(this).attr('id')+' span').addClass('checked')          
    })
  }
  else
  {        
    $('.'+class_name).each(function(){      
      $(this).attr('checked',false)
      $('#uniform-'+$(this).attr('id')+' span').removeClass('checked')
    })
  } 
}

function app_search_item_by_id()
{
  $('#search_item_by_id_result').addClass('ajax-loading');
  url = $('#search_item_by_id_form').attr('action');
  id = $('#search_item_by_id').val();
  related_entities_id = $('#search_item_by_id_button').attr('data-related-entities-id');
  
  
  $('#search_item_by_id_result').load(url,{id:id,related_entities_id:related_entities_id},function(){
    $('#search_item_by_id_result').removeClass('ajax-loading');
  })
  return false;
}


