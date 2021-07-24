<?php

function demo_content_top()
{
	global $urls;
	global $admin;
	global $fotopage;

	if($admin) return false;
	
	if($fotopage or $urls['current']!=$urls['base']) return false;

?>
<div id="presentation_wrap">
	<h2><?=VERSION?></h2>
	<div style="margin:0 auto;width:880px">
	<div style="margin-right:.5em;float:left">
		<div id="presentation">
			<div>
				<img src="_screenshots/upload.png" />
				<p>
					<b>Upload</b> simultaneo delle foto senza ricaricare la pagina
				</p>
			</div>
			<div style="display:none">
				<img src="_screenshots/desc.png" />
				<p>
					<b>Aggiungi titolo</b> e descrizione facilmente alle foto appena caricate
				</p>
			</div>
			<div style="display:none">
				<img src="_screenshots/delete.png" />
				<p><b>Elimina</b> foto e album caricati per errore
				</p> 
			</div>
			<div style="display:none">
				<img src="_screenshots/crop.png" />	
				<p>
					<b>Ritaglia</b> le foto dopo averle caricate
				</p> 
			</div>
			<div style="display:none">
				<img src="_screenshots/rotate.png" />	
				<p>
					<b>Ruota</b> in un click le foto orientate male
				</p> 
			</div>
			<div style="display:none">
				<img src="_screenshots/order.png" />	
				<p>
					<b>Ordina</b> le foto come vuoi tu dopo averle caricate
				</p> 
			</div>
			<div style="display:none">
				<img src="_screenshots/map.png" />	
				<p>
					<b>Mappa</b> geografica degli album navigabile
				</p> 
			</div>	
			<div style="display:none">
				<img src="_screenshots/calendar.png" />	
				<p>
					<b>Calendario</b> dell'album aggiornabile in ogni momento
				</p> 
			</div>	
			<div style="display:none">
				<img src="_screenshots/slideshow.png" />	
				<p>
					<b>Presentazione</b> con autoplay e adattabile allo schermo
				</p> 
			</div>	
			<div style="display:none">
				<img src="_screenshots/watermark.png" />	
				<p>
					<b>Watermark</b> impresso automaticamente sulle foto
				</p> 
			</div>	
		</div>
	</div>
	<div id="presentation_desc">
		<ul>
			<li>PHP/jQuery con HTML5</li>
			<li>Architettura modulare</li>
			<li>Interfaccia di gestione integrata</li>
			<li>Ottimizzazioni SEO</li>
			<li>Compressione Javascript e Css automatica</li>
			<li>Decine di plugins attivabili</li>
			<li class="null">&nbsp;</li>
			<li class="no">Non richiede installazione</li>
			<li class="no">Non usa database esterni</li>
		</ul>
	</div>
	<div id="nav" class="nav"></div>
</div>
<br />
<b>Account Demo!</b> user: <i>demo</i>  pass: <i>demo</i>
</div>

<a style="position:absolute;top:8px;right:8px;opacity:.8;" href="https://github.com/stefanocudini/UltraLightGallery"><img id="ribbon" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>

<?
}

function demo_head_css()
{
	global $urls;
	global $fotopage;
	global $images;

	if($fotopage or $urls['current']!=$urls['base']) return false;
?>
<style>
#presentation_wrap {
	margin: 1em 0;
	text-align: center;
	font-family: Helvetica;
	overflow: hidden;
	clear: both;
}
#presentation_wrap div {

}
#presentation_desc {
	float:left;
	width:250px;
	text-align: left;
	padding: 0;
	margin: 0;
	font-size: 1em;
}
#presentation_desc li {
	list-style-image: url('_screenshots/yes.png');
	color: #666;
	padding:0;
	margin-bottom: 2px;
	line-height: 18px;
	vertical-align: top;
}
#presentation_desc li.null {
	visibility: hidden;
	list-style-type: none;
	list-style-image: none;
	list-style: none;
	margin: 0;
}
#presentation_desc li.no {
	list-style-image: url('_screenshots/no.png');
	color: #a00;
}
#presentation {
	float: left;
	text-align: center;
	width:600px;
	height:260px;
	border:2px solid #fa0;
	border-radius:10px;
	box-shadow: inset 5px 5px 5px #ccc;		
}
#presentation div {
	padding:0;

}
#presentation div img {
	border: none;
	margin:0;
	z-index:10;
	opacity:0.65;
}
#presentation div b {
	font-size:1.2em;
}
#presentation div p {
	display:block;
	float:right;
	text-align: right;
	position:absolute;
	color: #000;
	background:#fd6;
	border: 1px solid #fa0;
	border-radius:5px;
	box-shadow: 2px 2px 10px #aaa;
	max-width:50%;
	opacity:.7;
	font-size:1.25em;
	right:-4px;
	top:16px;
	padding:10px;
	z-index:500;
}
#nav {
	clear:both;
}
#nav a {
	margin: 5px;
	padding: 5px;
	display: inline-block;
	height:12px;
	width:12px;
	border-radius: 12px;
	background: #ddd;
	color:#ddd;
}
#nav a.activeSlide {
	background: #fd6;
	color:#fd6;
}
</style>
<?
}


function demo_head_js()
{
	global $urls;
	global $fotopage;

	if($fotopage or $urls['current']!=$urls['base']) return false;
?>
<script src="<?=$urls['plugins']?>jquery.cycle.min.js"></script>
<script>
$(function() {
	$('#presentation').cycle({
		fx: 'scrollLeft',
		speed: 'slow',
		pager: '#nav',
		before: function() {
			console.log('demo_head_css');
			$(this).parent().fadeIn();
		}
	});
});
</script>
<?
}

?>
