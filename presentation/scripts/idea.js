(function($, REALISE){

	function bindVote(){
		$('a#vote').bind('click', function(){
			// Get the ID
			id = window.location.pathname.match(/([\d]+)$/);
			$.post(window.location.pathname + "/vote", { "idea": id[0]  }, function(data){
				if(data.status == 200){
					var current = $('.voteCount').html();
					current = parseInt(current);
					current += data.recalc;
					$('.voteCount').html(current);
					
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