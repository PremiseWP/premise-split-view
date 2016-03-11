(function($){

	$(document).ready(function() {

		// Bind select
		$('.psv-cpt-ui').length > 0 ? psvToggleSelect() : false;

	});


	function psvToggleSelect() {
		$('#premise_split_view-left-type, #premise_split_view-right-type').change(function(){
			var type = $(this).val();

			$(this).parents('.psv-cpt-ui').find('.psv-insert-content').removeClass( 'psv-content-active' );
			$(this).parents('.psv-cpt-ui').find('.psv-insert-'+type).addClass( 'psv-content-active' );

			return false;
		});
	}

})(jQuery);