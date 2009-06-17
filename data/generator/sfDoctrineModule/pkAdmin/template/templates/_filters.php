[?php include_stylesheets_for_form($form) ?]
[?php include_javascripts_for_form($form) ?]

<div class="pk_admin_filter">
	<h3>Filters</h3>
  [?php if ($form->hasGlobalErrors()): ?]
    [?php echo $form->renderGlobalErrors() ?]
  [?php endif; ?]

  <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter')) ?]" method="post" id="pk_admin_filter_form">
    <table cellspacing="0">
      <tfoot>
        <tr>
          <td colspan="2">
            [?php echo $form->renderHiddenFields() ?]
						[?php echo jq_link_to_function('Filter<span></span>', '$("#pk_admin_filter_form").submit();', array('class' => 'pk-btn', )) ?]
						<span class="or">or</span>
						[?php echo link_to(__('reset', array(), 'pk_admin'), '<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter'), array('query_string' => '_reset', 'method' => 'post', 'class' => 'pk-btn icon pk-cancel event-default')) ?]
          </td>
        </tr>
      </tfoot>
      <tbody>
        [?php foreach ($configuration->getFormFilterFields($form) as $name => $field): ?]
        [?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?]
          [?php include_partial('<?php echo $this->getModuleName() ?>/filters_field', array(
            'name'       => $name,
            'attributes' => $field->getConfig('attributes', array()),
            'label'      => $field->getConfig('label'),
            'help'       => $field->getConfig('help'),
            'form'       => $form,
            'field'      => $field,
            'class'      => 'pk_admin_form_row pk_admin_'.strtolower($field->getType()).' pk_admin_filter_field_'.$name,
          )) ?]
        [?php endforeach; ?]
      </tbody>
    </table>
  </form>
</div>
