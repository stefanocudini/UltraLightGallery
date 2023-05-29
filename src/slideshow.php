<?

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
	global $start;

	$urls['slidepage'] = "./?slideshow";

	$images['slideicon']['dir'] = $dirs['cache_base'].'_slideicon.png';
	$images['slideicon']['url'] = $urls['cache_base'].'_slideicon.png';

	$dims['maxslidesize'] = max($dims['tnsizes']);

	if(!$public and !$admin)
	  unset($plugins['slideshow']);

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
}

function slideshow_start()
{
	global $imgfiles;
	global $dims;
	global $urls;
	global $fotopage;
	global $opts;
	global $nocss;

	head();

?>
<body id="slideshow">
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
			<img width="<?=$w?>" height="<?=$h?>" src="<?=thumburl($fotofile)?>" id="<?=thumbid($fotofile)?>" />
		</div>
		<?

	endfor;
?>
</div>

<div id="controlslider">
	<a href="#" id="prevslider" class="pulsante"><span>&lt;</span></a>
	<a href="#" id="playstopslider" class="pulsante"><span>&#9632;</span></a>
	<a href="#" id="nextslider" class="pulsante"><span>&gt;</span></a>
</div>

<div id="controlslider2">
	<a href="#" id="closeslider"><big>&times;</big></a>
</div>
<?
  js();
  tail();
}

function slideshow_head_js()
{
	global $urls;
	global $index;
	global $imgfiles;
	global $start;

	if($start=='slideshow'):
?>
<script src="<?=$urls['plugins']?>jquery.cycle.min.js"></script>
<script src="<?=$urls['plugins']?>jquery.transit.js"></script>
<?
	endif;
}

function slideshow_js()
{
  global $urls;
  global $colors;
  global $dirs;
  global $masks;
?>
<? if(false): ?>
<script>
<? endif; ?>

ULG.urls.slidepage = '<?php echo $urls['slidepage']; ?>';

function fullopen()
{
	var opts = "top=0,left=0"+
			",width="+window.screen.width+",height="+window.screen.height+
			",toolbar=no,scrollbars=auto,location=no,status=0";
	window.open(ULG.urls.slidepage,'',opts);
}

function slideshow_events()
{
	var slider$ = $('#slider'),
		h, s; //timer

	function slide_hidecontrol()
	{
		h = setTimeout(function() {
			$('#controlslider,#controlslider2').fadeOut('slow');
		}, 3000);
	}
	
	function slide_showcontrol()
	{
		clearTimeout(h);
		$('#controlslider,#controlslider2').show();
		slide_hidecontrol();
	}

	function slide_resize()
	{
		slider$.find('.imgwrap').each(function() {
			
			var img$ = $(this).children('img'),
				fotosize = {w: parseFloat(img$.attr('width')),
							h: parseFloat(img$.attr('height')) },
				body = {w: $(window).width(),
						h: $(window).height() },
				w, h;
				
			h = body.h;
			w = (h/fotosize.h) * fotosize.w;//mantiene proporzioni img
			w = w;

			$(this).css({height: h, width: w, left:'50%', marginLeft: -parseInt(w/2) });
			img$.attr({height: h, width: w});
			//img$.css({height: h, width: w});
		});		
	}

	slide_resize();

	ULG.cyclepause = false;

	slider$.cycle({
	  	//delay:300,
		// speed:       1000,  // speed of the transition (any valid fx speed value) 
		// speedIn:     null,  // speed of the "in" transition 
		// speedOut:    null,  // speed of the "out" transition 
		// easing:      null,  // easing method for both in and out transitions 
		// easeIn:      null,  // easing for "in" transition 
		// easeOut:     null,  // easing for "out" transition
		timeout: 5000,
		prev: '#prevslider',
		next: '#nextslider',
	  	fx: 'none',
	  	before: function() {
	  		if(ULG.cyclepause) return;

	  		var img$ = $(this).children('img');
	  		//img$.height( img$.attr('height') );
			//img$.width( img$.attr('width') );
			//img$.css({transform:''});
			$(this).css({opacity:0});
			
			slide_resize();
	  	},
		after: function() {
			if(ULG.cyclepause) return;

			var img$ = $(this).children('img');

			$(this).queue(function() {
				$(this).animate({opacity: 1},1000);
				$(this).delay(3000).animate({opacity: 0},1000);
				$(this).dequeue();
			});

			img$.transition({
				//translate: [120,0],
				//rotate: 2
				scale: 1.1
			}, 6000, 'in',function() {	//deve durate piu di cycle timeout
				img$.css({transform:''});
			});
		}
	});	
	
	$('#slider').mousemove(function() {
		slide_showcontrol();
	});

	$('#controlslider a').click(function() {
		slide_showcontrol();
		$(this).blur();
	});

	$('#playstopslider')
	  .toggle(function() {
			slider$.find('img').css({transform:''});
			slider$.find('.imgwrap').css({opacity:1});
			ULG.cyclepause = true;  	
			slider$.cycle('pause').cycle('next');
			$(this).html('&#9658;');
		},
		function() {
			ULG.cyclepause = false; 
			slider$.cycle('resume',true);
			$(this).html('&#9632;');
		});

	$('#closeslider').click(function() { window.close(); });

	slide_hidecontrol();

	$(window).on('resize',function() {
		slide_resize();
	});

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
<? endif;
}

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
	background: #000;
	text-align:center;
	vertical-align:middle;
	overflow:hidden;
}
body#slideshow #slider {
	text-align: center;
	overflow: hidden;
	position: absolute;
/*	left: 10px;
	top: 10px;
	right: 10px;
	bottom: 10px;*/
	left: 0;
	top: 0;
	right: 0;
	bottom: 0;	
}
body#slideshow #slider img {
	position: relative;	
}
body#slideshow #slider .imgwrap {	
	position: absolute;
	overflow: hidden;
	opacity: 0;
}

#controlslider {
	position:absolute;
	z-index:100;
	left:50%;
	bottom:-1.85em;
	border:0.125em solid #666;
	background-color:#333;
	padding:.25em .25em 2em .25em;
	width:9em;
	height:2em;
	margin-left:-4.5em;
	border-radius: 1em;
	opacity: 0.8;
}
#prevslider,
#playstopslider,
#nextslider {
	width: 1em;
	display: inline-block;
	background-color:#333;
	border: 0.125em solid <?=$colors['text']?>;
	padding:.25em .5em;
	margin:.25em 0;
}

#controlslider2 {
	position: absolute;
	z-index: 100;
	top: -1.2em;
	right: -1.2em;
	width: 3.5em;
	height: 3.5em;
	border:0.125em solid #666;
	background-color:#333;	
	border-radius: 1.5em;
	opacity: 0.8;	
	padding: 1em 1em .5em .5em;	
}
body#slideshow #closeslider {
	font-size: 3em;
	font-weight: bold;
	border: none;
	width: 2em;
	line-height: 1.2em;
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
}

?>
