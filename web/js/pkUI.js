/* Init pk controls */

function init_pk_controls(target, instance)
{

	// TARGETING THING I'M TRYING OUT
	// this is super rough, but the idea is being able to scope the UI initialization for Ajax calls etc.

	if (typeof target == 'undefined') // If Not Set
	{
		target = '';
	}
	else if (typeof target == 'object') // If jQuery object
	{
		target = "#"+$(target).attr('id')+" ";
	}
	else // probably a string
	{
		target = target+" ";
	}

	if (typeof instance == 'undefined') // If Not Set
	{
		instance = null;
	}
	
	//
	// TARGETTED CONTROLS
	//

	if (target != '') // Only execute code in here if there's a target set
	{

	}
	
	var addSlotButton = $(target+'ul.pk-area-controls a.pk-add.slot');
	
	if (addSlotButton.hasClass('addslot-now')) // init_pk_controls was resetting add slot buttons in some scenarios when we didn't want it to	
	{ 
		addSlotButton.prev().css('display','none');
	} 
	else
	{
		addSlotButton.siblings('.pk-area-options').css('display','none');
	}
	
	//
	// GLOBAL CONTROLS
	//
	$('.pk-i').remove();
	$.each($('.pk-btn'), function() { // inject extra markup for link styles
		txt = $(this).text();
		$(this).html("<span class='pk-i'></span><span class='pk-b'>"+txt+"</span>");
   });
	
	$('.pk-submit').before("<span class='pk-i'></span>"); // since input's cant contain the background, they go before it with a wrapper around both to contain it

	$('.pk-i').fadeTo(0,.65); //crossbrowser opacity
	$('.pk-page-overlay').fadeTo(0,.85);
	//add 'last' class to last option
	$('.pk-controls li:last-child').addClass('last'); 
	
	$('.pk-area-controls .pk-controls-item').siblings(':not(.cancel)').css('display', 'block');
	$('.pk-area-controls .pk-controls-item').children('.pk-btn').css('display', 'block');
	// $('.pk-area-controls ul, .pk-area-controls a.pk-cancel').parent().css('display', 'none');

	//
	// INSTANCE CONTROLS
	//

	if (instance == 'history-preview') 
	{ // if we are refreshing while using the history browser we need to set some parameters
		$(target+".pk-controls-item").siblings().show();
		$(target+".pk-controls-item").siblings('.slot').hide();
		$(target+".pk-controls-item").siblings('.edit').hide();
		
	};
	
	if (instance == 'history-revert') 
	{ // after clicking 'save as current revision'
		$('.pk-history-browser, .pk-history-preview-notice, .pk-page-overlay').css('display','none');		
		$(target+".pk-controls-item").siblings().show();
		$(target+".pk-controls-item").siblings('.cancel').hide();
		$(target).removeClass('browsing-history');
		$(target).removeClass('previewing-history');
		if ($(target).hasClass('singleton')) // remove instances of pk-slot-controls for singleton areas
		{
			$(target +" .pk-slot-controls").remove();					
		}
	};
	
	if (instance == 'history-cancel') 
	{ // clicking cancel after previewing history item
		$('.pk-history-browser, .pk-history-preview-notice, .pk-page-overlay').css('display','none');				
		$(target).removeClass('browsing-history');
		$(target).removeClass('previewing-history');
		if ($(target).hasClass('singleton')) // remove instances of pk-slot-controls for singleton areas
		{
			$(target +" .pk-slot-controls").remove();					
		}
	};

	//
	// PK-CONTROLS BUTTON EVENTS
	//

	$('a.pk-add.slot').click(function(event){
		event.preventDefault();
		$(this).hide(); //HIDE SELF
		$(this).prev('.pk-i').hide(); //HIDE SELF BG
		$(this).siblings('.pk-area-options.slot').fadeIn(); //SHOW AREA OPTIONS FOR SLOTS
		$(this).parent().siblings(':not(.cancel)').hide(); //HIDE OTHER OPTION CHILD LINKS
		$(this).parent().addClass('addslot-now');
		$(this).parent().siblings('.pk-controls-item.cancel').show().addClass('cancel-addslot'); //SHOW CANCEL BUTTON
	});
	
	$('a.pk-history').click(function(event){

		event.preventDefault();	
			
		$('.pk-history-browser').hide();
		$('a.pk-history').parents('.pk-area').removeClass('browsing-history');
		$('a.pk-history').parents('.pk-area').removeClass('previewing-history');
		$('.pk-page-overlay').show();
		
		if (!$(this).parents('.pk-area').hasClass('browsing-history')) 
		{
			//clear history and show the animator
			$('.pk-history-browser .pk-history-items').html('<tr class="pk-history-item"><td class="date"><img src="\/images\/pk-icon-loader-ani.gif"><\/td><td class="editor"><\/td><td class="preview"><\/td><\/tr>');
			//tell the area that we're browsing history
			$(this).parents('.pk-area').addClass('browsing-history');
		}
				
		var y1 = .49, y2 = $(this).offset().top;

		if (parseInt(y1 + y2) > parseInt(y2)) { y2 = parseInt(y1 + y2);	} else { y2 = parseInt(y2); } 

		$('.pk-history-browser').css('top',(y2+20)+"px"); //21 = height of buttons plus one margin
		$('.pk-history-browser').fadeIn();

		$(this).parent().siblings(':not(.cancel)').hide(); //HIDE OTHER OPTION CHILD LINKS
		$(this).parents('.pk-controls').find('.cancel').show().addClass('cancel-history'); //SHOW CANCEL BUTTON And Scope it to History

	});
	
	$('a.pk-cancel').click(function(event){

		$(this).parents('.pk-controls').children().show();
		$(this).parents('.pk-controls').find('.pk-area-options').hide();		
		$(this).parent().hide(); //hide parent <li>

		if ($(this).parent().hasClass('cancel-history')) //history specific events
		{
			$(this).parents('.pk-controls').find('.pk-history-options').hide();
			$(this).parents('.pk-controls').find('a.pk-history').show();
			$(this).parents('.pk-controls').find('a.pk-history').prev('.pk-i').show();
			$(this).parent().removeClass('cancel-history');
			$('.pk-history-browser, .pk-history-preview-notice, .pk-page-overlay').css('display','none');		
			$(this).parents('.pk-area').removeClass('browsing-history');
			$(this).parents('.pk-area').removeClass('previewing-history');
		}
		
		if ($(this).parent().hasClass('cancel-addslot')) //add slot specific events
		{
			$(this).parents('.pk-controls').find('a.pk-add.slot').show();
			$(this).parents('.pk-controls').find('a.pk-add.slot').prev('.pk-i').show();
			$('.addslot-now').removeClass('addslot-now');
			$(this).parent().removeClass('cancel-addslot');			
		}

		if ($(this).hasClass('event-default')) 
		{ //allow default event
			
			$(this).parent().show(); //unhide cancel button
			
		}
		else
		{
			//prevent default event
			event.preventDefault();
		}
		
	});

	$('.pk-controls').css('visibility','visible'); //show them after everything is loaded
	
}

$(document).ready(function(){

	init_pk_controls();
		
	/*
	experimental awesomeness - toggle pk-controls on modifier
	*/
	

	$(document).bind('keydown', "ctrl+return", function() {
		$('body').toggleClass("pk-preview");
		$('.pk-controls').toggle();
		$('#pk-global-toolbar').toggle();
	});

});