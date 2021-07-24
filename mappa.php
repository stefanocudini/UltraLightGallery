<?

/*
//TODO inserire meta tags:
http://www.geo-tag.de/informator/en2.html

<meta name="geo.position" content="48.169822;11.601171" />
*/

function mappa_init()
{
	global $images;
	global $urls;
	global $dirs;
	global $opts;
	global $masks;
	global $start;
	global $public;
	global $admin;

	$opts['fullmap'] = isset($_GET['mappa']);
	//mappa degli album a schermo intero 
	
	$masks['mapfile'] = $dirs['data']."_map.txt";
	$masks['mapfilemulti'] = $dirs['data']."_mapmulti.txt";
	
	$opts['mapenable'] = true;

	if(is_file($masks['mapfile']))
		$opts['maptype'] = 'single';
	elseif(is_file($masks['mapfilemulti']))
		$opts['maptype'] = 'multi';
	else
		$opts['maptype'] = $opts['mapenable'] = false;
	
	$opts['mapsizes'] = array('h'=>160, 'w'=>320);
	
	$opts['mapcoords'] = array('lon'=>12.59102, 'lat'=>42.61197, 'zom'=>8,
					   'box'=>'11.2,41.92,13.99,43.3',
					   'h'=>$opts['mapsizes']['h'], 'w'=>$opts['mapsizes']['w'], 'lay'=>'mapnik');

	if($opts['fullmap'] and ($public or $admin))
		$start = 'mappa';

}  //fine init()

function mappa_start()//mappa a schermo intero
{	
	head();

?>
<body id="fullmap">
<?
	mappa_getsubmap();

	js();
	tail();
}


function mappa_getsubmap()//mappa leaflet
{
	global $opts;
?>
<? if(!$opts['fullmap']): ?>
	<a href="./?mappa"><b>Mappa a schermo intero &raquo;</b></a><br />
<? else: ?>
	<a href="./"><b>&laquo; torna all'album</b></a><br />
<? endif; ?>
	
<div id="mapmulti"></div>

<?
	return true;
}


function mappa_getmap($iframe=true)
{
	global $dirs;
	global $masks;
	global $opts;
	global $ajax;
	
	if($ajax)
		list($LON,$LAT, $ZOM,$BOX, $H,$W, $LAY) = array_values($opts['mapcoords']);

	if($opts['mapenable'])
	{
		list($LON,$LAT, $ZOM,$BOX, $H,$W, $LAY) = explode('::', trim(get_contents($masks['mapfile'])) );
		$LAY='mapnik';
		
		$H = $opts['mapsizes']['h'];
		$W = $opts['mapsizes']['w'];

		$embedurl = "http://www.openstreetmap.org/export/embed.html?bbox=$BOX&amp;layers=$LAY&amp;marker=$LAT,$LON";
		$linkurl = "http://www.openstreetmap.org/?lat=$LAT&amp;lon=$LON&amp;zoom=$ZOM&amp;layers=$LAY";
		
		if($iframe):
			?>
			<a id="bigmap" href="#" title="Ingrandisci Mappa" class="pulsante"><big>+</big></a>
			<iframe width="<?=$W?>" height="<?=$H?>" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?=$embedurl?>"></iframe>
			<small class="map-permalink"><i><?=$LON?>,<?=$LAT?></i> <a href="<?=$linkurl?>">Link alla mappa</a></small>
			<?
		else:
			list($LON,$LAT,$ZOM,$BOX,$H,$W,$LAY) = explode('::', trim(get_contents($masks['mapfile'])) );
			return array('lon'=>$LON, 'lat'=>$LAT, 'zom'=>$ZOM, 'lay'=>$LAY, 'h'=>$H, 'w'=>$W, 'box'=>$BOX);
		endif;
	}
}

function mappa_getpoints()
{
	global $cachetime;
	#header("Content-type: text/plain");
	$cachetime = array(30,0,0);
	//serve a far funzione la cache
	//anche del caso che nel config.php sia specificato $cachetime = false come in demo!!

	//cache_exec(array(30,0,0), 'mappa_listpoints');
	mappa_listpoints();
}

function mappa_listpoints()
{
	global $dirs;
	global $masks;
	global $ajax;
	global $opts;
	global $dims;
		
	$dirdatadefault = $dirs['data'];
	$rd = rgetdirs();
	$dats = array();
	foreach($rd as $d)
	{
		$mapfile = $d.'/'.$opts['datadirname'].'/'.basename($masks['mapfile']);
		if(!is_file($mapfile)) continue;

		$title = trim(strip_tags(plugins_rawexec('thumb_title',$d,false)));
		$desc = trim(strip_tags(plugins_rawexec('thumb_text',$d,false)));
		$url = $d;
		
		list($LON,$LAT,$ZOM,$BOX,$H,$W,$LAY) = explode('::', trim(get_contents( $mapfile )) );
		$LAY = 'mapnik';
		$dats[] = array('lat'=>(float)$LAT,'lon'=>(float)$LON,'title'=>$title,'url'=>$url,'description'=>$desc,'thumb'=>thumburl($d));
	}
	$dirs['data'] = $dirdatadefault;
	
	echo json_encode($dats);
}

if($admin):

function mappa_submit($fotofile)  //salva coordinate e zoom della mappa
{
	global $masks;
	global $alerts;
	global $dirs;
	global $ajax;
	global $opts;
	
	list($lon,$lat,$zom,$box,$h,$w,$lay) = explode('::',@$_POST['coords']);
	$lon = floatval($lon);
	$lat = floatval($lat);
	$zom = intval($zom);
	$box = trim($box); //oppure explode(',',$box);
	$h = intval($h);
	$w = intval($w);
	$lay = trim($lay);		
	
	$opts['mapcoords'] = array('lon'=>$lon,'lat'=>$lat,'zom'=>$zom,'box'=>$box,'h'=>$h,'w'=>$w,'lay'=>$lay);
	
    put_contents($masks['mapfile'], implode('::',$opts['mapcoords']));
	cache_reset();//rigenera la cache html della pagina

	$ok = true;
	$mess = "La mappa &egrave; stata salvata";
	
	$coords = mappa_getmap(false);
	
	$J['head']['ok']= $ok ? 'true' : 'false';
	$J['head']['mess']= $mess;
	$J['data']= $coords;
	$out =json_encode($J);

	if($ajax)
	  echo $out;
	else
	  $alerts[]= $mess;
}

function mappa_enable($multimap='single')
{
	global $masks;
	global $opts;
	global $ajax;
	global $dirs;
	
	$file = $multimap=='multi' ? $masks['mapfilemulti'] : $masks['mapfile'];

	put_contents($file, implode('::',$opts['mapcoords']));
	$opts['mapenable'] = true;
	cache_reset();//rigenera la cache html della pagina

	if($ajax)
		if($multimap=='multi')
			mappa_getsubmap();	
		else
			mappa_getmap();
}

function mappa_disable()
{
	global $masks;
	global $opts;
			
	@unlink($masks['mapfile']);
	@unlink($masks['mapfilemulti']);	
	cache_reset();//rigenera la cache html della pagina
}

endif; //fine admin

function mappa_head_css()
{
	global $opts;
	global $admin;
	
	if(!$admin and !$opts['mapenable']) return false;

	if(islocal()):
		?><link rel="stylesheet" href="/maps/leaflet/dist/leaflet.css" /><?
	else:
		?><link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.6/leaflet.css" /><?
	endif;
}

function mappa_head_js()
{
	global $opts;
	global $admin;
	
	if(!$admin and !$opts['mapenable']) return false;	
?>
<script>
<?php if($opts['maptype']=='multi'): ?>
ULG.opts.mapmulticoords = <? mappa_getpoints(); ?>;
<?php else: ?>
ULG.opts.mapcoords = <?php echo json_encode($opts['mapcoords']); ?>;
<?php endif; ?>
</script>
<?php
}

function mappa_content_top()
{
  global $admin;
  global $masks;
  global $images;
  global $public;
  global $fotopage;
  global $opts;

  if($fotopage) return false; 

?>
<div id="map_wrap">
<div id="map">
<?
	if($opts['maptype']=='single')
		mappa_getmap();
	elseif($opts['maptype']=='multi')
		mappa_getsubmap();
?>
</div>

<? if($admin){	//modifica mappa ?>
<div class="maptool">
	<input id="removemap" style="display:<?=($opts['mapenable']?'block':'none')?>" type="button" value="Rimuovi Mappa" class="pulsante" />
	<input id="addnewmap" style="display:<?=(!$opts['mapenable']?'block':'none')?>" type="button" value="Aggiungi Mappa" class="pulsante" />
	<input id="multimap"  style="display:<?=(!$opts['mapenable']?'block':'none')?>" type="button" value="Mappa degli album" class="pulsante" />
</div>
<div class="maptool" style="display:<?=(($opts['mapenable'] and $opts['maptype']=='single') ?'block':'none')?>">
	<input id="editmap" style="display:block" type="button" value="Modifica Mappa" class="pulsante" />
	<input id="savemap" style="display:none" type="button" value="Salva" class="pulsante" />
	<input id="cancmap" style="display:none" type="reset" value="Annulla" class="pulsante" />
</div>

<div id="maploading">
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><img src="<?=$images['jqueryloadingcircle']['url']?>" alt="Loading..." />
	<br /><b>Salva mappa...</b>
</div>
<? } ?>
</div>

<?
}

function mappa_js()
{
  global $urls;
  global $dirs;
  global $opts;
    
?>
<? if(false): ?>
<script>
<? endif; ?>
$(function() {

	var map$ = $('#map'),
		mapOL,	//OGGETTO OPENlAYERS
		iframe,
		mapdefdims = [<?=$opts['mapcoords']['h'].','.$opts['mapcoords']['w']?>];
	
	var wgs84,mercator;

	$('#addnewmap, #multimap').on('click',function(e) {
		
		var mtype = $(e.target).is('#multimap') ? 'multi' : 'single';

		$.get(ULG.urls.action,
			{
				ajax:'mappa',
				func:'enable',
				file: mtype
			},
			function(resp) {
				iframe = map$.children('iframe');
				map$.height(iframe.height()).width(iframe.width()).empty();
				
				if(mtype=='multi')
				{
					map$.html(resp);
					$('.maptool:last').hide();
					$.getJSON(ULG.urls.action,
						{
							ajax:'mappa',
							func:'getpoints'
						},
						function(json) {
							ULG.opts.mapmulticoords = json;
							showmultimap();
						});
				}
				else
				{
					map$.html( getviewmap(ULG.opts.mapcoords) );
					$('.maptool:last').show();
				}
				
				$('#removemap,#addnewmap,#multimap').toggle();
			});
	});
	
	$('#removemap').on('click',function() {
		$.get(ULG.urls.action,{ajax:'mappa',func:'disable'},function() {
			destroymapOL();
			$('#map').height('auto').width('auto').empty().prev().remove();
			$('.maptool:last').hide();
			$('#removemap,#addnewmap,#multimap').toggle();
		});
	});
	
	$('#editmap').on('click',function() {	//tramuta in mappa editabile		
		iframe = map$.children('iframe');
		map$.height(iframe.height()).width(iframe.width()).empty();
		
		makemapeditable();

		//crea mappa openlayers editabile
		
		$('#removemap,#savemap,#cancmap,#editmap').toggle();
		return false;
	});

	$('#cancmap').on('click', function() {  //annulla cambiamenti
		destroymapOL();
		map$.height(mapdefdims[0]).width(mapdefdims[1]).html( iframe );
		$('#removemap,#savemap,#cancmap,#editmap').toggle();
		return false;
	});

	$('#map').delegate('#bigmap','click', function() {  //annulla cambiamenti
		var big$ = $(this).children('big');
		if(big$.text()=='+') {
			var w = map$.parent().parent().innerWidth()-120,
				h = map$.innerHeight()*2;

			map$.height(h).width(w)
				.children('iframe')
					.height(h).width(w);

			big$.text('-');
		} else {  //annulla cambiamenti
			map$.height(mapdefdims[0]).width(mapdefdims[1]).children('iframe').height(mapdefdims[0]).width(mapdefdims[1]);
			big$.text('+');
		}
		return false;
	});

	$('#savemap').on('click', function() {

		$('#maploading').show();  // "sending..."
		
		var m = ULG.opts.mapcoords,
			newcoords = [m.lon,m.lat,
						m.zom,m.box,
						m.h,m.w,
						m.lay];

		$.post(ULG.urls.action,
		{
			ajax: "mappa",
			func: "submit",
			coords: newcoords.join('::')
		},
		function(resp) {

			if(resp.head.ok)  //tutto ok
			{
				$('#cancmap').trigger('click');
				map$.html( getviewmap(resp.data) );
				$('#maploading').hide();
			}
			ulgalert(resp.head.mess)
		},
		'json');//*/
		return false;
	});
	
	function setCoords(lonlat) {	//eseguito al click e moveend su openlayers
		var size = new OpenLayers.Size(21,25);
        var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
        var icon = new OpenLayers.Icon('http://www.openlayers.org/dev/img/marker.png',size,offset);
        mapOL.layers[1].clearMarkers();
        mapOL.layers[1].addMarker(new OpenLayers.Marker(lonlat,icon));

		var pos = lonlat.transform(mapOL.getProjectionObject(),wgs84);
		//posizione marker
		var bbox = (mapOL.getExtent().transform(mapOL.getProjectionObject(),wgs84)).toBBOX();
		//posizione mappa
		
		ULG.opts.mapcoords.lon = lonlat.lon.toFixed(6);
		ULG.opts.mapcoords.lat = lonlat.lat.toFixed(6);
		ULG.opts.mapcoords.zom = mapOL.getZoom();
		ULG.opts.mapcoords.box = bbox;
	}

	function destroymapOL() {	//destroy openlayers
		if(mapOL) mapOL.destroy();
	}
	
	function getviewmap(c) {
		return '<a id="bigmap" href="#" title="Ingrandisci Mappa" class="pulsante"><big>+</big></a>'+
			   '<iframe width="'+c.w+'" height="'+c.h+'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox='+c.box+'&amp;layers='+c.lay+'&amp;marker='+c.lat+','+c.lon+'"></iframe>'+
			   '<small class="map-permalink"><a href="http://www.openstreetmap.org/?lat='+c.lat+'&amp;lon='+c.lon+'&amp;zoom='+c.zom+'&amp;layers='+c.lay+'">Link alla mappa</a></small>';
	}

	function makemapeditable()
	{
		if(!window.OpenLayers)
			$.getScript('http://www.openlayers.org/api/OpenLayers.js', makemapeditable_do);	
		else
			makemapeditable_do();
	}
	
	function makemapeditable_do()
	{
		wgs84 = new OpenLayers.Projection("EPSG:4326");
		mercator =  new OpenLayers.Projection("EPSG:900913");
		
		ulgalert('Trascina la mappa e clicca su un punto');
		mapOL = new OpenLayers.Map({
			div: "map",
			//allOverlays: true,
			projection: mercator,
			displayProjection: wgs84
		});
		
		if(false && islocal())	//per velocizzare debug
			var osm = new OpenLayers.Layer.OSM("TMS Locale", "/maps/osm/${z}/${x}/${y}.png",{numZoomLevels: 19, alpha: true, attribution:''});
		else
			switch(ULG.opts.mapcoords.lay)
			{
				case 'cyclemap':
				var osm = new OpenLayers.Layer.OSM.CycleMap("CycleMap");
				break;
				case 'mapnik':
				var osm = new OpenLayers.Layer.OSM("OpenStreetMap");
				break;
			}
		mapOL.addLayers([osm]);
		mapOL.addControl(new OpenLayers.Control.Navigation());

		/* definizione controllo click */
		OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
			defaultHandlerOptions: {
				single: true,
				double: false,
				pixelTolerance: 0,
				stopSingle: false,
				stopDouble: false
			},
			initialize: function(options) {
				this.handlerOptions = OpenLayers.Util.extend(
					{}, this.defaultHandlerOptions
				);
				OpenLayers.Control.prototype.initialize.apply(
					this, arguments
				);
				this.handler = new OpenLayers.Handler.Click(
					this, {
						click: this.trigger
					}, this.handlerOptions
				);
			},
			trigger: function(e) {
				var lonlat = mapOL.getLonLatFromViewPortPx(e.xy);
		   		setCoords(lonlat);
			}
		});	
		
		var markers = new OpenLayers.Layer.Markers( "Posizioni" );
        mapOL.addLayer(markers);
		var clickControl = new OpenLayers.Control.Click();
		mapOL.addControl(clickControl);
		clickControl.activate();
		mapOL.events.register("moveend", null, function() {
			//$('.olLayerGoogleCopyright').hide();	//leva sto cazzo di copyright di google
			setCoords(mapOL.getCenter());
		});

		var box = new OpenLayers.Bounds.fromString(ULG.opts.mapcoords.box);
		mapOL.zoomToExtent(box.transform( wgs84, mapOL.getProjectionObject()));
		setCoords( new OpenLayers.LonLat(parseFloat(ULG.opts.mapcoords.lon), parseFloat(ULG.opts.mapcoords.lat)) );
		//imposta posizione del marker
	}

	function showmultimap()
	{
		if(!window.L)
			$.getScript('http://cdn.leafletjs.com/leaflet/v0.7.6/leaflet.js', showmultimap_do);	
		else
			showmultimap_do();
	}

	function showmultimap_do()
	{
		var map = new L.Map('mapmulti');
		var	osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			osmLayer = new L.TileLayer(osmUrl, {maxZoom: 18, attribution: ''}),
			positions = [],
			json = ULG.opts.mapmulticoords;//definito in head_js

		map.addLayer(osmLayer);
		
		if($.isArray(json))
		{
			for(p in json)
			{
				var J = json[p];
				var pos = new L.LatLng( J.lat.toFixed(6), J.lon.toFixed(6) );
				
				positions.push(pos);
				var marker = new L.Marker(pos);
				map.addLayer(marker);
				marker.bindPopup('<b>'+ J.title +'</b><br>'+
								 '<a href="'+ J.url +'"><img class="mapthumb" src="'+ J.thumb +'" /'+'></a><br>'+
								 '<div>'+J.description+'</div>'
								 );
			}
		}
		map.fitBounds( new L.LatLngBounds( positions ) );
	}
	
	if(ULG.opts.mapmulticoords)
		showmultimap();
});
<? if(false): ?>
</script>
<? endif; ?>
<?
} # */

function mappa_css()
{
	global $colors;
	global $opts;
	
if(false): ?>
<style type="text/css">
<? endif; ?>

#mapmulti {
	height:250px;
	width:500px;
}

#fullmap #mapmulti {
	height:500;
	width:auto;
	position:absolute;
	top:1.5em;
	left:0;
	right:0;
	bottom:0;
}

.mapthumb  {
	width:60px;
	height:60px;
}

.leaflet-popup-content {
	margin:.5em;
}

.leaflet-popup-content-wrapper {
	border-radius: .5em;
}

#map_wrap {
	float: right;
	position:relative;
	margin:1em;
}
#bigmap {
	position:absolute;
	top:-2px;
	right:-2px;
	display:block;
	border-radius: .25em;
	width:1em;
	height:1em;
	text-align:center;
	z-index:10000;
}
#map {
	position:relative;
	border-radius: .5em;
	overflow:hidden;
	background: #F1EEE8;
	margin-bottom:1em;
	border:0.125em solid <?=$colors['border']?>;
	max-width: 100%;
}
.maptool {
}
.maptool input {
	float:left;
	margin-right:.5em;
}
.map-permalink {
	position:absolute;
	top:0;
	right:2em;
	border:1px solid <?=$colors['border']?>;
	padding:2px;
	background:<?=$colors['bgbox']?>;
	background-color: <?=$colors['bginput']?>;
	border:0.0625em solid <?=$colors['border']?>;
	opacity: 0.8;
}
.map-permalink a {
	font-weight:bold;
}
.map-permalink i {
	font-weight:bold;
}
#maploading {
	display:none;
	position:absolute;
	top:0;
	left:0;
	margin: 0 -8px -8px 0;
	width:100%;
	height:100%;
	text-align:center;
	vertical-align:bottom;
	background-color: <?=$colors['bginput']?>;
	border:0.0625em solid <?=$colors['border']?>;
	opacity: 0.8;
}

.olPopup {
	background-color: <?=$colors['bginput']?>;
	padding:.25em;
	
}
.olPopup h2 {
	font-size: 1em;
	margin:0;
	padding:0;
	line-height:1em;
}
.olPopup p {
	display:none;
}
.olPopupContent {
	padding:0;	
}

<? if(false): ?>
</style>
<? endif;
}

?>
