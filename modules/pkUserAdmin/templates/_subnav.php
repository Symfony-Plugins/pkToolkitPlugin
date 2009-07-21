<?php // Regular admins don't get to change which groups and permissions exist, ?>
<?php // that has serious consequences and doesn't make much sense unless you're a ?>
<?php // PHP developer extending the system. To add a user to one of the two standard groups ?>
<?php // (admin and editor) or other groups we already added to the system, just edit that user ?>

<?php if ($sf_user->isSuperAdmin()): ?>
  <ul>
  <li class="dashboard"><h4><?php echo link_to('User Dashboard', 'pkUserAdmin/index', array('class' => 'b')) ?></h4></li>
  <li><?php echo link_to('Add User<span></span>', 'pkUserAdmin/new', array('class' => 'pk-btn add')) ?></li>

  <li class="dashboard"><h4><?php echo link_to('Group Dashboard', 'pkGroupAdmin/index', array('class' => 'b')) ?></h4></li>
  <li><?php echo link_to('Add Group<span></span>', 'pkGroupAdmin/new', array('class' => 'pk-btn add')) ?></li>

  <li class="dashboard"><h4><?php echo link_to('Permissions Dashboard', 'pkPermissionAdmin/index', array('class' => 'b')) ?></h4></li>
  <li><?php echo link_to('Add Permission<span></span>', 'pkPermissionAdmin/new', array('class' => 'pk-btn add')) ?></li>
  </ul>
<?php endif ?>