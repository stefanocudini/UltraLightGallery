<?

//dipends: jquery,desc

function slideshow_init()  //se l'album e' privato il plugin disattiva se stesso!! :)
{
	global $public;
	global $admin;
	global $plugins;
	global $urls;
	global $dirs;
	global $dims;
	global $opts;
	global $imgfiles;
	global $images;
	global $masks;
	global $fotopage;
	global $index;
	global $lastslides;
	global $start;
	global $maxslidelist;
	global $minislider;

	$maxslidelist = isset($maxslidelist) ? 	$maxslidelist : 12;

	$urls['slidepage'] = "./?slideshow";

	$images['slideicon']['dir'] = $dirs['cache_base'].'_slideicon.png';
	$images['slideicon']['url'] = $urls['cache_base'].'_slideicon.png';

	$images['slidecur']['dir'] = $dirs['cache_base'].'_slidecur.cur';
	$images['slidecur']['url'] = $urls['cache_base'].'_slidecur.cur';

	$lastslides = false;#$index;  //mostra oppure no le ultie foto di un album indice

	$minislider = false;  //mostra oppure no la minislideshow nel pannello a destra

	$dims['minislidesize'] = back_tnsize(max($dims['tnsizes']));
	$dims['maxslidesize'] = max($dims['tnsizes']);
	$dims['lastslidetnsize'] = min($dims['tnsizes']);

	if(!$public and !$admin)  //per non mostrare sul menu, l'accesso ai file è gia bloccato in getfiles()
	{
	  unset($plugins['slideshow']);
	  $lastslides = false;
	}

	if(isset($_GET['slideshow'])):

		if( isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER["SERVER_NAME"].$urls['current'] )
		{
			if($public or $admin)
		  		$start = 'slideshow';
		}
		else
		{
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$urls['current']); 
			exit(0);
		}
		
	endif;

}  //fine init()

function slideshow_start()
{
	global $imgfiles;
	global $dims;
	global $urls;
	global $fotopage;
	global $opts;
	global $nocss;

	function slideshow_title()
	{
	 ?> <big>&raquo;</big> Slideshow<?
	}

	head();

?>
<body id="slideshow" style="background-color:#000">
<div id="slider">
<?	
	for($i=0; $i<count($imgfiles); $i++):
	
		$dims['tnsize'] = $dims['maxslidesize'];
		$dims['tnmargin'] = 0;
		$opts['thumbquad'] = 0;
		$opts['thumbround'] = 0;
		$opts['thumbcut'] = 0;
		$opts['thumbinterlace'] = 1;
		$opts['thumbqualit'] = 95;

		$fotopage = true;

		$fotofile = decript_filename($imgfiles[$i]);

		list($w,$h) = thumbsize($fotofile);

		$l = $dims['maxslidesize']/2 - $w/2;
		$t = $dims['maxslidesize']/2 - $h/2;

	    if(!$nocss) $style = ' style="margin-left:'.pixem($l).'em; margin-top:'.pixem($t).'em;"';  //l'altezza si deve adattare al contenuto
	    else $style = '';

		?>
		<div class="imgwrap" style="width:<?=$w-18?>px;height:<?=$h?>px">
			<img width="<?=$w?>" height="<?=$h?>" src="<?=thumburl($fotofile)?>" />
		</div>
		<?

	endfor;
?>
</div>
<div id="controlslider">
	<a href="#" id="prevslider" class="pulsante"><big>◄</big></a>
	<a href="#" id="playstopslider" class="pulsante">I I</a>
	<a href="#" id="nextslider" class="pulsante"><big>►</big></a>
</div>
<div id="controlslider2">
	<a href="#" id="closeslider" class="pulsante">chiudi</a>
</div>
<?
  js();
  tail();
}

function slideshow_text()
{
  global $lastslides;
  global $fotopage;

	if($lastslides==false or $fotopage) return false;

?>
<div id="lastslides"><div id="lastempty"></div></div>
<?

  return 'Ultime foto';
} # */


function slideshow_panel()  //minislideshow
{
	global $dims;
	global $opts;
	global $slidesize;
    global $imgfiles;
	global $minislider;

	if(!$minislider or count($imgfiles)==0) return false;

?>
<div id="minislider">
<?
#caricamento contenuto in ajax
?>
</div>
<div id="controlminislider">
	<a href="#" id="prevminislider" class="pulsante">&laquo;</a>
	<a href="#" id="playstopminislider" class="pulsante">Play</a>
	<a href="#" id="nextminislider" class="pulsante">&raquo;</a>
</div>
<br />
<?

  return 'Mini Slideshow';
} # */

function slideshow_lastslides($fotofile='')
{
	global $dims;
	global $dirs;
	global $urls;
	global $opts;
	global $alerts;
	global $public;
	global $lastslides;
	global $maxslidelist;
	global $nolist;
	global $ajax;

	$fotofile = getcript_filename($fotofile);

	$dims['maxgetfiles'] = 8;

	$opts['thumbquad'] = 1;
	$opts['thumbround'] = 1;

	if($lastslides==false) return false;

	$deftnsize = $dims['tnsize'];
	$dims['tnsize'] = $dims['lastslidetnsize'];
	$defaction = $urls['action'];
	$defurlcurrent = $urls['current'];
	$defcurrent = $dirs['current'];
	$defdircache = $dirs['cache'];
	$defurlcache = $urls['cache'];

###########
	$rfiles = array();
	$allrfiles = array();

	$allrfiles = rgetfiles();
    $allrfiles = sortfiles($dirs['current'], $allrfiles);

	$minslidelist = 0;
	if(is_file($fotofile))
	  $minslidelist = array_search($fotofile,$allrfiles)+1;  //parte dal file $fotofile
	$maxslidelist = 12;                                //numero di foto nella lista
	$maxslidelist = $minslidelist + $maxslidelist;

	for($i=$minslidelist; $i<$maxslidelist; $i++)
	{
	  $rfiles[]= $allrfiles[$i];
	  #echo $allrfiles[$i]."<br>";
	}
###########

	$lastdir = dirname($rfiles[0])!='.' ? dirname($rfiles[0]).'/' : '';
	foreach($rfiles as $fil)
	{
		$dirfile = dirname($fil)!='.' ? dirname($fil).'/' : '';

		if($lastdir!=$dirfile) echo '<div class="thumb_link space">&nbsp;</div>';

		$urls['current'] = $defurlcurrent.$dirfile;
		$dirs['current'] = $defcurrent.$dirfile;
		$urls['action'] = dirname($defaction).'/'.$dirfile;
		$dirs['cache'] = $dirs['current'].$opts['thumbdirname'].'/';
		$urls['cache'] = $urls['current'].$opts['thumbdirname'].'/';

		thumb_link( decript_filename($fil) );

		$lastdir = $dirfile;
		flush();
	}

	$dims['tnsize'] = $deftnsize;
	$urls['action'] = $defaction;
	$urls['current'] = $defurlcurrent;
	$dirs['current'] = $defcurrent;
	$dirs['cache'] = $defdircache;
	$urls['cache'] = $defurlcache;
}

function slideshow_listslides()
{
	global $imgfiles;
	global $dims;
	global $opts;
	global $recache;

	$dims['tnsize'] = $dims['minislidesize'];
	$opts['thumbcut'] = 0;
	$opts['thumbquad'] = 1;
	$opts['thumbround'] = 0;

	for($i=0; $i<count($imgfiles); $i++)
	{
        thumb_link(decript_filename($imgfiles[$i]));
		echo "\n";
	}
}

function slideshow_head_js()
{
	global $urls;
	global $index;
	global $imgfiles;
	global $start;
	global $minislider;

	if($start=='slideshow' or $minislider):
?>
<script src="<?=$urls['plugins']?>jquery.cycle.min.js"></script>
<script src="<?=$urls['plugins']?>jquery.transit.js"></script>
<?
	else:
?>
<script>
ULG.opts.lastslides = <?=$index?'true':'false'?>;
</script>
<?
	endif;
} # */

function slideshow_js()
{
  global $lastslides;
  global $urls;
  global $colors;
  global $dirs;
  global $masks;
  global $minislider;
?>
<? if(false): ?>
<script>
<? endif; ?>

var minisliderloaded = false;

var lastloading = $('<div id="lastloading" class="imgloader">Ultime Foto...</div>');
var refreshlastp1 = $('<a href="#" id="refreshlastp1" class="pulsante">&laquo;</a>');
var refreshlastp = $('<a href="#" id="refreshlastp" class="pulsante">&raquo;</a>');
var lastdescription = $('<div id="lastdescription"><b></b> <span></span></div>');
//oggettini utili :)
var urlslidepage = '<?php echo $urls['slidepage']; ?>';

function loadminislider()  //carica le slides sul minislider in ajax
{
	$('#minislider')
	.load(ULG.urls.action,
		{
			ajax:'slideshow',
			func:'listslides'
		},
		function(r) {
			$(this)
			  .cycle({
			         fx:'fade',  //non serve per cycle lite
					 timeout: 2500,
					 prev: '#prevminislider',
					 next: '#nextminislider'
					 });
			minisliderloaded = true;
		});
}

function loadlastslides()
{
    $('#refreshlastp').blur();

    var ffoto = $('#lastslides .imgthumb:last').parent().attr('href');

	var fotourl = "<?=str_replace('%s','',$masks['fotopageurl'])?>";

	ffoto = ffoto!=null ? ffoto.replace(ULG.urls.base, '').replace(fotourl,'') :'';

	$('#lastslides')
	    .append(lastloading)  //SISTEMARE SU IE
		.load(ULG.urls.action+'&'+ulgrand(),
			{
				ajax:'slideshow',
				func:'lastslides',
				file: ffoto
			},
			function() {
				var lastdes = lastdescription.clone();

				$(this).append(refreshlastp.clone(true)).append(lastdes);

				$('.imgthumb',$(this)).hover(function() {
						$(lastdes).text($(this).attr('alt'));
					}, function() {
						$(lastdes).empty();
					});
			});
}

function fullopen()
{
  var top = -5;
  var left = -5;
  var h = window.screen.height+10;
  var w = window.screen.width+10;
  var option = "top="+top+",left="+left+",toolbar=no,scrollbars=auto,location=no,status=0,width="+w+",height="+h;
  var popwindow = window.open(urlslidepage,"",option);
}

<? if($minislider): ?>
add_panel_event(function(obj) {

if(obj.attr('id')!='panel_slideshow') return false;

	$('#playstopminislider')
	  .toggle(function() {
			if(minisliderloaded==false)  //scarica minislider
				loadminislider();
			else
				$('#minislider').cycle('resume');

			$(this).text('stop');
		},
		function() {
          $(this).text('play');
	      $('#minislider').cycle('pause');
		});
});
<? endif; ?>

function slideshow_events()
{

	var h,s; //timer

	var slider = $('#slider');

	slider.cycle({
	  	//delay:300,
	  	fx: 'fade',
	  	before: function() {
	  		var img$ = $(this).children('img');
	  		img$.height( img$.attr('height') );
			img$.width( img$.attr('width') );
			img$.css({transform:'',opacity:1});
	  	},
		after: function() {
			var img$ = $(this).children('img');

			img$.transition({
				translate: [-40,-40],
				scale: 1.4,
				opacity:0.1,
				rotate: 4
			}, 6000, 'in');

		},
		speed: 2000,
		timeout: 4000,
		prev:'#prevslider',
		next:'#nextslider'
	});

	function slide_hidecontrol()
	{
		h = setTimeout(function() {
				$('#controlslider,#controlslider2').fadeOut('slow');
		},3000);
	}
	function slide_showcontrol()
	{
		clearTimeout(h);
		$('#controlslider,#controlslider2').show();
		slide_hidecontrol();
	}
	$('#slider').mousemove(function() {
	  slide_showcontrol();
	});

	$('#controlslider a').click(function() {
  	  slide_showcontrol();
	  $(this).blur();
	});

	$('#playstopslider')
	  .toggle(function() {
			slider.cycle('pause');
			$(this).html('&gt;');
		},
		function() {
			slider.cycle('resume',true);
			$(this).html('I I');
		});

	$('#closeslider').click(function() { window.close(); });

	slide_hidecontrol();

	$(document).on('keydown',function (e) {
		switch(e.keyCode)
		{
			case 37:
				$('#prevslider').click();
				break;
			case 39:
				$('#nextslider').click();
				break;
			case 27:
				$('#closeslider').click();
				break;
		}
	});	
}

$(function() {

	if($('body').is('#slideshow'))
	{
		slideshow_events();
	}
	else
	{
		$('#slidebutton').click(function() {
		   fullopen();
		   return false;
		});
	}
});


<? if(false): ?>
</script>
<? endif; ?>
<?
} # */

function slideshow_css()
{
	global $images;
	global $colors;
	global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
body#slideshow {
  margin:0;
  padding:0;
  background: #000000;
  text-align:center;
  vertical-align:middle;
  overflow:hidden;
}
body#slideshow #slider {
	text-align:center;
	position: absolute;
	top:50%;
	left:50%;
	/*dopo il cycle() diventa cmq relative */
	margin:0 auto;
	width:<?=pixem($dims['maxslidesize'])?>em;
	height:<?=pixem($dims['maxslidesize'])?>em;
	margin-top:-<?=pixem($dims['maxslidesize']/2)?>em;
	margin-left:-<?=pixem($dims['maxslidesize']/2)?>em;
	text-align:center;
}
body#slideshow #slider img {
	position: relative;
}
body#slideshow #slider .imgwrap {	
	position: absolute;
	overflow: hidden;
	border: 0px solid #666;
}

#prevslider,
#playstopslider,
#nextslider,
#closeslider {
	background-color:#333;
	border: 0.125em solid <?=$colors['text']?>;
	padding:0.125em 0.5em;
}

#controlslider,
#controlslider2 {
	position:absolute;
	z-index:100;
	left:50%;
	bottom:-16px;
	border:0.125em solid #666;
	background-color:#333;
	padding:1em 0.5em;
	width:9em;
	height:2em;
	margin-left:-4.5em;
	border-radius: 1em;
	opacity: 0.8;
}
#controlslider2 {
	top:-24px;
	bottom:auto;
}
body#slideshow #closeslider {
	position:absolute;
	top:2em;
	left:50%;
	margin-left:-2.5em;
	width: 4em;
	margin-bottom:0;
}

#minislider {
	border: 1px solid <?=$colors['border']?>;
	background-color: <?=$colors['bgbox']?>;
	width: <?=pixem($dims['minislidesize'])?>em;
	height: <?=pixem($dims['minislidesize'])?>em;
	margin-bottom:1em;
	overflow:hidden;
}
#minislider {}
#lastslides {
    border: 0px solid <?=$colors['border']?>;
	overflow:hidden;
	margin-bottom:1em;
	margin: 0;
	position:relative;
	text-align:center;
}
#lastempty {
	height: <?=pixem($dims['lastslidetnsize'])?>em;
}

#lastdescription {
	clear:both;
	float:left;
	height:1.25em;
	margin-top:-0.5em;
	font-size:small;
	font-weight:bold;
}

#refreshlastp,
#refreshlastp1 {
  float:left;
  line-height: <?=pixem($dims['lastslidetnsize']-8)?>em;
  height: <?=pixem($dims['lastslidetnsize']-8)?>em;
  margin: 0;
}
#lastloading {
  text-align:center;
  vertical-align:middle;
  background-color: <?=$colors['bgbox']?>;
  line-height: <?=pixem($dims['lastslidetnsize']+4)?>em;
  position:absolute;
  top:0;
  left:0;
  right:0;
  bottom:0.5em;
  margin:0;
  opacity: 0.7;
}
#lastempty,
#lastslides .thumb_link,
#lastslides .thumb_link.space {
  float: left;
  margin: 0 0.5em 0.5em 0;
  background:none;
}
#lastslides .thumb_link.space {
  width:.5em;
  line-height: <?=pixem($dims['lastslidetnsize']+4)?>em;
}
#lastslides .lastalbum {
  border: 0.0625em solid <?=$colors['border']?>;
  padding: 0.125em 0;
  margin: 0;
  float: left;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function slideshow_menu()
{
	global $images;
	global $urls;
	global $imgfiles;

	if(count($imgfiles)<2) return false;

  ?><a href="#" id="slidebutton" title="Avvia presentazione"><img src="<?=$images['slideicon']['url']?>" alt="slideshow" /><span> Slideshow</span></a><?
	return 'Play Slideshow';
} # */


function slideshow_cache()   //in futuro generare i dati exif all'interno di questa funzione.. ua volta per tutte
{
  global $images;

  require('slideshow.cache.php');

  put_contents($images['slideicon']['dir'],$icon1);
  put_contents($images['slidecur']['dir'],$cur1);

}

?>
