[?php include_stylesheets_for_form($form) ?]
[?php include_javascripts_for_form($form) ?]

<div id="pk-admin-filters-container">

	  <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter')) ?]" method="post" id="pk-admin-filters-form">
		
			<h3>Filters</h3>

		  [?php if ($form->hasGlobalErrors()): ?]
		    [?php echo $form->renderGlobalErrors() ?]
		  [?php endif; ?]
		
	    <div class="pk-admin-filters-fields">

	        [?php foreach ($configuration->getFormFilterFields($form) as $name => $field): ?]
	        [?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?]
					<div class="pk-form-row" id="pk-admin-filters-[?php echo str_replace("_","-",$name) ?]">
	          [?php include_partial('<?php echo $this->getModuleName() ?>/filters_field', array(
	            'name'       => $name,
	            'attributes' => $field->getConfig('attributes', array()),
	            'label'      => $field->getConfig('label'),
	            'help'       => $field->getConfig('help'),
	            'form'       => $form,
	            'field'      => $field,
	            'class'      => 'pk-form-row pk-admin-'.strtolower($field->getType()).' pk-admin-filter-field-'.$name,
	          )) ?]
					</div>
	        [?php endforeach; ?]

        [?php echo $form->renderHiddenFields() ?]
				<div class="pk-form-row submit">
					<ul class="pk-controls pk-admin-filter-controls">
						<li>[?php echo jq_link_to_function('Filter<span></span>', '$("#pk-admin-filters-form").submit();', array('class' => 'pk-btn', )) ?]</li>
						<li>[?php echo link_to(__('reset', array(), 'pk-admin'), '<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter'), array('query_string' => '_reset', 'method' => 'post', 'class' => 'pk-btn icon pk-cancel event-default')) ?]</li>
					</ul>
				</div>
				
	    </div>
	  </form>

</div>
