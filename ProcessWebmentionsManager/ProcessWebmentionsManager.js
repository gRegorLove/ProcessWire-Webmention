
	jQuery(document).ready(function(){

		jQuery('.show-advanced').click(function(e) {
			e.preventDefault();

			$(this).next().toggle();
		});

	});
