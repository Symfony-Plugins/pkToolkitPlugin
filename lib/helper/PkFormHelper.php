<?php

use_helper('Form');

// Symfony contains an unfortunate "fix" to enable the old
// Symfony 1.0 fillin helper to see the linked hidden field as a
// type="text" field. This breaks any text with newlines in it in
// Safari and Chrome. Un-fix the fix until this gets corrected
// in an official Symfony 1.2 release. 
// http://trac.symfony-project.com/ticket/732

function pk_textarea_tag($name, $value, $options)
{
  $result = textarea_tag($name, $value, $options);
  if (isset($options['rich']) && $options['rich'])
  {
    $result = preg_replace('/type="text"/', 'type="hidden"',
      $result, 1);
  }
  return $result;
}

