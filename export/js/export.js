// JavaScript Document

jQuery(document).ready(function ($) { 
	$("body.post-type-post:not(.post-new-php) .wrap h1").append('<div class="page-title-action exportpost">Export</div>');	
	$('.exportpost').click(function(e) {
		my_posts();
	});

});


function my_posts(){
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: {
			'action': 'my_posts',
		},
		beforeSend: function(){
		},
		success: function(res){ 
			window.open(res, '_blank');
		}
	});	
}