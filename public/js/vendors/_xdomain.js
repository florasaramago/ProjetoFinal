/* JQUERY START */
$().ready(function() {
	
	// CROSS DOMAIN - TESTING
	$.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?tags=cat&tagmode=any&format=json&jsoncallback=?", function(data){
		  $.each(data.items, function(i,item){
		    $("<img/>").attr("src", item.media.m).appendTo("#images")
		      .wrap("<a href='" + item.link + "'></a>");
		    if ( i == 3 ) return false;
		  });
		});
 
});