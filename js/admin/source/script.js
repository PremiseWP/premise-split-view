(function($){

	$(document).ready(function() {

		var pwpsvEditor = null,
		modal = $( '#pwpsv-modal' ),
		modalOverlay = modal.find( '.pwpsv-modal-overlay' );

		$('.psv-cpt-ui').length > 0 ? psvToggleSelect() : false;

		$( '.pwpsv-edit-insert' ).click(function(){
			var side = ( 0 < $(this).parents( '.psv-ui-left' ).length ) ? 'left' : 'right';
			displayModal( side );
		});


		function psvToggleSelect() {
			$('#premise_split_view-left-type, #premise_split_view-right-type').change(function(){
				var type = $(this).val(),
				side = $(this).is( '#premise_split_view-left-type' ) ? 'left' : 'right';

				if ( 'Insert' == type ) {
					displayModal( side );
				}

				$(this).parents('.psv-cpt-ui').find('.psv-insert-content').removeClass( 'psv-content-active' );
				$(this).parents('.psv-cpt-ui').find('.psv-insert-'+type).addClass( 'psv-content-active' );

				// return false;
			});
		}


		function displayModal( side ) {
			var oldContent = $( '#premise_split_view-'+side+'-Insert' ).val();

			pwpsvEditor = tinymce.get('pwpsv_insert_editor');
			if ( pwpsvEditor && oldContent ) {
				pwpsvEditor.setContent( oldContent );
			}

			modal.fadeIn('fast');

			// confirm inserting content
			$( '#pwpsv-insert-content' ).off().click( insertContent );

			// cancel inserting content
			$( '#pwpsv-insert-cancel' ).off().click( closeModal );

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
				var content = '';
				pwpsvEditor = tinymce.get('pwpsv_insert_editor');
				if ( pwpsvEditor ) {
					content = pwpsvEditor.getContent();
				}
				else {
					content = modal.find('[name="pwpsv_insert_editor"]').val();
				}
				$( '#premise_split_view-'+side+'-Insert' ).val( content );
				closeModal();
			}

			// close the modal
			function closeModal() {
				modal.fadeOut( 'fast' );
				pwpsvEditor = tinymce.get('pwpsv_insert_editor');
				if ( pwpsvEditor ) {
					pwpsvEditor.setContent( '' );
				}
				else {
					modal.find('[name="pwpsv_insert_editor"]').val('');
				}
				return false;
			}
		}
	});


}(jQuery));