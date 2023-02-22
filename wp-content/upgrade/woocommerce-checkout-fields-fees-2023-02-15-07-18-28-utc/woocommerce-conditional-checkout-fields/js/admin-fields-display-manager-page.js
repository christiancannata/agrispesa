"use strict";

jQuery(document).ready(function()
{
	jQuery('.column').sortable({
		connectWith: '.column',
		handle: 'h2',
		cursor: 'move',
		placeholder: 'placeholder',
		forcePlaceholderSize: true,
		opacity: 0.4,
		stop: function(event, ui)
		{
			
		}
	})
	.disableSelection(); 
});