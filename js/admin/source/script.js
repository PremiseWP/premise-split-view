(function($){

	$(document).ready(function() {

		// Bind select
		$('.psv-cpt-ui').length > 0 ? psvToggleSelect() : false;

	});


	function psvToggleSelect() {
		$('.psv-cpt-ui select').change(function(){
			var type = $(this).val();

			$(this).parents('.psv-cpt-ui').find('.psv-insert-content').removeClass( 'psv-content-active' );
			$(this).parents('.psv-cpt-ui').find('.psv-insert-'+type).addClass( 'psv-content-active' );

			return false;
		});
	}

})(jQuery);