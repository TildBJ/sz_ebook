jQuery(document).ready(function() {
	setTimeout(function() {
		var sel = jQuery('.next-button, .previous-button');
		var ePaperHeight = jQuery('.page-wrapper').height();

		sel.css({
			height: ePaperHeight,
			backgroundPosition: '-38px '+(ePaperHeight/2-32/2)+'px'
		});
	}, 500);
});