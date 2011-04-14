(function($, REALISE){


	function bindAdd(){
		$('.addFoll').bind('click', function(){
			var selectedCount = $('.addFoll:checked').length;
			if(selectedCount == 0) {
				$('#addMembers').attr('disabled', 'disabled');
			} else {
				$('#addMembers').removeAttr('disabled');
			}


		});
		
		$('#addMembers').bind('click', function(){
			
			var users = [];
			
			$('.addFoll:checked').each(function(i, v){
				users.push(parseInt($(this).siblings('input[name="id"]').val()));
			});
			
			if(users.count == 0) return;
			
			$.post(window.location.pathname + "/addMembers", { "users": users }, function(data){
				if(data.status == 200){
					location.reload();			
				} else {
					alert(data.message);
				}
			
			}, 'json');
		});
	}


	REALISE.addLoadEvent(function(){
		bindAdd();
	});

})(jQuery, REALISE);