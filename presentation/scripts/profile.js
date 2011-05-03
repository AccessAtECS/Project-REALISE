(function($, REALISE){

	var usernameInUse = false;
	var sameUsername = true;
	var defaultVal;

	function setupPage(){
		
		defaultVal = $('#username').val();
		$('#bio').ckeditor();
	
		$('#username').bind('blur', function(){
			
			if($(this).val() == "" || $(this).val() == defaultVal) return;
			
			$.post(window.location.pathname + '/usernameCheck', { username: $('#username').val() }, function(data){
				usernameInUse = data.usernameExists;
				sameUsername = data.sameUsername;
				
				if(usernameInUse && !sameUsername) {
					alert("This username is already in use, please choose another.");
					$('#username').val("").focus();
				}
			}, 'json');
		})
	}
	
	REALISE.addLoadEvent(function(){
		setupPage();
	});

})(jQuery, REALISE);