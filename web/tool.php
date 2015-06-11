<!DOCTYPE html>
<html>
<head>
	<title>MineTool</title>

	<!-- scripts -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> <!--COLLISION -->
	<script src="js/bootstrap.js"></script>
	<script>$j = jQuery.noConflict(true)</script> <!-- fixed collision -->
	<script src="https://ajax.googleapis.com/ajax/libs/mootools/1.3.2/mootools.js"></script>
	<script src="js/circle.js"></script>
	<?php
	// TODO call this every time user clicks 'Ping' button, update below
		$SERVER_IP = "23.95.29.207"; //Insert the IP of the server you want to query. 
		$SERVER_PORT = "25565"; //Insert the PORT of the server you want to ping. Needed to get the favicon, motd, players online and players max. etc

		$SHOW_FAVICON = "on"; //"off" / "on"
		
		$ping = json_decode(file_get_contents('http://api.minetools.eu/ping/' . $SERVER_IP . '/' . $SERVER_PORT . ''), true);
		$query = json_decode(file_get_contents('http://api.minetools.eu/query/' . $SERVER_IP . '/' . $SERVER_PORT . ''), true);

		//Put the collected player information into an array for later use.
		if(empty($ping['error'])) { 
			$version = $ping['version']['name'];
			$online = $ping['players']['online'];
			$max = $ping['players']['max'];
			$motd = $ping['description'];
			$favicon = $ping['favicon'];
		}

		if(empty($query['error'])) {
			$playerlist = $query['Playerlist'];
		}
	?>
	

	<!-- styles -->
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/tab-content.css">
	<link rel="stylesheet" href="css/circle.css">
</head>

<body>
<noscript><strong>MineTool requires JavaScript to be turned on!</strong></noscript><br />
	<div id="content">
		<!-- Tab Layout -->
		<ul id="tabs" class="nav nav-tabs nav-justified" data-tabs="tabs">
			<li class="active"><a href="#player" data-toggle="tab">Server Info</a></li>
			<li><a href="#circle" data-toggle="tab">Circle Generator</a></li>
			<li><a href="#coords" data-toggle="tab">Guides</a></li>
		</ul>
		<!-- Tab Content -->
		<div id="tab-content" class="tab-content">
			<div class="tab-pane active" id="player">
				<!-- Player List -->
				<div id="url"></div>
				Server Address: <input type="text" id="server" value="23.95.29.207"> <br>
				Port Number:&nbsp&nbsp&nbsp <input type="number" id="port" min="1" max="99999" value="25565"> <br>
				<button type="button" id="ping" onclick="ping()">Ping</button> <!-- TODO HOW TO CALL PHP -->
				
				
				
				
				
				<div class="row">
					<div class="col-md-1">
						<h4><b>General Information</b></h4>
						<table class="table table-striped">
							<tbody>
								<tr>
									<td><b>IP</b></td>
									<td><?php echo $SERVER_IP . ':' . $SERVER_PORT; ?></td>
								</tr>
							<?php if(empty($ping['error'])) { ?>
								<tr>
									<td><b>Version</b></td>
									<td><?php echo $version; ?></td>
								</tr>
							<?php } ?>
							<?php if(empty($ping['error'])) { ?>
								<tr>
									<td><b>Players</b></td>
									<td><?php echo "".$online." / ".$max."";?></td>
								</tr>
							<?php } ?>
								<tr>
									<td><b>Status</b></td>
									<td><?php if(empty($ping['error'])) { echo "<i class=\"fa fa-check-circle\"></i><font color=\"#00DA00\"> Server is online</font>"; } else { echo "<i class=\"fa fa-times-circle\"></i><font color=\"#DA0000\"> Server is offline</font>";}?></td>
								</tr>
							<?php if(empty($ping['error'])) { ?>
							<?php if(!empty($favicon)) { ?>
								<tr>
									<td><b>Favicon</b></td>
									<td><img src='<?php echo $favicon; ?>' width="64px" height="64px" style="float:left;"/></td>
								</tr>
							<?php } ?>
							<?php } ?>
							</tbody>
						</table>
					</div>
					<div class="col-md-8" style="font-size:0px;">
						<h4><b>Players</b></h4>
						<?php
						$url = "https://cravatar.eu/helmavatar/";
						if(empty($query['error'])) {
							if($playerlist != "null") {
								$shown = "0";
								foreach ($playerlist as $player) {
									$shown++;
								?>
									<a data-placement="top" rel="tooltip" style="display: inline-block;" title="<?php echo $player;?>">
									<img src="<?php echo $url.$player;?>/50" size="40" width="40" height="40" style="width: 40px; height: 40px; margin-bottom: 5px; margin-right: 5px; border-radius: 3px; "/></a>
							<?php
								}
							}
							else { echo "<div class=\"alert alert-info\" style=\"font-size:12px;\"> No players online.</div>"; }
						}
						else { echo "<div class=\"alert alert-danger\" style=\"font-size:12px;\"> Query must be enabled in your server.properties file! <i class=\"fa fa-meh-o\"></i></div>"; }
							?>
					</div>
				</div>
				
				
				
				
				
				
				
				
				
				</ul>
			</div>
			<div class="tab-pane" id="circle">
				<!-- Circle Generator -->
				Width:&nbsp <input tabindex="1" type="number" size="5" id="diameter" value="8" max="2048" autocomplete="off" autofocus>
				Block Count: <span id="blockcount"></span><br/>
				Height: <input tabindex="2" type="number" size="5" id="height" value="8" max="2048" autocomplete="off">
				<select id="thickness" tabindex="3">
					<option selected="selected">thin</option>
					<option>thick</option>
					<option>filled</option>
				</select>
				<br/><br/>
				<div id="result"></div>
			</div>
			<div class="tab-pane" id="guides">
				<!-- Guides -->
				
			</div>
		</div>
	</div>
</body>
</html>