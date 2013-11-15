// page init
/*! Copyright 2012, Ben Lin (http://dreamerslab.com/)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 1.0.14
 *
 * Requires: jQuery 1.2.3 ~ 1.9.0
 */
;( function ( $ ){
  $.fn.extend({
    actual : function ( method, options ){
      // check if the jQuery method exist
      if( !this[ method ]){
        throw '$.actual => The jQuery method "' + method + '" you called does not exist';
      }

      var defaults = {
        absolute      : false,
        clone         : false,
        includeMargin : false
      };

      var configs = $.extend( defaults, options );

      var $target = this.eq( 0 );
      var fix, restore;

      if( configs.clone === true ){
        fix = function (){
          var style = 'position: absolute !important; top: -1000 !important; ';

          // this is useful with css3pie
          $target = $target.
            clone().
            attr( 'style', style ).
            appendTo( 'body' );
        };

        restore = function (){
          // remove DOM element after getting the width
          $target.remove();
        };
      }else{
        var tmp   = [];
        var style = '';
        var $hidden;

        fix = function (){
          // get all hidden parents
          if ($.fn.jquery >= "1.8.0")
            $hidden = $target.parents().addBack().filter( ':hidden' );
          else
            $hidden = $target.parents().andSelf().filter( ':hidden' );

          style += 'visibility: hidden !important; display: block !important; ';

          if( configs.absolute === true ) style += 'position: absolute !important; ';

          // save the origin style props
          // set the hidden el css to be got the actual value later
          $hidden.each( function (){
            var $this = $( this );

            // Save original style. If no style was set, attr() returns undefined
            tmp.push( $this.attr( 'style' ));
            $this.attr( 'style', style );
          });
        };

        restore = function (){
          // restore origin style values
          $hidden.each( function ( i ){
            var $this = $( this );
            var _tmp  = tmp[ i ];

            if( _tmp === undefined ){
              $this.removeAttr( 'style' );
            }else{
              $this.attr( 'style', _tmp );
            }
          });
        };
      }

      fix();
      // get the actual value with user specific methed
      // it can be 'width', 'height', 'outerWidth', 'innerWidth'... etc
      // configs.includeMargin only works for 'outerWidth' and 'outerHeight'
      var actual = /(outer)/g.test( method ) ?
        $target[ method ]( configs.includeMargin ) :
        $target[ method ]();

      restore();
      // IMPORTANT, this plugin only return the value of the first element
      return actual;
    }
  });
})( jQuery );



(function($){

	$.fn.tabler = function(options) {

	    var settings = $.extend({side:'left'}, options);

		var tab_control = this.find('.tabset li a');
		var tabs_collection = this.find('.tab-content');
		var btn_close = this.find('.tab-content .tab-btn .close');
		var btn_overlay = this.find('.tab-content .tab-btn .overlay');

        btn_close.click(function(){
        	//if(btn_overlay.hasClass('push')){btn_overlay.click();}
        	$(this).toggleClass('closed');
        	tab_control.removeClass('active');
        	tabs_collection.find('.tab').removeClass('active');
        	tabs_collection.find('.tab').css('display', 'none').css('margin-left', '-300px');

        	if(!$('.main').hasClass('pushed-'+settings.side)&&btn_overlay.hasClass('push')){$('.main').addClass('pushed-'+settings.side);}
        	else{$('.main').removeClass('pushed-'+settings.side);}

        	return false;
        });

        btn_overlay.click(function(){
        	$(this).toggleClass('push');
        	if(!$('.main').hasClass('pushed-'+settings.side)){$('.main').addClass('pushed-'+settings.side);}
        	else{$('.main').removeClass('pushed-'+settings.side);}
        	return false;
        });

		tab_control.each(function(){
			$(this).click(function(){
                var tab_id = $(this).attr('href');

                if (!$(tab_id).hasClass('active'))
                {

	                    tab_control.removeClass('active');
	                    $(this).addClass('active');
	                    tabs_collection.find('.tab').removeClass('active');

		                if(btn_overlay.hasClass('push'))
		                {
	                       $('.main').addClass('pushed-'+settings.side);
	                       tabs_collection.find('.tab').css('display', 'none').css('margin-left', '-300px');
	                       $(tab_id).css('display', 'block').addClass('active').css('margin-left', '0px');
		                }
		                else
		                {
						    tabs_collection.find('.tab').css('display', 'none').css('margin-left', '-300px');
							$(tab_id).css('display', 'block').animate({marginLeft: "0px"}, "slow").addClass('active');
		                }
                }
                else{btn_close.click();}
				return false;
			});
		});



		//return this;
	};

})(jQuery);

jQuery(function(){
	initContentHeight();
	$('.play').each(function(){
		$(this).click(function(){
			var offset = $(this).offset();
			$(".play-box").offset({ top: offset.top, left: offset.left });
		});
	});

	$(document).mouseup(function (e) {
	    var container = $(".play-box");
	    if (container.has(e.target).length === 0){
	        container.offset({ top: '0', left: '-9999' });
	    }
	    e.preventDefault();
	});

	$(document).mouseup(function (e) {

		 if($(e.target).attr('type')!='search'&&$(e.target).attr('type')!='submit'){
		 	$("input[type=search]").fadeOut(100);
		 }
	     e.preventDefault();
	});

	$('.jqm-navmenu-link').click(function(){
		$(this).toggleClass('clicked');
		$('.left-box').toggleClass('hide');
		$('.right-slider').toggleClass('hide');
		$('.main').toggleClass('fullscreen');
		return false;
	});

    $('.left-box').tabler();
    $('.right-box').tabler({side:'right'});

    $('.form-search input[type=search]').hide();
    $('.form-search input[type=submit]').mouseover(function(){$('.form-search input[type=search]').fadeIn(600).focus();});


	$('.subheaders li a').live('click',function(e){
		e.preventDefault();
		$('.container-scroll-box').scrollTop(parseInt($('.container-scroll-box a[name="'+$(this).attr('href').slice(1)+'"]').offset().top-$('.container-scroll-box-holder').offset().top))
	})

    initContentHeight();


});

// content height init
function initContentHeight(){
	var win = $(window);
	var header = $('.header');
	var main = $('.container-scroll-box');
	var tab_holder = $('.tab-scroll');
	var subnavfixedtop = $('.subnav-fixed-frame');
	var subnavfixedbottom = $('.subnav-fixed-frame');
	var footer = $('.footer');
	var timer;
	function handleResize(){
		main.css({
			height: win.height() - header.outerHeight(true) - footer.outerHeight(true) - subnavfixedtop.outerHeight(true) - subnavfixedbottom.outerHeight(true)
		});
		tab_holder.each(function(){
			if($(this).parents('.tab').find('.tab-preambula').length){
			    $(this).css({
     				height: win.height() - footer.outerHeight(true) - 131 - $(this).parents('.tab').find('.tab-preambula').actual('height')
    			})
   			}
   			else{
    			$(this).css({
     				height: win.height() - footer.outerHeight(true) - 95
    			})
   			}
		});

		$(".play-box").offset({
			top: '0',
			left: '-9999'
		});
		clearTimeout(timer);
		timer = setTimeout(function(){
			if(win.width()<1003 && !$('.jqm-navmenu-link').hasClass('clicked')){
				$('.jqm-navmenu-link').click();
			}
			if(win.width()>1003 && $('.jqm-navmenu-link').hasClass('clicked')){
				$('.jqm-navmenu-link').click();
			}
			console.log(getWindowMode());
		}, 50);
	}

	handleResize();

	win.resize(handleResize);
}

function getWindowMode() {
	var win = $(window);
	if (win.width() < 480) {
		return 'Mobile';
	} else if (win.width() < 1003) {
		return 'iPad';
	}
	return 'Desktop';
}