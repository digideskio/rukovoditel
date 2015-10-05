<h3 class="page-title"><?php echo TEXT_ENTITIES_HEADING ?></h3>

<?php echo button_tag(TEXT_ADD_NEW_ENTITY,url_for('entities/entities_form')) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th>Action</th>
    <th>#</th>
    <th width="100%">Name</th>
    <th>Sort Order</th>    
  </tr>
</thead>
<tbody>
<?php if(count($entities_list)==0) echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; ?>
<?php foreach($entities_list as $v): ?>
<tr>
  <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('entities/entities_delete','id=' . $v['id'])) . ' ' . button_icon_edit(url_for('entities/entities_form','id=' . $v['id'])) . ' ' . button_icon(TEXT_CREATE_SUB_ENTITY,'fa fa-plus',url_for('entities/entities_form','parent_id=' . $v['id'])) ?></td>
  <td><?php echo $v['id']?></td>
  <td><?php echo  str_repeat('&nbsp;-&nbsp;', $v['level']) . link_to($v['name'],url_for('entities/entities_configuration','entities_id=' . $v['id']))?></td>
  <td><?php echo $v['sort_order']?></td>
</tr>  
<?php endforeach ?>
</tbody>
</table>
</div>

