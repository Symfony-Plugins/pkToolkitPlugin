<div id="pk-admin-bar" [?php if (count($sf_user->getAttribute('<?php echo $this->getModuleName() ?>.filters', null, 'admin_module'))): ?]class="has-filters"[?php endif ?]>
	<h2 class="pk-admin-title you-are-here">[?php echo <?php echo $this->getI18NString('list.title') ?> ?]</h2>
  [?php echo jq_link_to_function("Admin Controls", "$('#pk_admin_filter_container').slideToggle()" ,array('class' => 'pk-admin-settings-btn', 'title'=>'Manage This Page')) ?]
</div>
<br class="clear c"/>