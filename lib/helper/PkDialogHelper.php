<?php

sfContext::getInstance()->getConfiguration()->loadHelpers(array('jQuery', 'I18N'));

// Opens a modal dialog using the CMS rolldown-from-top styles. 
// The dialog content is loaded via AJAX to avoid the performance
// impact of loading it all the time.

// The required id option is the DOM id of the dialog, but it is also
// the prefix for all other classes and IDs associated with it and referenced
// in the generated HTML/CSS/JavaScript, such as: .$id-button

// The title option is also required. It appears in title attributes
// and default button link text, following "View" or "Close".

// The action option, also required, is the Symfony URL to be loaded
// into the dialog element via AJAX.

// If the chadFrom option is set, the chad is positioned based on the
// location of the element matching that selector. The chad is 
// identified by the pk-chad class, found within the dialog's id.

// For examples seee the page settings form and the user profile settings form.

function pk_remote_dialog_toggle($options)
{
  if (!isset($options['id']))
  {
    throw new sfException("Required id option not passed to pk_dialog_toggle");
  }
  if (!isset($options['title']))
  {
    throw new sfException("Required title option not passed to pk_dialog_toggle");
  }
  if (!isset($options['action']))
  {
    throw new sfException("Required action option not passed to pk_dialog_toggle");
  }
  $id = $options['id'];
  $title = $options['title'];
  $action = $options['action'];
  if (isset($options['chadFrom']))
  {
    $chadFrom = $options['chadFrom'];
  }
  if (isset($options['loading']))
  {
    $loading = $options['loading'];
  }
  $s = '';
  $s .= jq_link_to_remote(__("View $title"), 
    array(
      "url" => $action,
      "update" => $id,
      "script" => true,
  		"before" => "$('.$id-button.open').hide();
  								 $('.$id-loading').show();", 
      "complete" => "$('#$id').fadeIn();
  									 $('.$id-loading').hide();
  									 $('#$id-button-close').show();" .
  									 (isset($chadFrom) ?
      							   "var arrowPosition = parseInt($('$chadFrom').offset().left);
      								 $('#$id .pk-chad').css('left',arrowPosition+'px'); alert('hello'); /*This doesn't work Tom!*/"
      								 : "") . "
  									 pkUI('#$id');
  									$('.pk-page-overlay').show();",
    ), array(
  		'class' => "$id-button open", 
  		'id' => "$id-button-open", 
  		'title'=> __("View $title")));
  $s .= jq_link_to_function(__("Close $title"), 
		"$('#$id-button-close').hide(); 
		 $('#$id-button-open').show(); 
		 $('#$id').hide();
		 $('.pk-page-overlay').hide();", 
		 array(
			'class' => "$id-button close", 
			'id' => "$id-button-close",  
			'title' => __("Close $title")));
	if (isset($loading))
	{
  	$s .= image_tag($loading,
  	  array('class' => "$id-loading", 'style' => 'display:none;',  ));
  }
  return $s;
}
