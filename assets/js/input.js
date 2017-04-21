(function($){

	function initialize_field( $el ) {
		// do stuff
	}

	if( typeof acf.add_action !== 'undefined' ) {

		/**
		 *  ready append (ACF5)
		 */
		acf.add_action('ready append', function( $el ){
			acf.get_fields({ type : 'acf-taxonomy-select'}, $el).each(function(){
				initialize_field( $(this) );
			});
		});
	}
	else {

		/**
		 *  acf/setup_fields (ACF4)
		 */
		$(document).live('acf/setup_fields', function(e, postbox){
			$(postbox).find('.field[data-field_type="acf-taxonomy-select"]').each(function(){
				initialize_field( $(this) );
			});
		});
	}

})(jQuery);
