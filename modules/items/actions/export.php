<?php


switch($app_module_action)
{  
  case 'export':
      if(!isset($app_selected_items[$_POST['reports_id']])) $app_selected_items[$_POST['reports_id']] = array();
      
      if(count($app_selected_items[$_POST['reports_id']])>0 and isset($_POST['fields']))
      {    
        $current_entity_info = db_find('app_entities',$current_entity_id);
        
        $listing_fields = array();
        $export = array();
        $heading = array();
        
        //adding parent item
        if($current_entity_info['parent_id']>0)
        {
          $heading[] = TEXT_RELATIONSHIP_HEADING;
        }
                
        //adding reserved fields               
        $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . implode(',',$_POST['fields']). ") and f.entities_id='" . db_input($current_entity_id) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
        while($fields = db_fetch_array($fields_query))
        {
          $heading[] = fields_types::get_option($fields['type'],'name',$fields['name']);
          
          $listing_fields[] = $fields;
        } 
                                      
        //adding item url
        $heading[] = TEXT_URL_HEADING;
        
        $export[] = $heading;
        
        $selected_items = implode(',',$app_selected_items[$_POST['reports_id']]);
        $listing_sql = "select e.* from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")" ;        
        $items_query = db_query($listing_sql);
        while($item = db_fetch_array($items_query))
        {
          $row = array();
        
          if($current_entity_info['parent_id']>0)
          {
            $path_info_in_report = items::get_path_info($current_entity_id,$item['id']);
            
            $row[] = trim(strip_tags($path_info_in_report['parent_name']));        
          }                      
          
          foreach($listing_fields as $field)
          {
                        
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
                                    'choices_cache'=>$app_choices_cache,
                                    'users_cache'=>$app_users_cache,
                                    'reports_id'=> $_POST['reports_id'],
                                    'path'=> (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path']  :$current_path));
                                    
                                                                         
            $row[] = trim(strip_tags(fields_types::output($output_options)));                                                                                                                        
          }    
                              
          $row[] = url_for('items/info', 'path=' . (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path']  :$current_path . '-' . $item['id']));
          
          $export[] = $row;                                            
        } 
                
        //echo '<pre>';
        //print_r($export);
        //exit();
        
        $filename = str_replace(' ','_',trim($_POST['filename']));
                
        require('includes/libs/PHPExcel/PHPExcel.php');
        
        $objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getProperties()->setCreator($app_user['name'])
							 ->setLastModifiedBy($app_user['name'])
							 ->setTitle($filename)
							 ->setSubject('')
							 ->setDescription('')
							 ->setKeywords('')
							 ->setCategory('');
               
        $objPHPExcel->getActiveSheet()->fromArray($export, null, 'A1');
      
        $objWorksheet = $objPHPExcel->getActiveSheet();;
        
        $highest_column = $objWorksheet->getHighestColumn();
        
        for ($col = 'A'; $col != $highest_column; $col++) 
        {
  	       $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
           $objPHPExcel->getActiveSheet()->getStyle($col.'1')->getFont()->setBold(true);
        }                                                                              
        
        $objPHPExcel->getActiveSheet()->getColumnDimension($highest_column)->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle($highest_column.'1')->getFont()->setBold(true);
                        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($filename);
                        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . addslashes($filename) . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        ob_clean();
        flush();
        
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');        
        $objWriter->save('php://output');                       
      }
                  
    exit();
  break;
}  