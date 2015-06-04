function ping() {
	// build url
	var url = "http://api.minetools.eu/query/".concat( document.getElementById("server").value, "/", document.getElementById("port").value );
	
	// update status
	document.getElementById("url").innerHTML = url;

	// jQuery is not defined, idk why, fml
	if (jQuery){
		jQuery.get( url+"?jsoncallback=?", function( data )
		{
			$("body")
			.append("Name: " + data.Map)
			.append("Time: " + data.Players);
		}, "json" );
	}
	else{ alert("jQuery is undefined."); }
	
	// parse json data into list
	
	// display list on html side
	
}