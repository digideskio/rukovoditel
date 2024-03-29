<?php

$entity_cfg = entities::get_cfg($current_entity_id);

switch($app_module_action)
{  
  case 'export':
    
    if(isset($_POST['fields']))
    {
      $export = array();
    
      $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
        
      $choices_cache = fields_choices::get_cache();
      
      $item = db_find('app_entity_' . $current_entity_id,$current_item_id);
              
      $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
      while($tabs = db_fetch_array($tabs_query))
      {   
        
        $export_data = array();
                 
        $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . implode(',',$_POST['fields']). ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
        while($field = db_fetch_array($fields_query))
        {            
          //check field access
          if(isset($fields_access_schema[$field['id']]))
          {
            if($fields_access_schema[$field['id']]=='hide') continue;
          }
                  
          switch($field['type'])
          {
            case 'fieldtype_created_by':
                $value = $item['created_by'];
              break;
            case 'fieldtype_date_added':
                $value = $item['date_added'];                
              break;
            case 'fieldtype_action':                
            case 'fieldtype_id':
                $value = $item['id'];
              break;
            default:
                $value = $item['field_' . $field['id']]; 
              break;
          }
          
          $output_options = array('class'=>$field['type'],
                              'value'=>$value,
                              'field'=>$field,
                              'item'=>$item,
                              'is_export'=>true,
                              'users_cache' =>$app_users_cache,                            
                              'choices_cache'=>$choices_cache,
                              'path'=>$current_path);
                              
          if(in_array($field['type'],array('fieldtype_textarea_wysiwyg')))
          {
            $output = trim(fields_types::output($output_options));
          }
          else
          {
            $output = trim(strip_tags(fields_types::output($output_options)));
          }   
           
          $export_data[] = array(fields_types::get_option($field['type'],'name',$field['name']),$output);
        }  
        
        if(count($export_data)>0)
        {
          $export[$tabs['name']] = $export_data;
        }                  
      }
      
        
      //echo '<pre>';
      //print_r($export);    
      //exit();
      
      $html_comments ='';
      if(users::has_comments_access('view') and $entity_cfg['use_comments']==1 and isset($_POST['export_comments']))
      {
        $html_comments = '
          <br><hr><b>' . TEXT_COMMENTS . '</b><br><hr>
            <table width="100%">';
        
        $listing_sql = "select * from app_comments where entities_id='" . db_input($current_entity_id) . "' and items_id='" . db_input($current_item_id) . "'  order by date_added desc";        
        $items_query = db_query($listing_sql);
        while($item = db_fetch_array($items_query))
        {

          $html_fields = '';
          $comments_fields_query = db_query("select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input($item['id']) . "' and f.id=ch.fields_id order by ch.id");
          while($field = db_fetch_array($comments_fields_query))
          {
            //check field access
            if(isset($fields_access_schema[$field['id']]))
            {
              if($fields_access_schema[$field['id']]=='hide') continue;
            }
                
            $output_options = array('class'=>$field['type'],
                                    'value'=>$field['fields_value'],
                                    'field'=>$field, 
                                    'path'=>$_POST['path'],                           
                                    'choices_cache'=>$choices_cache);
                  
            $html_fields .='                      
                <tr><th>&bull;&nbsp;' . $field['name'] . ':&nbsp;</th><td>' . trim(strip_tags(fields_types::output($output_options))). '</td></tr>           
            ';
          }
          
          if(strlen($html_fields)>0)
          {
            $html_fields = '<table>' . $html_fields . '</table>';
          }
          
          $attachments = fields_types::output(array('class'=>'fieldtype_attachments','value'=>$item['attachments'],'path'=>$_POST['path'],'field'=>array('entities_id'=>$current_entity_id),'item'=>array('id'=>$current_item_id)));
          $attachments = '<div>' . strip_tags($attachments) . '</div>';
          $html_fields = '<div class="comments_fields">' . $html_fields. '</div>';
        
           $html_comments .= '
            <tr>                  
              <td align="left" valign="top">' . $item['description'] . $attachments . $html_fields .  '</td>
              <td class="nowrap" valign="top">' . date(CFG_APP_DATETIME_FORMAT,$item['date_added']) . '<br>' . $app_users_cache[$item['created_by']]['name']. '<br>' . '</td>
            </tr>
            <tr>
              <td colspan="2"><hr></td>
            </tr>
          '; 
        
        }
        $html_comments .= '</table>';                
      }
      
      //echo $html_comments;
      //exit();
      
      
      $html = '
      <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        </head>
        <style>
          body { font-family: ipagp;}
          .comments_fields th{ text-align: left; font-weight: normal; }
        </style>
        <body>
          <table>';
      
      foreach($export as $tab=>$fields)
      {
        $html .= '
          <tr>
            <td valign="top"><br><b>' . $tab. '</b></td>
            <td valign="top"></td>
          </tr>
        ';
        
        foreach($fields as $v)
        $html .= '
          <tr>
            <td valign="top" width="30%">' . $v[0]. ': </td>
            <td valign="top">' . $v[1]. '</td>
          </tr>
        ';
      }
      
      $html .= '</table> ' . $html_comments . '          
          </body>
      </html>
      ';
      
      //echo $html;
      //exit();          
      
      $filename = str_replace(' ','_',trim($_POST['filename']));
                              
      require_once("includes/libs/dompdf/dompdf_config.inc.php");    
                                    
      $dompdf = new DOMPDF();
      $dompdf->load_html($html);
      $dompdf->render();
              
      $dompdf->stream($filename . ".pdf");
    }
    exit();
  break;
}  


