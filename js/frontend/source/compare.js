/**
 * This code works but needs to be enqueued properly
 */
(function($){

	$(document).ready(function(){
		dragCompare();
	});

	function dragCompare() {
	    var container = $('.psv-compare-inner');
	    
	    if ( container.length > 0 ) {
	    	container.each(function(){
	    		var compare = $(this);
	    		compareThis(compare);
	    	});
	    }
	}


	function compareThis(container, makeDraggable, sectionsClickable) {
		container         = container         || null;
		makeDraggable     = 'boolean' === typeof makeDraggable     ? makeDraggable     : true;
		sectionsClickable = 'boolean' === typeof sectionsClickable ? sectionsClickable : false;

		// Check if the container exists first 
		if ( ! container ) {
			return false;
		}

		var front  = container.find('.psv-compare-front'),
	    handle     = container.find('.psv-compare-handle'),
	    content    = container.find('.psv-content'),
	    left       = handle.find('.psv-slide-left'),
	    right      = handle.find('.psv-slide-right'),
	    isResizing = false,
	    rightOffset = 10;

	    // set the content width to the same width of the container
	    content.width( container.width() );
	    $(window).resize( function(){
	    	content.width( container.width() );
	    });

	    left.click(slideRight);

	    right.click(slideLeft);

	    // Bind the handle behaviour based on the params submitted
	    // by default this is set to be drggable
	    if ( makeDraggable ) {
	    	doDraggable();
	    }
	    else {
	    	handle.click(slideCenter);
	    }

	    // if left and right sections shoudl be clickable 
		// Adds the class used to bind that event
		// Currently not supported if handle is draggable
		if ( sectionsClickable && ! makeDraggable ) {
			doClickableSections();
		}


	    function doDraggable() {
	    	handle.on('mousedown', function() {
		        // start resizing
		        $(this).addClass('psv-dragging');
		    	isResizing = true;
				$(document).on('mousemove', function (e) {
					// we don't want to do anything if we aren't resizing.
				    if (!isResizing) 
				        return;
				    var offsetRight = container.width() - (e.clientX - container.offset().left);

				    if ( offsetRight > rightOffset && offsetRight < ( container.width() - rightOffset ) ) {
				    	front.css('width', offsetRight);
				    }
				}).
				on('mouseup', function (e) {
				    // stop resizing
				    handle.removeClass('psv-dragging');
				    isResizing = false;
				    return false;
				});
		        return false;
		    });
		    return false;
	    }


	    function doClickableSections() {
	    	$('.psv-compare-left, .psv-compare-right').addClass('psv-compare-clickable');
	    
		    // bind comparing sections
		    // allows clicking on the left or right switch
		    $('.psv-compare-left.psv-compare-clickable').click(function() {
		    	slideRight();
		    	return false;
		    });
			$('.psv-compare-right.psv-compare-clickable').click(function() {
				slideLeft();
				return false;
			});
			return false;
	    }


	    function slideLeft() {
	    	front.removeClass('psv-slided-left');
	        front.addClass('psv-slided-right');
	        front.animate({
	        	width: rightOffset
	        }, 400);
	        return false;
	    }


	    function slideRight() {
	    	front.removeClass('psv-slided-right');
	        front.addClass('psv-slided-left');
	        var width = container.width() - rightOffset;
	        front.animate({
	        	width: width 
	        }, 400);
	        return false;
	    }


	    function slideCenter() {
	    	front.removeClass('psv-slided-left psv-slided-right');
	    	front.animate({
	        	width: '50%'
	        }, 400);
	        return false;
	    }
	}

})(jQuery);


/**
 * YouTube
 */
var PsvYouTubePlayer = {

	init: function($, YT) {
		// Begin YouTube Player when needed
		if ( $('.psv-youtube-video').length > 0 ) {
			var ytVideos = $('.psv-youtube-video');

			ytVideos.each(function(i,v){
				var videoId = $(this).attr('data-psv-video');

				new YT.Player($(this).attr('id'), {
					height: '100%',
					width: '100%',
					videoId: videoId,
					events: {
						'onReady': '',
						'onStateChange': ''
					}
				});
			});
		}
	}
}




function onYouTubeIframeAPIReady() {
	(function($){
		PsvYouTubePlayer.init($, YT);
	})(jQuery);
}