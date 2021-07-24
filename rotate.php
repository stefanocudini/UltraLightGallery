<?
		
function rotate_init()
{
	global $images;
	global $dirs;
	global $urls;  
	global $dims;
	global $opts;
	global $admin;
	global $public;
	global $colors;
	
	$opts['rotatebgcolor'] = hexrgb($colors['background']);

	$opts['rotatebackup'] = false;//crea backup delle foto originale
	
	$images['iconrotatedx']['dir'] = $dirs['cache_base'].'_iconrotatedx.png';
	$images['iconrotatesx']['dir'] = $dirs['cache_base'].'_iconrotatesx.png';
	
	$images['iconrotatedx']['url'] = $urls['cache_base'].'_iconrotatedx.png';
	$images['iconrotatesx']['url'] = $urls['cache_base'].'_iconrotatesx.png';
}

function rotate_thumbnail($fotofile)
{
	global $dims;
	global $ajax;
	global $colors;
	global $recache;
	global $opts;
	global $dirs;
	
	$criptfotofile = getcript_filename($fotofile);

    if( !isset($_GET['rotatetype']) or !file_exists($criptfotofile) ) return false;
	
	$rtnsize = (int)$_GET['rtnsize'];
	
	$dims['tnsize']= check_tnsize($rtnsize);
	
	if($_GET['rotatetype']=='dx')
		$angle = 270;
	elseif($_GET['rotatetype']=='sx')
		$angle = 90;

	if(function_exists('imagerotate'))	
		$bigimage = ulgimagecreate($criptfotofile);

	@rename($criptfotofile,'_'.$criptfotofile);  //rende invisibile l'originale

	$orifotofile = $dirs['current'].'_'.$criptfotofile;
	
	thumbdel($fotofile);	//elimina tutte le thumbnail dell'originale

    cache_reset();//rigenera la cache html della pagina

	if( function_exists('imagerotate') )
	{
    	list($r,$g,$b) = $opts['rotatebgcolor'];
	    $back = imagecolorallocate($bigimage, $r, $g, $b);
    }
    else
    	$back = false;

#debug_getmicrotime();
	if( function_exists('imagerotate') )
		$rotimage = imagerotate($bigimage, $angle, $back);//circa 100ms
	else
		$rotimage = rotate_imagerotateImagick($orifotofile, $angle, $back, true);//circa 2s(apre direttamente file jpg)		
#	$rotimage = rotate_imagerotateImagick($bigimage, $angle, $back);//circa 2s(apre immagine gd)
#	$rotimage = rotate_imagerotateRaw($bigimage, $angle,$back);//circa 8s

	if($opts['rotatebackup']==false)
		@unlink($orifotofile);

	imagejpeg($rotimage, $fotofile, 95);
	@chmod($fotofile, CHMOD);
	imagedestroy($rotimage);
	check_filename($fotofile,true);  //critta il nuovo file, serve True

	$recache = true;	//forza rigenerazione miniatura
    thumb($fotofile);    
}

/*function rotate_thumb($fotofile)
{
	if(isajax())
		echo debug_getmicrotime(false,'RotateTime');
}//*/

function rotate_thumb_menu($fotofile)
{
	global $images;
	global $dims;
	global $admin;
	global $opts;
	
	if(is_dir($fotofile) or !$admin) return false;  //per ora non usare per gli album

?><a class="icon rotatedx" href="#" title="Ruota in senso orario"><span>Rotate Dx</span><img src="<?=$images['iconrotatedx']['url']?>" /></a><?
?><a class="icon rotatesx" href="#" title="Ruota in senso antiorario"><span>Rotate Sx</span><img src="<?=$images['iconrotatesx']['url']?>" /></a><?

  return 'Ruota';
} # */



function rotate_js()
{
  global $dims;
  global $urls;
  global $zoommenu;
?>
<? if(false): ?>
<script>
<? endif; ?>

add_thumb_event(function(obj) {

     if(obj.is(".photo"))  //gli album, non hanno lo zoom per il moemento
	 {
		$(".icon.rotatedx", obj).click(function() {
			rotate(obj,'dx');
			$(this).blur();
			return false; //senno scrolla la pagina     
		});

		$(".icon.rotatesx", obj).click(function() {
			rotate(obj,'sx');
			$(this).blur();
			return false; //senno scrolla la pagina     
		});
    }
});

function rotate(obj,tipo)
{
    var fotofile = $(obj).attr('id');
    var thumb_wrap = obj.parent();
	var thumb_link = $('.thumb_link',obj);
	var imglink = $('a:first',thumb_link);
	var imgthumb = $('.imgthumb',obj);
	var tn = Math.max( imglink.width(),
	                  imglink.height() );  //cosi vale anche per thubnail rettangolari
		
	
    obj.addClass('loading');
    thumb_link.css('visibility','hidden');
	
    $.get(ULG.urls.action,  //forse fare in post
         {
			 ajax: 'rotate',
			 func: 'thumbnail',
			 file: fotofile,
			 rotatetype: tipo,  // 'dx' or 'sx'
			 rtnsize: tn
		 },
		 function(resp) {
			var newobj = $(resp);
			thumb_event(newobj);
			obj.replaceWith(newobj);
			thumb_wrap.width(newobj.width());

			thumb_wrap.removeClass('loading');

         });
}

<? if(false): ?>
</script>
<? endif; ?>
<?
}

function rotate_cache()
{
  global $images;

  require('rotate.cache.php');

  put_contents($images['iconrotatedx']['dir'], $icondx);
  put_contents($images['iconrotatesx']['dir'], $iconsx);
}

//function rotate_imagerotateRaw($srcImg, $angle, $bgcolor, $ignore_transparent = 0) {
function rotate_imagerotateImagick($sImage, $angle, $bgcolor=false, $inputjpg=false)
{
	global $opts;
	
	$imagickBin = '/usr/bin/convert';
	if(!file_exists($imagickBin))
		return $sImage;
	
	$angle = "-rotate ".(360-$angle)." ";

	if($bgcolor)
	{
		list($r,$g,$b,$a) = array_values(imagecolorsforindex($sImage, $bgcolor));
		$color = "-background 'rgb($r,$g,$b)' ";
	}
	else
	 	$color = "-background 'rgb(".implode(',',$opts['rotatebgcolor']).")' ";
	
	if($inputjpg and file_exists($sImage))//se l'immagine sorgente e' un file jpg e non una immagine GD
	{
		$fileIn = $sImage;
	}
	else
	{
		$fileIn = '/tmp/imagick_'.rand(10000,99999).'.png';
		imagepng($sImage, $fileIn);//crea file png temporaneo
	}

	$fileOut = '/tmp/imagick_'.rand(10000,99999).'.png';
	
	exec("$imagickBin $angle $color $fileIn $fileOut");

    $new_image = imagecreatefrompng($fileOut);

    unlink($fileOut);    
	if(!$inputjpg)
		unlink($fileIn);

    return $new_image;
}

function rotate_imagerotateRaw($srcImg, $angle, $bgcolor, $ignore_transparent = 0) {
    function rotateX($x, $y, $theta){
        return $x * cos($theta) - $y * sin($theta);
    }
    function rotateY($x, $y, $theta){
        return $x * sin($theta) + $y * cos($theta);
    }

    $srcw = imagesx($srcImg);
    $srch = imagesy($srcImg);

    //Normalize angle
    $angle %= 360;
    //Set rotate to clockwise
    #$angle = -$angle;

    if($angle == 0) {
        if ($ignore_transparent == 0) {
            imagesavealpha($srcImg, true);
        }
        return $srcImg;
    }

    // Convert the angle to radians
    $theta = deg2rad ($angle);

    //Standart case of rotate
    if ( (abs($angle) == 90) || (abs($angle) == 270) ) {
        $width = $srch;
        $height = $srcw;
        if ( ($angle == 90) || ($angle == -270) ) {
            $minX = 0;
            $maxX = $width;
            $minY = -$height+1;
            $maxY = 1;
        } else if ( ($angle == -90) || ($angle == 270) ) {
            $minX = -$width+1;
            $maxX = 1;
            $minY = 0;
            $maxY = $height;
        }
    } else if (abs($angle) === 180) {
        $width = $srcw;
        $height = $srch;
        $minX = -$width+1;
        $maxX = 1;
        $minY = -$height+1;
        $maxY = 1;
    } else {
        // Calculate the width of the destination image.
        $temp = array (rotateX(0, 0, 0-$theta),
        rotateX($srcw, 0, 0-$theta),
        rotateX(0, $srch, 0-$theta),
        rotateX($srcw, $srch, 0-$theta)
        );
        $minX = floor(min($temp));
        $maxX = ceil(max($temp));
        $width = $maxX - $minX;

        // Calculate the height of the destination image.
        $temp = array (rotateY(0, 0, 0-$theta),
        rotateY($srcw, 0, 0-$theta),
        rotateY(0, $srch, 0-$theta),
        rotateY($srcw, $srch, 0-$theta)
        );
        $minY = floor(min($temp));
        $maxY = ceil(max($temp));
        $height = $maxY - $minY;
    }

    $destimg = imagecreatetruecolor($width, $height);
    if ($ignore_transparent == 0) {
        imagefill($destimg, 0, 0, imagecolorallocatealpha($destimg, 255,255, 255, 127));
        imagesavealpha($destimg, true);
    }

    // sets all pixels in the new image
    for($x=$minX; $x<$maxX; $x++) {
        for($y=$minY; $y<$maxY; $y++) {
            // fetch corresponding pixel from the source image
            $srcX = round(rotateX($x, $y, $theta));
            $srcY = round(rotateY($x, $y, $theta));
            if($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
                $color = imagecolorat($srcImg, $srcX, $srcY );
            } else {
                $color = $bgcolor;
            }
            imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
        }
    }
    return $destimg;
}

?>
