(function($, REALISE){

	function bindVote(){
		$('a#vote').bind('click', function(){
			// Get the ID
			id = window.location.pathname.match(/([\d]+)$/);
			$.post(window.location.pathname + "/vote", { "project": id[0]  }, function(data){
				if(data.status == 200){
					var current = $('.follower_title').html();
					current = parseInt(current);
					current += data.recalc;
					$('.follower_title').html(current);
					
					if(data.action == "follow"){
						// Get the user's profile image.
						var user_image = $('#user').find('img').clone();
						$(user_image).height('50px').width('50px').removeAttr('align').removeAttr('alt').addClass('roundItem follower').hide();
						
						$('#followers').append($(user_image).fadeIn());
					} else {
						var user_image_src = $('#user').find('img').attr('src');
						
						$('#followers').find('img[src="' + user_image_src + '"]').fadeOut('fast', function(){
							$(this).remove();
						})
					}
					
					$('#voteImage').attr('src', data.image);				
				} else {
					alert(data.message);
				}
			}, 'json');
		
		});
	}

	
	REALISE.addLoadEvent(function(){
		bindVote();
	});

})(jQuery, REALISE);