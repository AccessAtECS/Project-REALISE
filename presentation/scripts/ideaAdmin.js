(function($, REALISE){


	REALISE.addLoadEvent(function(){
		bindOverview();
		
		$('#description').ckeditor();
	});

})(jQuery, REALISE);