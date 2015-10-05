<?php

  switch($app_module_action)
  {    
    case 'restore':
       if(is_file(DIR_FS_BACKUPS . $_GET['file']))
       {
       
          if(is_file(DIR_FS_BACKUPS . $_GET['file']))
          {
             set_time_limit(0);
             
             $tables_query = db_query("show tables");
             while($tables = db_fetch_array($tables_query))
             {
               db_query('DROP TABLE ' . current($tables));    
             }
                                       
             $fp = fopen(DIR_FS_BACKUPS . $_GET['file'], 'r');
             
             $file_cache = $sql = $table = $insert = '';
             $query_len = 0;
             $execute = 0;
                                                   
             while(($str = app_db_restore_fp_read_str($fp)) !== false)
             {        
               if (empty($str) || preg_match("/^(#|--)/", $str)) 
               {				
            	   continue;
            	 }
                                   	                     
                $query_len += strlen($str);
                
                //echo $str  . '<hr>';
                                                                          
                if (!$insert && preg_match("/INSERT INTO ([^`]*?) VALUES([^`]*?)/i", $str, $m)) 
                {
          				if ($table != $m[1]) 
                  {
          				    $table = $m[1];  					  					  					  					    					
          				}
                
                  $insert = $m[0] . ' ';
                                                                                     
          				$sql .= '';    				
              	}
          			else
                {
                  $sql .= $str;                            
                } 
                        
                if (!$insert && preg_match("/CREATE TABLE `([^`]*?)`/i", $str, $m) && $table != $m[1])
                {              
        				  $table = $m[1];
        				  $insert = '';                  				    				
        			  }
                
                if ($sql) 
                {                            
                  if (preg_match("/;$/", $str)) 
                  {
                		$sql = rtrim($insert . $sql, ";");
                		
                		$insert = '';
                	  $execute = 1;            		            		
                	}
                	
                	if ($query_len >= 65536 && preg_match("/,$/", $str)) 
                  {                                                             
                		$sql = rtrim($insert . $sql, ",");                
                	  $execute = 1;
                	}
                	
                	if ($execute) 
                  {            		            		            		            		
                		db_query($sql);
                    
                		$sql = '';
                		$query_len = 0;
                		$execute = 0;
                	}
                	
                }                                             
             }                                     
          }
                 
         $alerts->add(TEXT_BACKUP_RESTORED,'success');
       }
       
       redirect_to('users/login','action=logoff');
      break;
    case 'download':
        if(is_file(DIR_FS_BACKUPS . $_GET['file']))
        {
          $filename = app_get_backup_filename($_GET['file']);
          
          header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
          header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
          header("Cache-Control: no-cache, must-revalidate");
          header("Pragma: no-cache");
          header("Content-Type: Application/octet-stream");
          header("Content-disposition: attachment; filename=" . $filename);
      
          readfile(DIR_FS_BACKUPS . $_GET['file']);
          
          exit();
        }
        else
        {
          $alerts->add(TEXT_FILE_NOT_FOUD,'error');
          
          redirect_to('tools/db_backup');
        }
      break;
    case 'delete':
        if(is_file(DIR_FS_BACKUPS . $_GET['file']))
        {
          unlink(DIR_FS_BACKUPS . $_GET['file']);
          
          $alerts->add(TEXT_BACKUP_DELETED,'success');
        }
        else
        {
          $alerts->add(TEXT_FILE_NOT_FOUD,'error');
        }
        
        redirect_to('tools/db_backup');
      break;
    case 'backup':
    
        app_create_db_backup();
                        
        $alerts->add(TEXT_BACKUP_CREATED,'success');
        redirect_to('tools/db_backup');
      break;
  }