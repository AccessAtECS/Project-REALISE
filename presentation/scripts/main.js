function REALISE(){
	this.onLoadEvents = new Array();
	
	this.addLoadEvent = function(f){
		REALISE.onLoadEvents.push(f);
	}	
	
	this.init = function(){			
		// Run post-setup scripts
		for(i in REALISE.onLoadEvents){
			REALISE.onLoadEvents[i]();
		}	

		
		$('#loginShow').bind('click', function(){
			 $.get('/home/login', function(data){
			 	$('#loginForm').html(data);
			 	$('#username').focus();
			 });
		});
		
		$('#registerShow').bind('click', function(){
			 $.get('/home/register', function(data){
			 	$('#contentPane').html(data);
			 	$('#username').focus();
			 });
		});	
		
		
		$('.tip').tipsy();
	}
	
	$.fn.disableSelection = function() {
   		$(this).attr('unselectable', 'on')
           .css('-moz-user-select', 'none')
           .each(function() { 
               this.onselectstart = function() { return false; };
        });
	};
	
}

function bindOverview(){
	$('#overview').bind('keydown focusout', function(){
		var length = $(this).val().length;
		
		if(length >= 100){
			$(this).val($(this).val().substr(0, 100));
			length = $(this).val().length;
		}
		
		$('#currCharCount').html(length);
		
	});
	$('#overview').trigger('keydown');
}

var REALISE = new REALISE();

$(document).ready(function(){
	REALISE.init();
});