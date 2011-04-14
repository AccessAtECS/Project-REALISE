(function($, REALISE){


	function bindDelete(){
		$('.delete .deleteButton').bind('click', function(){
			var cfm = confirm("Are you sure you want to delete this object? This action cannot be undone.");
			
			var object = $(this).parentsUntil('.idea').parent();

			if(cfm){
				// Get the ID.
				var id = object.find('input[name=id]').val();
				var type = object.find('input[name=type]').val();
				
				$.post("/admin/" + type + "/" + id + "/delete", function(data){
					if(data.status == 200){

						object.fadeOut('fast', function() { $(object).remove(); } );
					} else {
						alert(data.message);
					}
					
				}, 'json');
			}
			
			return false;
		});
	}


	REALISE.addLoadEvent(function(){
		bindDelete();
	});

})(jQuery, REALISE);