function ping() {
	// build url
	var url = "http://minetools.eu/query/".concat( document.getElementById("server").value, "/", document.getElementById("port").value );
	
	// update status
	document.getElementById("status").innerHTML = url;
	
	// get json string
	getJSONP( url, function(data){
		
	} );
	
	// parse json data into list
	
	// display list on html side
	
}
function getJSONP(url, success) {

    var ud = '_' + +new Date,
        script = document.createElement('script'),
        head = document.getElementsByTagName('head')[0] || document.documentElement;

    window[ud] = function(data) {
        head.removeChild(script);
        success && success(data);
    };

    script.src = url.replace('callback=?', 'callback=' + ud);
    head.appendChild(script);
}
