<?php
// ADD TO Apache config:	AddType application/x-httpd-php .html

function ping() {
	$ip = "s.freecraft.eu";
	$port = "25565";

	$data = json_decode(file_get_contents('http://api.minetools.eu/ping/' . $ip . '/' . $port . ''), true);

	//var_dump($data);
	if(empty($data['error'])) { //only display data if nothing went wrong. 
		$version = $data['version']['name'];
		$online = $data['players']['online'];
		$max = $data['players']['max'];
		$motd = $data['description'];
		$favicon = $data['favicon'];

		echo 'Version: ' . $version;
		echo "<br>";
		echo 'Online User: ' . $online;
		echo "<br>";
		echo 'Max User: ' . $max;
		echo "<br>";
		echo 'MOTD: ' . $motd;
		echo "<br>";
		if(!empty($favicon)) { //is there a favicon? Then display it!
			echo 'Favicon: <img width="64" height="64" src="' . $favicon . '">';
		}
	} else { //err we have an error here :(
		echo $data['error'];
	}
}
?>
