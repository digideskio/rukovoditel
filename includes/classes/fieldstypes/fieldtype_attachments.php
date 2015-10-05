<?php

class fieldtype_attachments
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_ATTACHMENTS_TITLE);
  }
  
  
  function render($field,$obj,$params = array())
  {
    global $uploadify_attachments, $current_path;
    
    $field_id = $field['id'];
    
    $uploadify_attachments[$field_id] = array();
    
    if(strlen($obj['field_' . $field['id']])>0)
    {
      $uploadify_attachments[$field_id] = explode(',',$obj['field_' . $field['id']]);
    }
    
    $timestamp = time();
    $form_token = md5('unique_salt' . $timestamp);
    
    $html = '
      <div class="form-control-static"> <input style="cursor: pointer" type="file" name="uploadifive_attachments_upload_' . $field_id . '" id="uploadifive_attachments_upload_' . $field_id . '" /> </div>
      
      <div id="uploadifive_queue_list_' . $field_id . '"></div>
      <div id="uploadifive_attachments_list_' . $field_id . '">' . attachments::render_preview($field_id, $uploadify_attachments[$field_id]) . '</div>
      <script type="text/javascript">
		
      var is_file_uploading = null;
      
  		$(function() {
  			$("#uploadifive_attachments_upload_' . $field_id  . '").uploadifive({
  				"auto"             : true,  
          "dnd"              : false, 
          "buttonClass"      : "btn btn-default btn-upload",
          "buttonText"       : "<i class=\"fa fa-upload\"></i> ' . TEXT_ADD_ATTACHMENTS. '",				
  				"formData"         : {
  									   "timestamp" : ' . $timestamp . ',
  									   "token"     : "' .  $form_token . '"
  				                     },
  				"queueID"          : "uploadifive_queue_list_' . $field_id . '",
          "fileSizeLimit" : "' . ((int)ini_get("post_max_size")<(int)ini_get("upload_max_filesize") ? (int)ini_get("post_max_size") : (int)ini_get("upload_max_filesize")) . 'MB",
  				"uploadScript"     : "' .  url_for('items/items','action=attachments_upload&path=' . $current_path . '&field_id=' . $field_id ,true)  . '",
          "onUpload"         :  function(filesToUpload){
            is_file_uploading = true;
          },
  				"onQueueComplete" : function(file, data) {
            is_file_uploading = null  
            $(".uploadifive-queue-item.complete").fadeOut();
            $("#uploadifive_attachments_list_' . $field_id . '").append("<div class=\"loading_data\"></div>");
            $("#uploadifive_attachments_list_' . $field_id . '").load("' .  url_for('items/items','action=attachments_preview&field_id=' . $field_id . '&path=' . $current_path . '&token=' . $form_token) . '"); 

          }
  			});
                        
        $("button[type=submit]").bind("click",function(){                         
            if(is_file_uploading)
            {
              alert("' . TEXT_PLEASE_WAYIT_FILES_LOADING . '"); return false;
            }                           
          });
        
  		});
	</script>
    '; 
    
    return $html;
  }
  
  function process($options)
  {
    $attachments = explode(',',$options['value']);
            
    if(isset($_POST['delete_attachments']))
    {
      foreach($_POST['delete_attachments'] as $filename=>$v)
      {                 
          
        if(($key = array_search($filename,$attachments))!==false)
        {                    
          unset($attachments[$key]);
        }
        
        $file = attachments::parse_filename($filename);
        if(is_file(DIR_WS_ATTACHMENTS . $file['folder'] .'/'. $file['file_sha1']))
        {
          unlink(DIR_WS_ATTACHMENTS . $file['folder']  .'/' . $file['file_sha1']);
        }
      }
    }
    
    //remove out of data tmp attachments
    db_query("delete from app_attachments where date_added!='" . date('Y-m-d') . "'");
    
    $options['value'] = implode(',',$attachments);
            
    return $options['value'];
  }
  
  function output($options)
  {    
    if(strlen($options['value'])>0)
    {
      if(isset($options['is_export']))
      {
        $html = array();
        foreach(explode(',',$options['value']) as $filename)
        {
          $file = attachments::parse_filename($filename);
          $html[] = $file['name'];
        }
        
        return implode(', ',$html);
      }
      else
      {
       
        $fancybox_css_class ='';
                        
        $html = '
        <div class="table-scrollable">
          <table class="table">
            <tbody>
              <tr>
                <td>
                  <ul style="padding: 0px; margin: 0px;">';
        foreach(explode(',',$options['value']) as $filename)
        {
          $file = attachments::parse_filename($filename);
          
          if($file['is_image'])
          {
            if(strlen($fancybox_css_class)==0)
            {
              $fancybox_css_class = 'fancybox' . time();
            }
            
            $link = link_to($file['name'],url_for('items/info&path=' . $options['path'] ,'&action=preview_attachment_image&file=' . urlencode(base64_encode($filename))),array('class'=>$fancybox_css_class,'title'=>$file['name'],'data-fancybox-group'=>'gallery'));
          }
          elseif($file['is_pdf'])
          {
            $link = link_to($file['name'],url_for('items/info&path=' . $options['path'] ,'&action=download_attachment&preview=1&file=' . urlencode(base64_encode($filename))),array('target'=>'_blank'));  
          }
          elseif($file['is_exel'])
          {          
            $link = link_to($file['name'],url_for('items/info&path=' . $options['path'] ,'&action=preview_attachment_exel&file=' . urlencode(base64_encode($filename))),array('target'=>'_blank'));  
          }
          else
          {
            $link = link_to($file['name'],url_for('items/info&path=' . $options['path'] ,'&action=download_attachment&file=' . urlencode(base64_encode($filename))));
          }
          
          $link .= ' ' . link_to('<i class="fa fa-download"></i>',url_for('items/info&path=' . $options['path'] ,'&action=download_attachment&file=' . urlencode(base64_encode($filename)))); 
          
          $html .= '
              <li style="list-style-image: url(' . url_for_file($file['icon']) . '); margin-left: 20px;">' .  $link. ' <small>(' . $file['size']. ')</small></li>
            ';
        }
        $html .= '  
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>';
        
        if(strlen($fancybox_css_class)>0)
        {
          $html .= '
            <script type="text/javascript">
            	$(document).ready(function() {
            		$(".' . $fancybox_css_class . '").fancybox({type: "ajax"});
            	});
            </script>
          ';
        }
        
        return $html ;
      }
    }
    else
    {
      return '';
    }
  }
}