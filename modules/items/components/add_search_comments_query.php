<?php

$sql_query = array();
foreach(explode(' ',$_POST['search_keywords']) as $keyword)
{
  $sql_query[] = "description like '%" . db_input($keyword)  ."%'";     
}

$listing_sql_query = ' and (' . implode(' or ',$sql_query) . ')';
