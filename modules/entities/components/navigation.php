<?php $entities_cfg = entities::get_cfg($_GET['entities_id']) ?>

<div class="navbar navbar-default" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
  		<span class="sr-only"></span>
  		<span class="fa fa-bar "></span>
  		<span class="fa fa-bar fa-align-justify"></span>
  		<span class="fa fa-bar"></span>
		</button>
		<a class="navbar-brand " href="<?php echo url_for('entities/entities_configuration&entities_id=' . $_GET['entities_id']) ?>"><?php echo entities::get_name_by_id($_GET['entities_id']) ?></a>
	</div>
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
			<li class="nav_entities_configuration">
				<?php echo link_to(TEXT_NAV_GENERAL_CONFIG,url_for('entities/entities_configuration&entities_id=' . $_GET['entities_id'])) ?>
			</li>
			<li class="nav_fields nav_fields_choices">
				<?php echo link_to(TEXT_NAV_FIELDS_CONFIG,url_for('entities/fields&entities_id=' . $_GET['entities_id'])) ?>
			</li>
      <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo TEXT_NAV_VIEW_CONFIG ?> <i class="fa fa-angle-down"></i></a>
				<ul class="dropdown-menu">
					<li>
						<?php echo link_to(TEXT_NAV_FORM_CONFIG,url_for('entities/forms','entities_id=' . $_GET['entities_id'])) ?>
					</li>
					<li>
						<?php echo link_to(TEXT_NAV_LISTING_CONFIG,url_for('entities/listing','entities_id=' . $_GET['entities_id'])) ?>
					</li>
					<li>
						<?php echo link_to(TEXT_NAV_LISTING_FILTERS_CONFIG,url_for('entities/listing_filters','entities_id=' . $_GET['entities_id'])) ?>
					</li>
          <?php if($_GET['entities_id']==1): ?>
          <li>
						<?php echo link_to(TEXT_NAV_USER_PUBLIC_PROFILE_CONFIG,url_for('entities/user_public_profile','entities_id=' . $_GET['entities_id'])) ?>
					</li>
          <?php endif ?>
				</ul>
			</li>
      <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo TEXT_NAV_ACCESS_CONFIG ?> <i class="fa fa-angle-down"></i></a>
				<ul class="dropdown-menu">
					<li>
						<?php echo link_to(TEXT_NAV_ENTITY_ACCESS,url_for('entities/access','entities_id=' . $_GET['entities_id'])) ?>
					</li>
					<li>
						<?php echo link_to(TEXT_NAV_FIELDS_ACCESS,url_for('entities/fields_access','entities_id=' . $_GET['entities_id'])) ?>
					</li>
				</ul>
			</li>
      <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo TEXT_NAV_COMMENTS_CONFIG ?> <i class="fa fa-angle-down"></i></a>
				<ul class="dropdown-menu">
					<li>
						<?php echo link_to(TEXT_NAV_COMMENTS_ACCESS,url_for('entities/comments_access','entities_id=' . $_GET['entities_id'])) ?>
					</li>
					<li>
						<?php echo link_to(TEXT_NAV_COMMENTS_FIELDS,url_for('entities/comments_form','entities_id=' . $_GET['entities_id'])) ?>
					</li>
				</ul>
			</li>
		</ul>

	</div>
	<!-- /.navbar-collapse -->
</div>

<script>
  $(function() { 
    $('.nav_<?php echo $app_action ?>').addClass('active');                                                                  
  });
  
</script>   