var json;

function ping() {
	// build url
	var server = encodeURIComponent( document.getElementById("server").value );
	var port = encodeURIComponent( document.getElementById("port").value );
	var url = "http://api.minetools.eu/query/".concat( server, "/", port );
	
	// update status
	document.getElementById("url").innerHTML = url;

	// get json data from server
	//var httpxml = new XMLHttpRequest({mozSystem: true});
	//httpxml.onload = setJson;
	/* So the problem is that the "Access-Control-Allow-Origin" header (CORS) is not enabled. This is a server-side issue, need a workaround. FML. */
	//httpxml.onerror = function() { alert('Error communicating with server.'); };
	//httpxml.open( "GET", url, false );
	//httpxml.send();
	//json = httpxml.responseText;
	
	$j.getJson( url, function() { alert('holy shitballs'); });
	var obj = JSON.parse( json );
	//document.getElementById("p1").innerHTML = obj.Playerlist;
	
	// display playerlist on html side
	
}

function getJson(data) {
	alert('should not hit this');
	json = data;
	document.getElementById("p2").innerHTML = json + "++";
}

function setJson() {
	document.getElementById("p1").innerHTML = this.responseText + "--";
}
