function twitter(){

	this.account = "";
	
	this.setAccount = function(name){
		this.account = name;
	}

	this.get = function(callback){
		o = this;
		try {
			$.getJSON('http://twitter.com/status/user_timeline/' + this.account + '.json?count=5&callback=?', function(data){
				// Container object.	
				var newObj = $("<div>", { class: 'tweetJar' });
				
				// Format the tweets
				$.each(data, function(){
					$("<div>", { 'class': 'tweetMessage' })
						.append( o.formatTweet(this.text), $("<span>", { 'class': 'tweetTime', text: o.formatDate(this.created_at) }) )
						.appendTo(newObj);
				});
				
				callback(newObj);
			});
		
		} catch(e){
			return false;
		}
	}
	
	this.formatDate = function(dateString){
		now = new Date();
		d 	= new Date(dateString);
		
		min 	= 1000 * 60;
		hr 		= 1000 * 60 * 60;
		day 	= 1000 * 60 * 60 * 24;
		
		diff = now.getTime() - d.getTime();
		
		day_diff 	= Math.floor(diff / day);
		hr_diff 	= Math.floor(diff / hr);
		min_diff 	= Math.floor(diff / min);
			
		if(day_diff > 0){
			return "about " + day_diff + ((day_diff == 1) ? " day ago" : " days ago");
		} else if(hr_diff > 0) {
			return "about " + hr_diff + ((hr_diff == 1) ? " hour ago " : " hours ago");
		} else if(min_diff > 0){
			return "about " + min_diff + ((min_diff == 1) ? " minute ago" : " minutes ago");
		} else {
			return "seconds ago";
		}
	}
	
	this.formatTweet = function(html){
		// Links
		html = html.replace(/(https?:\/\/[a-zA-Z0-9_\-%\.\/]+)/gi, '<a href="$1" target="_blank">$1</a>');	
		// Hashtags
		html = html.replace(/(#\S+)/gi, '<a href="http://twitter.com/search?q=$1" target="_blank">$1</a>');	
		// Users
		html = html.replace(/@(\S+)/gi, '<a href="http://twitter.com/$1" target="_blank">@$1</a>');
		return html + " ";
	}
}