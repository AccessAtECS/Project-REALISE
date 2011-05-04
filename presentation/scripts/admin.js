(function($, REALISE){


	function bindDelete(){
		$('.delete .deleteButton, .delete .enableButton').bind('click', function(){
			var action;
			if($(this).hasClass('enableButton')){
				action = "enable";
				var cfm = confirm("Are you sure you want to make this object visible?");
			} else {
				action = "disable";
				var cfm = confirm("Are you sure you want to hide this object?");
			}
			var object = $(this).parentsUntil('.idea').parent();

			if(cfm){
				// Get the ID.
				var id = object.find('input[name=id]').val();
				var type = object.find('input[name=type]').val();
				
				$.post("/admin/" + type + "/" + id + "/hide", { "action": action }, function(data){
					if(data.status == 200){
						if(action == "disable") {
							object.fadeOut('fast', function() { $(object).remove(); } );
						} else {
							location.reload(true);
						}
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