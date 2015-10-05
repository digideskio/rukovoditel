<?php

  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, "http://rukovoditel.net/current_version/ext_version.txt");  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
  
  $plugin_ext_current_version = curl_exec($ch);  
    
  curl_close($ch);