<?

function popupwindow_init()
{
  global $images;
  global $dirs;
  global $urls;
  global $start;
  global $dims;
  
  $images['popupicon']['dir'] = $dirs['cache_base'].'_popupicon.png';
  $images['popupicon']['url'] = $urls['cache_base'].'_popupicon.png';
  
  $dims['popuptnsize'] = max($dims['tnsizes']);
  
  if(isset($_GET['popup']))
    $start = 'popupwindow';
}

//genera la finestra di popup che contienre l'immagine grande   
function popupwindow_start()
{
	global $dims;
	global $masks;
	global $urls;
	global $opts;
  
    $fotofile = trim($_GET['popup']);

    if(!is_file(getcript_filename($fotofile)))
	{
	  echo 'File Not Found';
	  return true;
	}
    
	$dims['tnsize'] = $dims['popuptnsize'];
	$dims['tnmargin'] = 0;
	$opts['thumbquad'] = 0;
	$opts['thumbround'] = 0;
	$opts['thumbcut'] = 0;
	$opts['thumbinterlace'] = 1;

  	list($w,$h) = thumbsize($fotofile);

    head();

?>
<body id="popup">
<img class="imgfoto"  width="<?=$w?>" height="<?=$h?>" src="<?=thumburl($fotofile)?>" alt="<?=thumbalt($fotofile)?>" />
<?
	js();
?>
<script type="text/javascript">
ULG.popupwindowsizes = {w: <?=$w?>, h: <?=$h?> };
</script>
<?
	tail();
}

function popupwindow_js()
{
?>
<? if(false): ?>
<script>
<? endif; ?>

add_thumb_event(function(obj) {
    $(".icon.popupwindow",obj).click(function() {
        wopen(obj);
        return false; //senno scrolla la pagina
    });
});

function wopen(obj)
{
	var fotofile = $(obj).attr('id');

	var w = ULG.dims.tnsizes[ULG.dims.tnsizes.length-2],
		h = w,
		t = (window.screen.height-h)/2,
		l = (window.screen.width-w)/2,
		option = "top="+t+",left="+l+",toolbar=no,scrollbars=auto,location=no,status=0,width="+w+",height="+h;
	
	ULG.popwindow = window.open("?popup="+fotofile,"",option);

	ULG.popwindow.onload = function() {
		var w = ULG.popwindow.ULG.popupwindowsizes.w,
			h = ULG.popwindow.ULG.popupwindowsizes.h,
			t = (window.screen.height-h)/2,
			l = (window.screen.width-w)/2;
		
		if($.browser.webkit){ h+=48; w+=8; }
		else if($.browser.mozilla){ h+=28; w+=0; }
		else if($.browser.msie){ h+=68; w+=8; }

		ULG.popwindow.resizeTo(w,h);
		ULG.popwindow.moveTo(l,t);
	};
}
<? if(false): ?>
</script>
<? endif; ?>
<?
}

function popupwindow_thumb_menu($fotofile)
{
  global $images;
    
  if(is_dir($fotofile)) return;
    
?><a class="icon popupwindow" href="#" title="Apri in nuova finestra"><span>Popup</span><img src="<?=$images['popupicon']['url']?>" /></a><?
  return 'Finestra Popup';
} # */

function popupwindow_css()   //in futuro generare i dati exif all'interno di questa funzione.. ua volta per tutte
{
?>
body#popup {
	margin:0;
	padding:0;
	overflow:hidden;
}
body#popup .imgfoto {
	margin:0;
	border:none;
}
<?
}

function popupwindow_cache()   //in futuro generare i dati exif all'interno di questa funzione.. ua volta per tutte
{
  global $images;
    
  require('popupwindow.cache.php');

  put_contents($images['popupicon']['dir'],$icon1);
}

?>