<?php

use_helper('Form');
use_helper('jQuery');

// type is 'profile' or similar, the singular name used in the module and in CSS, not the full model class name

function pk_sub_crud_edit($label, $type, $sub, $object)
{
  $editButton = $type.'-form-edit-'.$sub;
  $displayData = $type.'-'.$sub;
  
  return jq_link_to_remote('edit', array(
    'url'      => '@'.$type.'_edit?id='.$object->getId(), 
    'method'   => 'get', 
    'update'   => $displayData, 
    'with'     => '"form='.$sub.'"', 
    'before'   => sprintf("$('#%s').data('pk-form-swap', $('#%s').html()); pkBusy(this)", $displayData, $displayData), 
  	'complete' => sprintf("pkReady('#%s'); $('#%s').hide()", $editButton, $editButton),
  ), array(
    'class' => 'pk-form-edit-button',
    'id' => $editButton
  )); 
}

function pk_sub_crud_cancel($label, $type)
{
  
}

function pk_sub_crud_form_tag()
{
  
}