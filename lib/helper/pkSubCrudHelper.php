<?php

use_helper('Form');
use_helper('jQuery');

// $type is usually the model class, in lower case, but sometimes (as with "profile") that is too annoying to use
// in CSS etc., so it's a parameter here and also a property of each form object.
//
// $subtype is the subtype, in lower case, otherwise exactly as found in the form subclass name ("essentials" or "registration"). 
// It is also a property of each form object.
//
// In other functions below we get these from the form object, but here we are in the show action so we don't have
// a form object to get them from yet.
//
// $publishedColumn is the name of the boolean column that indicates this subform is ready for publication.
//
// $canEditMethod is the name of the method of the object that returns whether the user can edit
// the object. If you do not specify a method, $object->userCanEdit() will be called.
// If you pass the string 'read-only', the user will never see an Edit button, which is useful when
// you want to display only the static view of the object reusing the same partials etc

function pk_sub_crud_chunk($label, $type, $subtype, $object, $publishedColumn = false, $canEditMethod = 'userCanEdit')
{
  $s = '';
  ob_start();
  $ok = false;
  if ($publishedColumn === false)
  {
    $ok = true;
  }
  elseif ($object->get($publishedColumn))
  {
    $ok = true;
  }

  // If the user can edit then they have to have access whether it's published or not!  
  if ($canEditMethod === 'read-only')
  {
    $canEdit = false;
  }
  else
  {
    $canEdit = $object->$canEditMethod();
  }
  if ($canEdit)
  {
    $ok = true;
  }
  if ($ok)
  {
  ?>
		<li class="form-chunk" id="form-chunk-<?php echo $subtype ?>">
		  <h3><?php echo $label ?><?php if ($canEdit): ?><?php echo pk_sub_crud_edit('edit', $type, $subtype, $object) ?><?php endif ?></h3>

      <div id="<?php echo "$type-$subtype" ?>">
        <?php echo include_partial("$type/$subtype", array($type => $object)) ?>
      </div>
    </li>
  <?php
  }
  return ob_get_clean();
}

function pk_sub_crud_edit($label, $type, $subtype, $object)
{
  $editButton = $type.'-form-edit-'.$subtype;
  $displayData = $type.'-'.$subtype;
  
  return jq_link_to_remote('edit', array(
    'url'      => '@'.$type.'_edit?id='.$object->getId(), 
    'method'   => 'get', 
    'update'   => $displayData, 
    'with'     => '"form='.$subtype.'"', 
    'before'   => sprintf("$('#%s').data('pk-form-swap', $('#%s').html()); pkBusy(this)", $displayData, $displayData), 
  	'complete' => sprintf("pkReady('#%s'); $('#%s').hide()", $editButton, $editButton),
  ), array(
    'class' => 'pk-form-edit-button',
    'id' => $editButton
  )); 
}

// Outputs the AJAX form for a chunk

function pk_sub_crud_form_tag($form)
{
  list($type, $subtype, $displayData) = _pk_sub_crud_form_info($form);
  
  // Necessary when we're editing a relation (EventUser) rather than the thing itself (Event)
  if (method_exists($form, 'getCrudObjectId'))
  {
    $oid = $form->getCrudObjectId();
  }
  else
  {
    $oid = $form->getObject()->getId();
  }

  $s = jq_form_remote_tag(array(
    'url' => "@$type" . "_update?id=$oid&form=$subtype", 
    'update' => $displayData, 
    // Redisplay the edit button only if the update does not contain a form.
    // This way the edit form is not resurrected by validation errors
    'complete' => "if (!$('#$type-$subtype form').length) { $('#$type-form-edit-$subtype').show(); }"));

  $s .= '<input type="hidden" name="sf_method" value="PUT" />';
  $s .= pk_sub_crud_form_body($form);

  // Oops I left this out earlier
  $s .= "</form>\n";
  return $s;
}

// Used only for the 'new' action, and targets the 'create' action. 
// Does NOT create an AJAX form, just follows the same styling. $form is usually a 
// form that is also used as a chunk later to allow editing later of the minimum required
// fields of the form. Or it might be a subclass of that form to allow
// for some differences in behavior.

function pk_sub_crud_create_form_tag($form)
{
  list($type, $subtype, $displayData) = _pk_sub_crud_form_info($form);
  $s = '<form method="POST" action="' . url_for("@$type" . "_create") . '">'; 
  ob_start();
  include_stylesheets_for_form($form);
  include_javascripts_for_form($form);
  echo $form->renderGlobalErrors();
  echo $form;
?>
  <ul class="pk-form-row submit">
  	<li><input type="submit" value="Save" class="pk-sub-submit"/></li>
  	<li><?php echo link_to('Cancel', "@$type", array("class" => "pk-sub-cancel")) ?></li>
  </ul>
<?php
  $s .= ob_get_clean();  
  return $s;
}

function _pk_sub_crud_form_info($form)
{
  $type = $form->type;
  $subtype = $form->subtype;
  $displayData = $type . '-' . $subtype;
  return array($type, $subtype, $displayData);
}

function pk_sub_crud_form_body($form)
{
  list($type, $subtype, $displayData) = _pk_sub_crud_form_info($form);
  ob_start();
  include_stylesheets_for_form($form);
  include_javascripts_for_form($form);
  echo $form;
?>
  <ul class="pk-form-row submit">
  	<li><input type="submit" value="Save" class="pk-sub-submit"/></li>
  	<li><?php echo link_to_function('Cancel', "$('#$displayData').html($('#$displayData').data('pk-form-swap')); $('#$type-form-edit-$subtype').show()", array("class" => "pk-sub-cancel")) ?></li>
  </ul>
<?php
  return ob_get_clean();
}
