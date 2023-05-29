<?

function crop_init()
{
	global $images;
	global $urls;
	global $dirs;
	global $dims;

	$images['cropback']['dir'] = $dirs['cache_base'].'_cropback.gif';
	$images['cropback']['url'] = $urls['cache_base'].'_cropback.gif';
	
	$images['cropicon']['dir'] = $dirs['cache_base'].'_cropicon.png';
	$images['cropicon']['url'] = $urls['cache_base'].'_cropicon.png';
	
	$dims['cropsize'] = back_tnsize(max($dims['tnsizes']));
}


if($admin):

function crop_submit()
{
	global $imgfiles_decript;
	global $dims;
	global $opts;
	global $admin;
	
	$fotofile = basename($_GET['file']);
	
	$criptfotofile = getcript_filename($fotofile);

	$cropfile = 'crop'.(microtime()*1000000).'_'.$fotofile;

	//$cropfilecript = check_filename($cropfile);  non serve usa la stessa crittografia per ogni copia croppata

	$imgs = ulgimagecreate($criptfotofile);
	$w = imagesx($imgs);
    $h = imagesy($imgs);
	
	if($w>$h)
	  $cropscale = $w/$dims['cropsize'];
	elseif($w<=$h)
	  $cropscale = $h/$dims['cropsize'];

	//fattore di scala con cui deve essere tagliata l'immagine	
	#$cropfile = null;
    #$cropscale = 1;

	$sx = $_GET['x'] * $cropscale;
	$sy = $_GET['y'] * $cropscale;
	$sw = $_GET['w'] * $cropscale;
	$sh = $_GET['h'] * $cropscale;
	
	$imgd = imagecreatetruecolor($sw,$sh);
	
    #imagecopyresampled(resource dst_im, resource src_im,
	#                   int dstX       , int dstY       , int srcX, int srcY,
	#                   int dstW       , int dstH       , int srcW, int srcH) > bool
	imagecopyresampled($imgd, $imgs,
	                   0    , 0    , $sx, $sy,
	                   $sw  , $sh  , $sw, $sh);
	imagejpeg($imgd, $cropfile, 95);
	$cropfile = check_filename($cropfile,true);  //critta il nome file
	imagedestroy($imgd);

	$opts['thumbquad'] = 0;
	$opts['thumbcut'] = 0;
	$opts['thumbround'] = 0;

	thumb_wrap($cropfile); //thumb nuova copia croppata	
	cache_reset();//rigenera la cache html della pagina
}

function crop_thumb_menu($fotofile)
{
    global $images;
  
	$fotofile = basename($fotofile);
	
	if(is_dir($fotofile)) return false;  //per ora non usare per gli album

?><a class="icon crop" href="#" title="Ritaglia la foto"><span>Ritaglia</span><img src="<?=$images['cropicon']['url']?>" /></a><?
  return 'Ritaglia la foto';
} # */

function crop_head_js()
{
    global $urls;
?>
<script  src="<?=$urls['plugins']?>jquery.jcrop.min.js"></script>
<?
} # */

function crop_thumbnail($fotofile)  //richiamata quasi sempre attraverso ajax
{
	global $dims;
	global $opts;
	global $urls;
	global $images;
	
	$dims['tnsize'] = $dims['cropsize'];
	$dims['tnmargin'] = 0;
	$opts['thumbquad'] = 0;
	$opts['thumbcut'] = 0;
	$opts['thumbround'] = 0;
	
	#thumb_link($fotofile);  //funzione interna di ulg
	
	list($w,$h) = thumbsize($fotofile);

?>
<div class="crop_wrap loading" style="width:<?=$w?>px; height:<?=$h?>px; z-index:100">
	<form class="formcrop" action="<?=$urls['action']?>" method="post">
		<input type="hidden" name="submit" value="crop" />
		<input type="hidden" id="filename" name="file" />
		<input type="hidden" id="x" name="x" />
		<input type="hidden" id="y" name="y" />
		<input type="hidden" id="w" name="w" />
		<input type="hidden" id="h" name="h" />
		<input type="submit" value="Ritaglia" />
		<input type="reset" value="Annulla" />
		<div class="croploader"><img src="<?=$images['jqueryloadingcircle1']['url']?>" /></div>
	</form>
	<img class="cropbox" src="<?=thumburl($fotofile)?>" alt="<?=thumbalt($fotofile)?>" title="" />    
</div>	
<?
}

endif; //fine admin


function crop_js()
{
  global $lastslides;
  global $urls;
  global $colors;
  global $dirs;
?>
<? if(false): ?>
<script>
<? endif; ?>

var cropwraptmp = [];

add_thumb_event(function(obj) {
    $(".icon.crop", obj).click(function() {
      showfotocrop(obj);
      return false;
    });
});

function updateCoords(c)
{
	$('#filename').val(c.filename);
	$('#x').val(c.x);
	$('#y').val(c.y);
	$('#w').val(c.w);
	$('#h').val(c.h);
};

function crop(objwrap,fotofile)
{    
	$('.cropbox',objwrap).Jcrop({
		onSelect: updateCoords
	});
	
	$('.formcrop', objwrap).submit(function() {
		
		var loa$ = $(this).children('.croploader').show();
		
		$.get(ULG.urls.action,
		{
			ajax: 'crop',
			func: 'submit',
			file: fotofile,
			x: $('#x',this).val(),
			y: $('#y',this).val(),
			w: $('#w',this).val(),
			h: $('#h',this).val()
		},
		function(data) {
			//alert(data);		
			loa$.hide();
			thumb_add($(data));
			ulgalert("Foto ritagliata");
		});
		
		return false;
	});
	
	$('.formcrop input:reset', objwrap).click(function() {

	  $('.cropbox',objwrap).parent().remove();
	  
	  objwrap.replaceWith(cropwraptmp[fotofile]);
	  
	  thumb_event($('.thumb',cropwraptmp[fotofile]));
	  return false;
	});
}

function showfotocrop(obj)
{
  var fotofile = $(obj).attr('id');
  var thumb_wrap = obj.parents('.thumb_wrap'); 
  
  obj.unbind().removeClass('active');  //annulla animazioni thumb

  cropwraptmp[fotofile] = thumb_wrap.clone();
  
  thumb_wrap.empty().addClass('loading');
  
  $.get(ULG.urls.action,
  {
	 ajax: 'crop',
	 func: 'thumbnail',
	 file: fotofile
  },
  function(resp) {
	thumb_wrap.removeClass('loading');
	var cropdiv = $(resp);
	
	cropdiv.appendTo(thumb_wrap);
	thumb_wrap.css({width: cropdiv.css('width'),height: cropdiv.css('height')});
	
	crop(thumb_wrap,fotofile);
	
	ulgalert("Ora puoi ritagliare la foto");
  });
  return false;
}

<? if(false): ?>
</script>
<? endif; ?>
<?
} # */

function crop_css()
{
	global $images;
	global $colors;
	global $dims;
?>	
<? if(false): ?>
<style type="text/css">
<? endif; ?>

/* Fixes issue here http://code.google.com/p/jcrop/issues/detail?id=1 */
.jcrop-holder {
	text-align: left;
}

.jcrop-vline,
.jcrop-hline {
	font-size: 0;
	position: absolute;
	background: white url('<?=$images['cropback']['url']?>') top left repeat;
}
.jcrop-vline { height: 100%; width: 1px !important; }
.jcrop-hline { width: 100%; height: 1px !important; }
.jcrop-handle {
	font-size: 1px;
	width: 7px !important;
	height: 7px !important;
	border: 1px #eee solid;
	background-color: #333;
	*width: 9px;
	*height: 9px;
}

.jcrop-tracker {
	*background-color: gray;
	width: 100%; height: 100%;
}

.custom .jcrop-vline,
.custom .jcrop-hline
{
	background: yellow;
}
.custom .jcrop-handle
{
	border-color: black;
	background-color: #C7BB00;
	border-radius: 3px;
}

.jcropper-holder { border: 1px black solid; }

.jcExample
{
	background: white;
	width: 700px;
	font-size: 80%;
	margin: 3.5em auto 2em auto;
	*margin: 3.5em 10% 2em 10%;
	border: 1px black solid;
	padding: 1em 2em 2em;
}

.formcrop {
	white-space:nowrap;
	margin-top:0.25em;
}
.crop_wrap {
	z-index:40;
	border:0.125em solid <?=$colors['border']?>;
	padding:0;
}
.crop_wrap input {
	cursor:pointer;
}
.croploader {
	display:none;
	float:right;
	padding-right:1em;
}


<? if(false): ?>
</style>
<? endif; ?>
<?
}

function crop_cache()   //in futuro generare i dati exif all'interno di questa funzione.. ua volta per tutte
{
  global $images;
	
  require('crop.cache.php');

  put_contents($images['cropback']['dir'],$back1);
  put_contents($images['cropicon']['dir'],$icon1);  
}
