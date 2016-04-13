jQuery(window).load(function() {
	jQuery(".site-loader-icon").fadeOut("slow");;
});

jQuery(document).ready(function() {
	jQuery(window).scroll(function() {
		if (jQuery(this).scrollTop() > 50){  
			jQuery('header#masthead').addClass("sticky");
	  	}
	  	else{
			jQuery('header#masthead').removeClass("sticky");
		}
	});
	
	
	
});


//Menu Section
jQuery(function() {
	var pull    = jQuery('.menu-toggle');
	menu    = jQuery('.menu-main-menu-container > ul');
	//submenu     = $('.menu-grid > ul > li > ul');

	jQuery(pull).on('click', function(e) {
		e.preventDefault();
		menu.slideToggle();
		pull.toggleClass('is-open');
	});    

});
//MENU Section

