
<h3 class="page-title"><?php echo TEXT_HEADING_DB_BACKUP ?></h3>

<?php

$dir = dir(DIR_FS_BACKUPS);
$backups = array();
while ($file = $dir->read()) 
{
  if (!is_dir(DIR_FS_BACKUPS . $file) and $file!='.htaccess') 
  {        
      $backups[] = $file;
  }  
}
rsort($backups);
?>
<div><?php echo button_tag(TEXT_BUTTON_CREATE_BACKUP,url_for('tools/db_backup','action=backup'),false) ?></div>
<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>        
    <th width="100%"><?php echo TEXT_NAME ?></th>
    <th><?php echo TEXT_RESTORE ?></th>        
  </tr>
</thead>
<tbody>
<?php foreach($backups as $file): ?>
  <tr>
    <td class="nowrap"><?php echo button_tag(TEXT_BUTTON_DELETE,url_for('tools/db_backup_delete','file=' . $file)) . ' ' . button_tag(TEXT_BUTTON_DOWNLOAD,url_for('tools/db_backup','action=download&file=' . $file),false);?></td>
    <td><?php echo app_get_backup_filename($file)  ?></td>
    <td><?php echo button_tag(TEXT_BUTTON_RESTORE,url_for('tools/db_restore','file=' . $file)) ?></td>
  </tr>
<?php endforeach ?>

<?php if(count($backups)==0) echo '<tr><td colspan="3">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';?>
<tbody>
</table>
</div>
