(function( $ ) {
	'use strict';

	$( document ).ready(function() {

		setTimeout(function() {
			if( typeof uploader == 'undefined' || uploader == null)
				return;
			uploader.bind('FileUploaded', function() {


				const slashPos = window.location.href.lastIndexOf('/');
				let newUrl = window.location.href.substr(0,slashPos);
				newUrl = newUrl.concat('/admin.php?page=arvancloud-vod-videos');
				let url = new URL(newUrl);
				url.searchParams.set('result', 'true');
				window.location.replace(url);
			});
		}, 1000);

	});

})( jQuery );

