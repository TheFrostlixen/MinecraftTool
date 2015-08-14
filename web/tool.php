<!DOCTYPE html>
<html>
<head>
	<title>Minecraft PowerTool | Glass Arcadia</title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

	<!-- scripts -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>
	<script>$j = jQuery.noConflict(true)</script>
	<script src="https://ajax.googleapis.com/ajax/libs/mootools/1.3.2/mootools.js"></script>
	<script src="js/circle.js"></script>
	<script type="text/javascript"> <!-- Ping server (TODO implement AJAX) -->
		// TODO use ajax to call (shouldn't have to reload page)
		function ping()
		{
		/*
			$j.ajax( { type: "GET", data:{ server: document.getElementById("server").value, port: document.getElementById("port").value }, success: function(data) {
				//alert('complete, returned:' + data); //returns webpage in html...
			} });
		*/
			window.location.href = "?server=" + document.getElementById("server").value + "&port=" + document.getElementById("port").value;
		}
	</script>
	<script type="text/javascript"> <!-- Filter List code -->
	$j(document).ready(function() {
		$j('#filter').keyup(function() {
				/* // regexes are ugly :(
				var re = new RegExp($j('#filter').val(), "i");
				$j('#list').children().each( function(i) {
					var toHide = !re.test($j(this).text());
					(toHide) ? $j(this).hide() : $j(this).show();
				});
				*/
				var search = $j('#filter').val();
				$j('#list').children().each( function(i) {
					// compare case-adjusted search string to case-adjusted list item
					var toHide = !~$j(this).text().toLowerCase().indexOf( search.toLowerCase() ); // !=boolean NOT, ~=bitwise NOT
					(toHide) ? $j(this).hide() : $j(this).show();
				});
		});
	});
	</script>
	<script type="text/javascript"> <!-- Infographics Listbox OnChange code -->
	function OnSelectChange(data) {
		var img = document.getElementById("imgGuide");
		var infotext = document.getElementById("infotext");
		
		if (data=="Food") {
			img.src = "img/food.jpg";
			infotext.innerHTML = "";
		} else if (data=="Brewing") {
			img.src = "img/brewing.jpg";
			infotext.innerHTML = "Image courtesy of <a href=\"http://minecraft.gamepedia.com/Brewing\">MineCraft Wiki</a>";
		} else if (data=="Redstone") {
			img.src = "img/redstone.jpg";
			infotext.innerHTML = "";
		} else if (data=="blankerino") {
			img.src = "";
			infotext.innerHTML = "";
		}
		
		if (img.src == window.location) { img.style.display = 'none'; } else { img.style.display = 'block'; }
	}
	</script>
	<?php
	// TODO update HTML elements asynch when called
	if (!empty($_GET['server']) && !empty($_GET['port'])) {
		$SERVER_IP = $_GET['server']; //"23.95.29.207"; //Insert the IP of the server you want to query. 
		$SERVER_PORT = $_GET['port']; //"25565"; //Insert the PORT of the server you want to ping. Needed to get the favicon, motd, players online and players max. etc

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
	}
	?>
	
	<!-- styles -->
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/arcadia.css">
</head>

<body>
<noscript><strong>MineTool requires JavaScript to be turned on!</strong></noscript><br />
	<div id="content">
		<!-- Tab Layout -->
		<ul id="tabs" class="nav nav-tabs nav-justified" data-tabs="tabs">
			<li class="active"><a href="#player" data-toggle="tab">Server Info</a></li>
			<li><a href="#circle" data-toggle="tab">Circle Generator</a></li>
			<li><a href="#guides" data-toggle="tab">Guides</a></li>
		</ul>
		<!-- Tab Content -->
		<div id="tab-content" class="tab-content">
			<div class="tab-pane active" id="player">
				<!-- Player List -->
				Server Address: <input type="text" id="server" value=<?php echo $_GET['server']; ?>> <br>
				Port Number:&nbsp&nbsp&nbsp <input type="number" id="port" min="1" max="99999" value=<?php echo $_GET['port']; ?>> <br>
				<button type="button" id="ping" onclick="ping()">Ping</button>
				<button type="button" id="ga" onclick=window.location.href="?server=23.95.29.207&port=25565">Glass Arcadia</button>
				<!-- DEBUG <a href="tool.php">return</a> -->
				<br /><br />
				<div class="row">
					<div class="col-md-12">
						<h4>General Information</h4>
						<table class="table table-striped">
							<tbody>
								<tr>
									<td><b>IP</b></td>
									<td><?php if(!empty($SERVER_IP)) { echo $SERVER_IP . ':' . $SERVER_PORT; } ?></td>
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
									<td><?php if(!empty($version)) { echo "".$online." / ".$max.""; } ?></td>
								</tr>
							<?php } ?>
								<tr>
									<td><b>Status</b></td>
									<td><?php if(empty($version)){} else if (empty($ping['error'])) {echo "<i class=\"fa fa-check-circle\"></i><font color=\"#00DA00\"> Server is online</font>";} else{echo "<i class=\"fa fa-times-circle\"></i><font color=\"#DA0000\"> Server is offline</font>";} ?></td>
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
					<div class="col-md-12" style="font-size:10px;">
						<h4>Players</h4>
						<?php
						$url = "https://cravatar.eu/helmavatar/";
						if(empty($query['error'])) {
							if($playerlist != "null" && !empty($playerlist) ) {
								$shown = "0";
								foreach ($playerlist as $player) {
									$shown++;
								?>
									<a data-placement="top" rel="tooltip" style="display: inline-block;" title="<?php echo $player;?>">
									<?php echo $player;?><br />
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
			</div>
			<div class="tab-pane" id="circle">
				<!-- Circle Generator -->
				<table border="0" cellpadding="0" cellspacing="0" >
					<tr>
						<td valign="left" width="400"> <!-- Circle Generator -->
							<div class="col-md-16">
								<h4>Circle Generator</h4>
								Width:&nbsp <input tabindex="1" type="number" size="5" id="diameter" value="8" max="2048" autocomplete="off" autofocus>
								Block Count: <span id="blockcount"></span><br />
								Height: <input tabindex="2" type="number" size="5" id="height" value="8" max="2048" autocomplete="off">
								<select id="thickness" tabindex="3">
									<option selected="selected">thin</option>
									<option>thick</option>
									<option>filled</option>
								</select>
								<br /><br />
								<div id="result"></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="tab-pane" id="guides">
				<!-- Guides -->
				<table border="0" cellpadding="0" cellspacing="0" >
					<tr>
						<td valign="top" width="400"> <!-- Block ID filter list -->
							<div name="blockID" id="blockID" class="col-md-16">
								<h4>Block IDs</h4>
								<input id="filter" >
								<ul id="list">
									<table border="0" cellpadding="0" cellspacing="0">
										<?php
											$blockfile = fopen("blocks.txt", "r");
											if ($blockfile)
											{
												while (($buffer = fgets( $blockfile, filesize("blocks.txt") )) !== false)
												{
													echo "<li>" . $buffer . "</li>\r\n";	
												}
											}
											fclose( $blockfile );
										?>
									</table>
								</ul>
							</div>
						</td>
						<td valign="top"  width="750" nowrap> <!-- Guides and infographics -->
							<div class="col-md-8">
								<h4>Infographics</h4>
								<select id="guidelist" onchange="OnSelectChange(this.value)">
									<option selected="selected" disabled="disabled">===</option>
									<option>Food</option>
									<option>Brewing</option>
									<option>Redstone</option>
									<option>blankerino</option>
								</select>
								<br /><br />
								<p id="infotext"></p>
								<img id="imgGuide">
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</body>
</html>
