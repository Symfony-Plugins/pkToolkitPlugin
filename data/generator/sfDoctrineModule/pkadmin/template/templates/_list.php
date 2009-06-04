<div class="pk_admin_list">
  [?php if (!$pager->getNbResults()): ?]
    <p>[?php echo __('No result', array(), 'pk_admin') ?]</p>
  [?php else: ?]
    <table cellspacing="0" class="pk_admin_list_table">
      <thead>
        <tr>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
          <th id="pk_admin_list_batch_actions"><input id="pk_admin_list_batch_checkbox" class="pk_admin_batch_checkbox" type="checkbox" onclick="checkAll();" /></th>
<?php endif; ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_th_<?php echo $this->configuration->getValue('list.layout') ?>', array('sort' => $sort)) ?]
<?php if ($this->configuration->getValue('list.object_actions')): ?>
          <th id="pk_admin_list_th_actions">[?php echo __('Actions', array(), 'pk_admin') ?]</th>
<?php endif; ?>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="<?php echo count($this->configuration->getValue('list.display')) + ($this->configuration->getValue('list.object_actions') ? 1 : 0) + ($this->configuration->getValue('list.batch_actions') ? 1 : 0) ?>">
						<div class="pk_admin_list_results">
	            [?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'pk_admin') ?]
	            [?php if ($pager->haveToPaginate()): ?]
	              [?php // echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'pk_admin') ?]
	            [?php endif; ?]
						</div>
            [?php if ($pager->haveToPaginate()): ?]
              [?php include_partial('<?php echo $this->getModuleName() ?>/pagination', array('pager' => $pager)) ?]
            [?php endif; ?]	
          </th>
        </tr>
      </tfoot>
      <tbody>
        [?php $n=1; $total = count($pager->getResults()); foreach ($pager->getResults() as $i => $<?php echo $this->getSingularName() ?>): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?]
          <tr class="pk_admin_row [?php echo $odd ?] [?php echo ($n == $total)? 'last':'' ?]">
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
            [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_batch_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper)) ?]
<?php endif; ?>
            [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_<?php echo $this->configuration->getValue('list.layout') ?>', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
<?php if ($this->configuration->getValue('list.object_actions')): ?>
            [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper)) ?]
<?php endif; ?>
          </tr>
        [?php $n++; endforeach; ?]
      </tbody>
    </table>
  [?php endif; ?]
</div>
<script type="text/javascript">
/* <![CDATA[ */
function checkAll()
{
  var boxes = document.getElementsByTagName('input'); for(index in boxes) { box = boxes[index]; if (box.type == 'checkbox' && box.className == 'pk_admin_batch_checkbox') box.checked = document.getElementById('pk_admin_list_batch_checkbox').checked } return true;
}
/* ]]> */
</script>
