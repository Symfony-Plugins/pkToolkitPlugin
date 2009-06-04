<div class="pk_pager_navigation">
		
	[?php if ($pager->getPage() == 1):?]
		<span class="pk_pager_navigation_image pk_pager_navigation_first pk_pager_navigation_disabled">First Page</span>	
	  <span class="pk_pager_navigation_image pk_pager_navigation_previous pk_pager_navigation_disabled">Previous Page</span>
	[?php else: ?]
		<a href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=1" class="pk_pager_navigation_image pk_pager_navigation_first">First Page</a>
  	<a href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getPreviousPage() ?]" class="pk_pager_navigation_image pk_pager_navigation_previous">Previous Page</a>
	[?php endif ?]


  [?php foreach ($pager->getLinks() as $page): ?]
    [?php if ($page == $pager->getPage()): ?]
      <span class="pk_page_navigation_number pk_pager_navigation_disabled">[?php echo $page ?]</span>
    [?php else: ?]
      <a href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $page ?]" class="pk_page_navigation_number">[?php echo $page ?]</a>
    [?php endif; ?]
  [?php endforeach; ?]


	[?php if ($pager->getPage() == $pager->getLastPage()):?]
	  <span class="pk_pager_navigation_image pk_pager_navigation_next pk_pager_navigation_disabled">Next Page</span>
		<span class="pk_pager_navigation_image pk_pager_navigation_last pk_pager_navigation_disabled">Last Page</span>	
	[?php else: ?]
	  <a href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getNextPage() ?]" class="pk_pager_navigation_image pk_pager_navigation_next">Next Page</a>
  	<a href="[?php echo url_for('<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getLastPage() ?]" class="pk_pager_navigation_image pk_pager_navigation_last">Last Page</a>
	[?php endif ?]

</div>
