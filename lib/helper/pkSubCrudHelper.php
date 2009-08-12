<?php

use_helper('Form');
use_helper('jQuery');

// type is 'profile' or similar, the singular name used in the module and in CSS, not the full model class name

function pk_sub_crud_edit($label, $type, $sub, $object)
{
  $selector = "#$type-form-edit-$sub";
  return jq_link_to_remote('edit', array(
    'url' => "@$type" . "_edit?id=".$object->getId(), 
    'method' => 'get', 
    'update' => "$type-$sub", 
    'with' => "\"form=$sub\"", 
  	'before' => "$('#$type-$sub').data('pk-form-swap', $('#$type-$sub').html()); pkBusy(this)",
  	'complete' => "pkReady('$selector'); $('$selector').hide()"
    ), array('class' => 'pk-form-edit-button', 'id' => "$type-form-edit-$sub")); 
}
