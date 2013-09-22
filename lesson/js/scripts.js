// page init
jQuery(function(){
	initFixLayout();
});
function initFixLayout(){
	jQuery('ul.news li:nth-child(2n+1)').addClass('even');
};


$.fn.equalHeights = function(px, selector) {
	$(this).each(function(){
		var currentTallest = 0;
		$(this).find('.'+selector).each(function(i){
			$(this).height('auto');
			if ($(this).height() > currentTallest) { currentTallest = $(this).height(); }
		});
	    if (!px && Number.prototype.pxToEm) currentTallest = currentTallest.pxToEm(); //use ems unless px is specified
		$(this).find('.'+selector).css({'height': currentTallest}); 
	});
	return this;
};

(function($) {
	$('body').addClass('js');
	$('.carousel').carousel({
		interval: 0
	});
	
	$('#about-us .carousel-inner').equalHeights('px', 'item');
	$('#why-we .carousel-inner').equalHeights('px', 'element');
	$(window).resize(function(){
		$('#about-us .carousel-inner').equalHeights('px', 'item');
		$('#why-we .carousel-inner').equalHeights('px', 'element');
	});

	/*---------------------------------- объявления -----------------------------------*/
	$('.ads-form input:checked').parent().parent().addClass('active');
	$('.ads-form input[type="checkbox"]').change(function(e){
		if ($(this).is(":checked")){
			$(this).parent().parent().addClass('active');
		} else {
			$(this).parent().parent().removeClass('active');
		};
	});
	$('.ads-form tbody tr').click(function(){
		if ($('input[type="checkbox"]', this).is(":checked")){
			$('input[type="checkbox"]', this).attr('checked', false);
			$(this).removeClass('active');
		} else{
			$('input[type="checkbox"]', this).attr('checked', true);
			$(this).addClass('active');
		};
	});
	$('.ads-form a').click(function(e){
		e.stopPropagation();
	});

	/*---------------------------------- inc / dec -----------------------------------*/
	$("span.increment-input").append('<span class="inc btn btn-mini">+</span><span class="dec btn btn-mini">-</span>');
	$(".inc, .dec").click(function() {
	    var $button = $(this);
	    var oldValue = $button.parent().find("input").val();

	    if ($button.text() == "+") {
		  var newVal = parseFloat(oldValue) + 1;
		} else {
		  if (oldValue >= 2	) {
		      var newVal = parseFloat(oldValue) - 1;
		  } else {
		  	var newVal = parseFloat(oldValue);
		  }
		}
		$button.parent().find("input").val(newVal);
	});

})(jQuery);