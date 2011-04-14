(function($, REALISE){

	function bindPost(){
		$('button#postCommentButton').bind('click', function(){
			$('#newCommentBox').attr('disabled', 'disabled');
			$('.loadingBar').fadeIn();
			
			$.post(window.location.pathname + "/comment", { 'body': $('#newCommentBox').val() }, function(data){
				if(data.status == 200){
					// OK, posted!
					var newComment = $(data.html);
					newComment.hide();
					
					$('#newComment').after(newComment);
					
					newComment.fadeIn();
					$('#newCommentBox').val("").removeAttr('disabled');
				} else {
					alert(data.message);
				}
				
				$('.loadingBar').hide();
			}, 'json');
		});
	
		$('.loadingBar').disableSelection();
	}

	function bindDelete(){
		$('.delete .deleteButton').bind('click', function(){
			var cfm = confirm("Are you sure you want to delete this comment? This action cannot be undone.");
			
			var comment = $(this).parentsUntil('.comment').parent();

			if(cfm){
				// Get the ID.
				var id = comment.find('input[name=id]').val();
				
				$.post(window.location.pathname + "/comment/" + id + "/delete", function(data){
					if(data.status == 200){
						// OK, deleted!
						
						comment.fadeOut('fast', function() { $(comment).remove(); } );
					} else {
						alert(data.message);
					}
					
				}, 'json');
			}
			
			return false;
		});
	}


	REALISE.addLoadEvent(function(){
		bindPost();
		
		bindDelete();
	});

})(jQuery, REALISE);

