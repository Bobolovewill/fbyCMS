$(function() {
	'use strict';

	// Hide placeholder on form focus
	$('[placeholder]').focus(function() {
		$(this).attr('data-text', $(this).attr('placeholder'));
		$(this).attr('placeholder', '');
	}).blur(function() {
		$(this).attr('placeholder', $(this).attr('data-text'));
	});

	// Add asterisk in required fields
	$('input').each(function() {
		if($(this).attr('required') == 'required') {
			$(this).after('<span class="asterisk">*</span>')
		}
	});

	// Convert password field to text field on hover
	$('.show-pass').hover(function() {
		$('.password').attr('type', 'text');
	}, function() {
		$('.password').attr('type', 'password');
	});
});