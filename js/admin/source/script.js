(function($){

	$(document).ready(function() {

		var modal = $( '#psview-modal' ),
		modalOverlay = modal.find( '.psview-modal-overlay' );

		$('.psv-cpt-ui').length > 0 ? psvToggleSelect() : false;

		$( '.psview-edit-insert' ).click(function(){
			var side = ( 0 < $(this).parents( '.psv-ui-left' ).length ) ? 'left' : 'right';
			displayModal( side );
		})


		function psvToggleSelect() {
			$('#premise_split_view-left-type, #premise_split_view-right-type').change(function(){
				var type = $(this).val(),
				side = $(this).is( '#premise_split_view-left-type' ) ? 'left' : 'right';

				if ( 'Insert' == type ) {
					displayModal( side );
				}

				$(this).parents('.psv-cpt-ui').find('.psv-insert-content').removeClass( 'psv-content-active' );
				$(this).parents('.psv-cpt-ui').find('.psv-insert-'+type).addClass( 'psv-content-active' );

				return false;
			});
		}


		function displayModal( side ) {
			var psviewEditor = tinyMCE.get('psview_insert_editor'),
			oldContent       = $( '#premise_split_view-'+side+'-Insert' ).val();

			modal.fadeIn('fast');
			psviewEditor.setContent( oldContent );

			// confirm inserting content
			$( '#psview-insert-content' ).off().click( insertContent );

			// cancel inserting content
			$( '#psview-insert-cancel' ).off().click( closeModal );
			// modalOverlay.off().click( function(e) {
			// 	e.stopPropagation();
			// 	closeModal();
			// } );

			return false;

			/*
				Private methods
			 */

			// Insert the content to the corresponding side
			function insertContent() {
				var content = psviewEditor.getContent();
				$( '#premise_split_view-'+side+'-Insert' ).val( content );
				closeModal();
			}

			// close the modal
			function closeModal() {
				modal.fadeOut( 'fast' );
				psviewEditor.setContent( '' );
				return false;
			}
		}
	});


}(jQuery));