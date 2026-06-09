(function ($) {
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

	$(function () {


		/************* GENERAL FORM **************/
		$('#gen_settings_form').on('submit', function (e) {
			e.preventDefault();

			$('.statusMsg').removeClass('error');
			$('.statusMsg').removeClass('updated');

			const ppp = $('#gen_settings_form #ech_encyclopedia_ppp').val();

			let statusMsg = '';
			let validStatus = false;
			if (ppp == '') {
				validStatus = false;
				statusMsg += 'Post per page cannot be empty<br>';
			} else {
				validStatus = true;
			}
			// set error status msg
			if (!validStatus) {
				$('.statusMsg').html(statusMsg);
				$('.statusMsg').addClass('error');
				return;
			} else {
				$('#gen_settings_form').attr('action', 'options.php');
				$('#gen_settings_form')[0].submit();
				// output success msg
				statusMsg += 'Settings updated <br>';
				$('.statusMsg').html(statusMsg);
				$('.statusMsg').addClass('updated');
			}
		});
		/************* (END) GENERAL FORM **************/

		/************* COPY SAMPLE SHORTCODE **************/
		$('#copyShortcode').click(function () {
			const shortcode = $('#sample_shortcode').text();
			const tempInput = $('<input>');
			$('body').append(tempInput);
			tempInput.val(shortcode).select();
			try {
				const successful = document.execCommand('copy');
				if (successful) {
					$('#copyShortcode').html('Copied !');
				} else {
					$('#copyMsg').html('Copying failed, try again...');
				}
			} catch (err) {
				$('#copyMsg').html('Unable to copy, please try manually.');
			}
			tempInput.remove();
			setTimeout(function () {
				$('#copyShortcode').html('Copy Shortcode');
			}, 3000);
		});
		/************* (END)COPY SAMPLE SHORTCODE **************/

	}); // doc ready

})(jQuery);
