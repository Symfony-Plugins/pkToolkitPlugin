[?php use_helper('I18N', 'Date', 'jQuery') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]

<div id="pk-admin-container" class="[?php echo $sf_params->get('module') ?]">

  [?php include_partial('<?php echo $this->getModuleName() ?>/list_bar', array('filters' => $filters)) ?]
  	
<?php if ($this->configuration->hasFilterForm()): ?>
	<div id="pk_admin_filter_container">
    [?php include_partial('<?php echo $this->getModuleName() ?>/filters', array('form' => $filters, 'configuration' => $configuration)) ?]
  </div>
<?php endif; ?>

	<div id="pk-admin-header" class="subnav shadow">
			<div class="content-container">
				<div class="content">
			    [?php include_partial('<?php echo $this->getModuleName() ?>/list_header', array('pager' => $pager)) ?]
				</div>
			</div>
  </div>

	<div id="pk-admin-content" class="main">
		<div class="content-container">
			<div class="content">

				[?php include_partial('<?php echo $this->getModuleName() ?>/flashes') ?]

<?php if ($this->configuration->getValue('list.batch_actions')): ?>
		    <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'batch')) ?]" method="post" id="pk_admin_batch_form">
<?php endif; ?>
		    [?php include_partial('<?php echo $this->getModuleName() ?>/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?]
		    <ul class="pk_admin_actions">
		      [?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper)) ?]
		      [?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]
		    </ul>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
		    </form>
<?php endif; ?>
			</div>
		</div>
	</div>

  <div id="pk-admin-footer">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_footer', array('pager' => $pager)) ?]
  </div>
</div>
