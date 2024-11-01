(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready( function(){
		$('#zwkof_add_filter').on('click', function(){
			$('.zwkof_special_order_filter').slideToggle( "400", function() {
				if ( $('.zwkof_special_order_filter').is(':visible') ) {
					document.cookie = "zwkof_special_order_filter=opened";
				} else {
					document.cookie = "zwkof_special_order_filter=closed";
				}
			});
		});

		$('#filter_clear').on('click', function() {
			$.each($('.zwkof_special_order_filter input, .zwkof_special_order_filter select'), function (k, v) {
				var type = $(v).attr('type');
				if (type == 'text' || type == 'email' || type == 'number') {
					$(v).val('');
				}
				if (type == null || $(v).prop('tagName') == 'SELECT') {
					$(v).val('');
				}
			});
		});
	});


})( jQuery );
