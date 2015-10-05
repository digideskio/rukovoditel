<?php echo ajax_modal_template_header(TEXT_WARNING) ?>

<?php echo form_tag('backup', url_for('tools/db_backup','action=restore&file=' . $_GET['file'])); ?> 

<div class="modal-body">    
<?php echo sprintf(TEXT_DB_RESTORE_CONFIRMATION,app_get_backup_filename($_GET['file']))?>
</div>

<?php echo ajax_modal_template_footer(TEXT_BUTTON_RESTORE) ?>

</form>  