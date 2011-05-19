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
		
		$('.delete .deleteButton').bind('click', function(){
			
			// Get number of innovators
			if($('.innovator').length == 1){
				alert("There must be at least one member in the team.");
				return;
			}
			
			var cfm = confirm("Are you sure you want to remove this user as a team member?");
			
			var user_id = $(this).parentsUntil('.innovator').parent().find('input[name="id"]').val();
			
			$.post(window.location.pathname + "/demoteMember", { "user_id": user_id }, function(data){
				if(data.status == 200){
					location.reload();			
				} else {
					alert(data.message);
				}
			
			}, 'json');
		});
		
		$('.mentor').attr('title', 'Make this user a mentor of this project').wrap($('<a>', { href: '#', click: function(){ 
			var holder = $(this).parentsUntil('.innovator').parent();
			var id = holder.find('input[name=id]').val();
			
			var URL = location.pathname + "/mentor/" + id;
			
			$.post(URL, function(data){
				if(data.status == 200){
					location.reload();			
				} else {
					alert(data.message);
				}
			
			}, 'json');
		} }));
		
	}


	REALISE.addLoadEvent(function(){
		bindAdd();
	});



})(jQuery, REALISE);