window.addEvent('domready', function(){

	//inputs
	var dia         = $('rectwidth'),
		height      = $('rectheight'),
		linked      = 0,

	//outputs
	var resultblock = $('golden'),
		blockcount  = $('rectblocks');

	if( !!window.location.hash || (!!window.localStorage && !!window.localStorage.CircleHash) ) {
		try{
			var hashval = {};
			
			if( !!window.location.hash ) {
				var hashval = JSON.decode( window.location.hash.replace(/^[# ]+/g, "").decodeBase64() );
			}else{
				var hashval = JSON.decode( window.localStorage.CircleHash );
			}
			dia.set('value',        Math.max( hashval.width,  1) );
			height.set('value',     Math.max( hashval.height, 1) );
		}catch(err){
			if( console && console.log ) console.log(err);
		}
		
	}

	var distance = function(x, y, ratio) {
		return Math.sqrt((Math.pow(y * ratio, 2)) + Math.pow(x, 2));
	};

	var filled = function(x, y, radius, ratio) {
		return distance(x, y, ratio) <= radius;
	};

	var fatfilled = function(x, y, radius, ratio) {
		return filled(x, y, radius, ratio) && !( 
			filled(x + 1, y, radius, ratio) &&
			filled(x - 1, y, radius, ratio) &&
			filled(x, y + 1, radius, ratio) &&
			filled(x, y - 1, radius, ratio) &&
			filled(x + 1, y + 1, radius, ratio) &&
			filled(x + 1, y - 1, radius, ratio) &&
			filled(x - 1, y - 1, radius, ratio) &&
			filled(x - 1, y + 1, radius, ratio)
		);
	};

	var Svg_renderer = function() {
		var _inner_content = '';
		var _svg_width  = 1;
		var _svg_height = 1;

		var _max_x = 0;
		var _max_y = 0;

		var _dwidth = 5;
		var _dborder = 1;
		var _dfull = _dwidth + _dborder;

		var hasInlineSvg = function() {
			var div = document.createElement('div');
			div.innerHTML = '<svg/>';
			return (div.firstChild && div.firstChild.namespaceURI) == 'http://www.w3.org/2000/svg';
		}

		this.init = function(max_x, max_y){
			_max_x = max_x;
			_max_y = max_y;
			_svg_width  = _dfull * max_x;
			_svg_height = _dfull * max_y;
			_inner_content = '';
		};

		this.add = function(x, y, filled) {

			var xp = ((x * _dfull) + (_svg_width/2) - (_dfull / 2)) + .5;
			var yp = ((y * _dfull) + (_svg_height/2) - (_dfull / 2)) + .5;

			var color = false;

			if(filled) {
				if( x == 0 || y == 0 ) {
					color = '#808080';
				}else{
					color = '#FF0000';
				}
			}else if( x == 0 || y == 0 ){
				if( x & 1 || y & 1 ) {
					color = '#EEEEEE';
				}else{
					color = '#F8F8F8';
				}
			}

			if( color !== false ) {
				_inner_content += '<rect x="'+xp+'" y="'+yp+'" fill="'+color+'" width="'+_dwidth+'" height="'+_dwidth+'"/>';
			}
		};

		this.render = function(){
			var text = '';
			text += '<svg id="svg_circle" xmlns="http://www.w3.org/2000/svg" width="'+_svg_width+'px" height="'+_svg_height+'px" viewBox="0 0 '+_svg_width+' '+_svg_height+'">';
			text += _inner_content;
			

			for(var ix = 0; ix < _svg_width; ix += _dfull) {
				text += '<rect x="'+(ix + (_dwidth / 2))+'" y="0" fill="#bbbbbb" width="'+_dborder+'" height="'+_svg_height+'" opacity=".4" />';
			}

			for(var iy = 0; iy < _svg_height; iy += _dfull) {
				text += '<rect x="0" y="'+(iy + (_dwidth / 2))+'" fill="#bbbbbb" width="'+_svg_width+'" opacity=".6" height="'+_dborder+'"/>';
			}

			text += '</svg>';

			if( hasInlineSvg() ) {
				return text;
			}else{
				return '<img id="svg_circle" src="data:image/svg+xml;base64,' + text.toBase64() + '">';
			}
			
		};

		this.scale = function(scale){
			var svgc = $$('#svg_circle');

			svgc.set('width', scale + 'px').set({'width': scale + 'px','height': scale + 'px'}).setStyles({'width': scale,'height': scale});
		};
	}

	var Table_renderer = function() {
		var _row_data = {};
		var _max_y;

		this.init = function(max_x, max_y){
			_max_x = max_x;
			_max_y = max_y;
			_row_data = {};
		}

		this.add = function(x, y, filled) {
			var offset_y = y + _max_y;
			if(typeof _row_data[offset_y] == "undefined") { _row_data[offset_y] = ''; }
			_row_data[offset_y] += '<td class="'+(filled ? 'filled' : '')+' cgx'+x+' cgy'+y+'"></td>';
		};

		this.render = function(){
			var text = '';
			text += '<table class="circle_output">';

			for( row in _row_data ) {
				if( _row_data.hasOwnProperty(row) ) {
					text += '<tr>'+ _row_data[row] +'</tr>';
				}
			}
			text += '</table>';

			text = '<p>Your browser does not support SVG, running in table compatibility mode. <br /> You will get <b>insanely</b> better performance from a modern browser like <a href="https://www.google.com/chrome/" target="_blank">Chrome</a>.</p>' + text;

			return text;
		};

		this.scale = function(scale){
			var tdwidth = scale / _max_x;
			$$('table.circle_output td').setStyles({'width': tdwidth,'height': tdwidth}); 
		};
	};


	var renderer = document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1") ? new Svg_renderer() : new Table_renderer();

	var hashTimeout = false;

	var draw = function(){
		var width_r = parseFloat(dia.value, 10) / 2;
		var height_r = parseFloat(height.value, 10) / 2;
		var ratio = width_r / height_r;

		var maxblocks_x, maxblocks_y;
		//var text = '';
		var ifilled = 0;

		if ( (width_r * 2) % 2 == 0 ) {
			maxblocks_x = Math.ceil(width_r - .5) * 2 + 1;
		} else {
			maxblocks_x = Math.ceil(width_r) * 2;	
		}

		if ( (height_r * 2) % 2 == 0 ) {
			maxblocks_y = Math.ceil(height_r - .5) * 2 + 1;
		} else {
			maxblocks_y = Math.ceil(height_r) * 2;	
		}

		renderer.init( maxblocks_x, maxblocks_y );
		var hash = JSON.encode({
			width:     width_r * 2, 
			height:    height_r * 2,
		});

		//setting it every time was slow, pause a second and then set it
		window.clearTimeout(hashTimeout);
		hashTimeout = window.setTimeout(function(){
			if( window.localStorage ) {
				window.localStorage.CircleHash = hash;
			}

//			window.location.hash = hash.toBase64();
		}, 1000);
		
		if( resultblock.get('data-hash') != hash ) {

			for (var y = -maxblocks_y / 2 + 1; y <= maxblocks_y / 2 - 1; y++) {
				for (var x =- maxblocks_x / 2 + 1; x <= maxblocks_x / 2 - 1; x++) {
					var xfilled;

					xfilled = fatfilled(x, y, width_r, ratio) && 
						!(fatfilled(x + (x > 0 ? 1 : -1), y, width_r, ratio) && fatfilled(x, y + (y > 0 ? 1 : -1), width_r, ratio));

					ifilled += (!!xfilled) * 1;

					renderer.add( x, y, xfilled );
				}
			}
			
			resultblock.set('data-hash', hash);
			resultblock.innerHTML = renderer.render();
			blockcount.set('html', ifilled);
		}

		renderer.scale(300);
	};

	var numcleanup = function(){
		this.set('value', parseInt( this.get('value'), 10 ).limit(1,2048) );

		if(linked.checked) {
			if(this.get('id') == height.get('id')) {
				diameter.set('value', height.get('value'));
			}else{
				height.set('value', diameter.get('value'));
			}
		}
	}

	$$(dia, height).addEvent('keyup', numcleanup).addEvent('change', numcleanup);
	$$(dia, height, linked).addEvent('keyup', draw).addEvent('change', draw);


	draw();
});
