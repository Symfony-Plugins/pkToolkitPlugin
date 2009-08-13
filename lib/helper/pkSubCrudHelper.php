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

function pk_sub_crud_form_tag($form)
{
  list($type, $subtype, $displayData) = _pk_sub_crud_form_info($form);
  $oid = $form->getObject()->getId();

  $s = jq_form_remote_tag(array(
    'url' => "@$type" . "_update?id=$oid&form=$subtype", 
    'update' => $displayData, 
    'complete' => "$('#$displayData').html($('#$displayData').data('pk-form-swap')); $('#$type-form-edit-$subtype').show()"));

  $s .= '<input type="hidden" name="sf_method" value="PUT" />';
  $s .= pk_sub_crud_form_body($form);
  return $s;
}

function pk_sub_crud_form_body($form)
{
  list($type, $subtype, $displayData) = _pk_sub_crud_form_info($form);
  $s = '';
  ob_start();
  include_stylesheets_for_form($form);
  include_javascripts_for_form($form);
  echo $form->renderGlobalErrors();
  echo $form;
?>
  <ul class="pk-form-row submit">
  	<li><input type="submit" value="Save" class="pk-sub-submit"/></li>
  	<li><?php echo button_to_function('Cancel', "$('#$displayData').html($('#$displayData').data('pk-form-swap')); $('#$type-form-edit-$subtype').show()", array("class" => "pk-sub-cancel")) ?></li>
  </ul>
<?php
  return ob_get_clean();
}

function _pk_sub_crud_form_info($form)
{
  $type = $form->type;
  $subtype = $form->subtype;
  $displayData = $type . '-' . $subtype;
  return array($type, $subtype, $displayData);
}