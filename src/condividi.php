<?

function condividi_content_top()
{
	global $fotopage;
	global $urls;
	global $masks;

	$fotofile = isset($_GET['foto']) ? $_GET['foto'] : null;

	$baseurl = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
	$url = $baseurl.($fotopage ? sprintf($masks['fotopageurl'],$fotofile) : '');
	$title = $fotopage ? plugins_rawexec('thumb_title',$fotofile.'.jpg') : plugins_rawexec('title');
	$title = urltitle($title,' ');

	condividi_div($title,$url);
}

function condividi_div($title,$url)
{
?>
  <div id="condividi">
	<label>share <input type="text" onclick="javascript:this.size = this.value.length-14;this.select();" onblur="javascript:this.size=8" size="8" title="Copia e condividi questa pagina" maxlength="<?=strlen($url)-2?>" value="<?=$url?>" readonly="readonly" /></label>
  </div>
<?
}

function condividi_css()
{
	global $colors;

?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>

#condividi {
	text-align:right;
	float:right;
	clear: both;
	padding: 2px;
	margin: .25em 0;
}

#condividi label {
	font-size:1em;
	font-weight: bold;
	padding: 0;
	margin: 0;
}
#condividi input {
	line-height: 1em;
	padding: 0;
	margin: 0 -.125em;
}


#fotopage #condividi {
  position: absolute;
  top: 2em;
  right: 4.25em;
  background:<?=$colors['bgfotopage']?>;
  z-index: 100;
}

<? if(false): ?>
</style>
<? endif;
}

?>
