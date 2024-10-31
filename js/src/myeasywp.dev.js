/**
 * myeasywp.com
 * settings
 * 17 December 2012
 */
function toggleOptions(id) {
	var toggler=document.getElementById(id+'-toggler');
	var contents=document.getElementById(id+'-contents');
	if(toggler && contents) {

		if(contents.style.display=='none') {
			//open element
			contents.style.display='block';
			toggler.className='optionsGroup-toggler-close';
		}
		else {
			//close element
			contents.style.display='none';
			toggler.className='optionsGroup-toggler-open';
		}
	}
}

if(jQuery) {

	jQuery(document).ready(function() {

		jQuery('#signup').submit(function() {

			jQuery('#mc-response').html('Adding email address...');

			jQuery.ajax({

				url: location.protocol+'//'+location.hostname+'/wp-content/plugins/'+myeasyplugin+'/inc/mc/inc/store-address.php',
				data: 'ajax=true&email=' + escape(jQuery('#email').val()),
				success: function(msg) {
					jQuery('#mc-response').html(msg);
				}
			});

			return false;
		});
	});
}