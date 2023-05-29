<?

function captcha_init()  //verifica codice captcha
{
	global $captcha;
	global $spammer;
	global $alerts;
	global $dirs;
	global $urls;
	global $colors;

	$spammer = true;  //determina se i form sono stati compilati da uno spammer bot
	$captcha;  //oggetto captcha
	$captchacode;  //codice genrato
	//variabili globali

	$captcha = new captcha($dirs['cache_base'].'_captcha');

	$captcha->setColors(array(hexrgb($colors['background']),hexrgb($colors['border'])));

	if(isset($_GET['captcha']))
	  $captcha->show();
	elseif(isset($_GET['prune']))
	  $captcha->prune();

	if(isset($_POST['captchacode']) or isset($_GET['captchacode']))
	  $spammer = captcha_check() ? false : true;
} #*/

function captcha_check()
{
  global $captcha;
  global $alerts;
  global $spammer;

  if(isset($_POST['captchacode']))
    $code = trim($_POST['captchacode']);
  elseif(isset($_GET['captchacode']))
    $code = trim($_GET['captchacode']);
  else
    $code = '';

  if($code=='')
    $alerts[] = 'Inserisci il codice di sicurezza di '.$captcha->code_length.' lettere';
  elseif($captcha->check($code))
    $spammer =  false;
  else
    $alerts[] = 'Il codice di sicurezza &egrave; sbagliato';

  return !$spammer;
}

function captcha_cache()
{
  global $captcha;
  global $dirs;
  global $opts;

  if(!is_dir($captcha->data_directory))
	@mkdir($captcha->data_directory,CHMOD);
}

function captcha_css()
{
  global $colors;
  global $urls;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
.captcha {
  margin: 1em 0;
  float:left;
  clear:both;
}
.captcha a {
	font-size:small;
}
.captcha big,
.captcha img {
	vertical-align: middle;
	font-size:2em;
}
.captcha input {
	text-transform:uppercase;
	vertical-align:bottom;
	width:4em;
	margin:0.25em 0;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function captcha_js()
{
  global $urls;
  global $images;
?>
<? if(false): ?>
<script>
<? endif; ?>

add_panel_event(function(obj) {

	if(obj.attr('id')=='panel_guestbook')
    {
		$('.captcha a').click(function() {
		  captcha_refresh($(this).parents('form'));
		  return false;
		});
    }
});

function captcha_refresh(form)  //rigenera immagine e codice in .../_captcha/
{
  $('.captcha',form).blur().children('img').attr({src: ULG.urls.action+'&captcha&'+ulgrand()});
  $('.captcha input',form).focus().val('');
}

<? if(false): ?>
</script>
<? endif; ?>
<?
}

function captcha_form()
{
  global $captcha;
  global $urls;
?>
    <div class="captcha">
	<a href="#" title="Rigenera Codice">Rigenera codice di <?=$captcha->code_length?> lettere</a><br />
	<img src="<?=$urls['action']?>&amp;captcha" alt="Codice" />
	<input type="text" name="captchacode" class="captchacode" size="<?=$captcha->code_length?>" maxlength="<?=$captcha->code_length?>" title="Codice di sicurezza" />
	</div>
<?
}


class captcha
{

  var $code_length = 4;

  var $image_width = 110;
  var $image_height = 40;

  var $image_bg_color = array(240, 240, 240);  //(r,g,b)
  var $dot_color = array(240, 240, 240);
  var $line_color = array(100, 100, 100);
  var $text_color = array(
                    array(200, 100, 180),
                    array(120, 200, 200),
                    array(220, 100, 100),//rosso
                    array(100, 220, 100),//verde
                    array(60, 60, 200),//blue
                    array(220, 100, 220),//viola
					);

  var $data_directory;
  var $prune_minimum_age = 15;  //(in minutes)

  var $hash_salt = "fg7hg3yg3fd90oi4i";

  var $im;
  var $code;
  var $code_entered;
  var $correct_code;

  function captcha($dir='_captcha')
  {
    $this->data_directory = $dir;
  }

  function setColors($arraycolors)
  {
	$this->image_bg_color = $arraycolors[0];  //sfondo
	$this->line_color     = $arraycolors[1];  //bordo
  }

  function check($code)
  {
    $this->code_entered = $code;
    $this->validate();
    return $this->correct_code;
  }

  function show()
  {
    $this->code = $this->generateCode($this->code_length);

	$w2 = $this->image_width*0.6;
	$h2 = $this->image_height*0.6;
	$this->im = imagecreatetruecolor($w2, $h2);
	$linecolor = imagecolorallocatealpha($this->im, $this->line_color[0], $this->line_color[1], $this->line_color[2],0);
    $bgcolor = imagecolorallocate($this->im, $this->image_bg_color[0], $this->image_bg_color[1], $this->image_bg_color[2]);
	imagefill($this->im, 0, 0, $bgcolor);  //sfonda l'immagine! :)

	$this->drawLines();
	$this->drawDots();
	$this->drawWord();

	$w = $this->image_width;
	$h = $this->image_height;
	$imt = imagecreatetruecolor($w, $h);
	imagecopyresampled($imt,$this->im,0,0,0,0,$w,$h,$w2,$h2);
	$this->im = $imt;

	ulgimagerectangleround($this->im, 5, $linecolor, $bgcolor);  //mia funzione per fare cornici rettangolari con spigoli arrotondati

	$this->saveData();
    $this->output();
	exit(0);
  }

  function drawLines()
  {
  	imagesetthickness($this->im, 2);
    $linecolor = imagecolorallocate($this->im, $this->line_color[0], $this->line_color[1], $this->line_color[2]);

    $w = imagesx($this->im);
    $h = imagesy($this->im);
    for($n=7; $n<$w; $n+=6)  //vertical lines
	  {
  	  $randcolor = $this->text_color[array_rand($this->text_color)];
  	  $linecolor = imagecolorallocatealpha($this->im, $randcolor[0], $randcolor[1], $randcolor[2],20);
        imageline($this->im, $n, 0, $n, $h, $linecolor);
        imageline($this->im, 0, $n, $w, $n, $linecolor);
    }
/*
//linee oblique
  	imagesetthickness($this->im, 3);
    $linecolor = imagecolorallocate($this->im, $this->line_color[0], $this->line_color[1], $this->line_color[2]);

	for($x = -($this->image_height); $x < $this->image_width; $x += 8)
		imageline($this->im, $x, 0, $x + $this->image_height, $this->image_height, $linecolor);

	for($x = $this->image_width + $this->image_height; $x > 0; $x -= 8)
		imageline($this->im, $x, 0, $x - $this->image_height, $this->image_height, $linecolor);
# */
  }

  function drawDots()
  {
    $w = imagesx($this->im);
    $h = imagesy($this->im);

    $dotcolor = imagecolorallocate($this->im, $this->image_bg_color[0], $this->image_bg_color[1], $this->image_bg_color[2]);

	  $maxdot = ($h*$w)/8;

  	for($n=0; $n<$maxdot; $n++)
  	{
  	    $x = rand(0,$w);
  		$y = rand(0,$h);
  		imagesetpixel($this->im, $x, $y, $dotcolor);
  		imagesetpixel($this->im, $x+1, $y, $dotcolor);
  		imagesetpixel($this->im, $x, $y+1, $dotcolor);
  		imagesetpixel($this->im, $x+1, $y+1, $dotcolor);
  	}
  }

  function drawWord()
  {
    $w = imagesx($this->im);
    $h = imagesy($this->im);

  	$fontcode = 5;
    $x = -8;
	  $y = 1;
    $strlen = strlen($this->code);

    $ymin = 1;
    $ymax = $h-($fontcode*3);

    for($i = 0; $i < $strlen; ++$i)
  	{
        $randcolor = $this->text_color[array_rand($this->text_color)];
        $shadow = $this->image_bg_color;
        $font_color = imagecolorallocate($this->im, $randcolor[0], $randcolor[1], $randcolor[2]);
  	  $shadow_color = imagecolorallocate($this->im, $shadow[0], $shadow[1], $shadow[2]);

        $y = rand($ymin, $ymax);
  	  $x += 1+rand($fontcode*3-$i, $fontcode*5-$x/10)-$fontcode;
  	  //non toccare piu questa equazione!!!
  	  $sco = 1;
     	  imagestring($this->im,$fontcode,$x+$sco,$y+$sco,$this->code{$i},$shadow_color);
     	  imagestring($this->im,$fontcode,$x-$sco,$y-$sco,$this->code{$i},$shadow_color);
     	  imagestring($this->im,$fontcode,$x+$sco,$y-$sco,$this->code{$i},$shadow_color);
     	  imagestring($this->im,$fontcode,$x-$sco,$y+$sco,$this->code{$i},$shadow_color);
  	  //bordo bianco

  	  imagestring($this->im,$fontcode,$x,$y,$this->code{$i},$font_color);
      }
  }

  function generateCode($len)
  {
    $code = "";

    for($i = 1; $i <= $len; ++$i)
      $code .= chr(rand(65, 90));

	   $code = str_replace('O',rand(80, 90),$code);
	//toglie le 'o'

    return $code;
  }

  function output()
  {
    header("Expires: Sun, 1 Jan 2000 12:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-Type: image/png");
    imagepng($this->im);
    imagedestroy($this->im);
  }

  function saveData()
  {
    if(!is_dir($this->data_directory))
	  captcha_cache();

    $filename = md5($this->hash_salt . $_SERVER['REMOTE_ADDR']);
    $fp = fopen($this->data_directory . "/" . $filename, "w+");
    fwrite($fp, md5( $this->hash_salt . strtolower($this->code) )  );
    fclose($fp);
    if(defined('CHMOD'))
      @chmod($filename, CHMOD);
  }

  function validate()
  {
    $filename = md5($this->hash_salt . $_SERVER['REMOTE_ADDR']);

	if(!is_file($this->data_directory . "/" . $filename))
	{
	  $this->correct_code = false;
	  return;
	}

    $enced_code = trim(implode(file($this->data_directory . "/" . $filename)));

    $check = md5($this->hash_salt . strtolower($this->code_entered));

    if($check == $enced_code)
	{
      $this->correct_code = true;
      @unlink($this->data_directory . "/" . $filename);
    }
	else
      $this->correct_code = false;
  }

  function checkCode()
  {
    return $this->correct_code;
  }

  function prune()
  {
    if($handle = @opendir($this->data_directory))
	{
      while(($filename = readdir($handle)) !== false)
	  {
        if(time() - filemtime($this->data_directory . "/" . $filename) > $this->prune_minimum_age * 60)
          @unlink($this->data_directory . "/" . $filename);
      }
      closedir($handle);
    }
  }

} //end class

?>
