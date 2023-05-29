<?

/*
richiede comando find di linux
*/
function lastupload_init()
{
	global $dirs;
	global $opts;

	$opts['lastuploadmaxfordir'] = 3;
	$opts['lastuploadmax'] = 35;
	$opts['lastuploadsize'] = 60;
	$dirs['lastuploadlist']= $dirs['data'].'_lastupload.txt';
}

function lastupload_genlist()
{
	global $dirs;
	global $opts;

	$cmd =  '/usr/bin/find $1 -type f -name "*.jpg" -not -name "_*" -not -path "*'.$opts['thumbdirname'].'*" -not -regex ".*_trash/.*" -printf "%TY-%Tm-%Td %p\n" | sort -k1 -r'.
			' | head -n'.$opts['lastuploadmax'].
			' > '.$dirs['lastuploadlist'];
	//TODO usare $nolist
	system($cmd);
}

function lastupload_thumbremote($fotofile)
{
	global $dims;
	global $opts;
	global $recache;
	
	$dims['tnsize'] = $opts['lastuploadsize'];
	$dims['tnmargin'] = 2;
	$opts['thumbquad']  = 1;
	$opts['thumbround'] = 1;
	$opts['thumbcut'] = 1;
	$opts['thumbinterlace'] = 0;
	$opts['qualit'] = 90;
	
	echo thumb_link($fotofile);
}

function lastupload_content_top()
{
	global $index;
	global $fotopage;

	if(!$index or $fotopage) return false;
//	giorni,ore,minuti
	cache_exec(array(1,0,0),'lastupload_gencontenttop');
}

function lastupload_gencontenttop()
{
	global $dirs;
	global $urls;
	global $opts;
	global $dims;

#	if(!file_exists($dirs['lastuploadlist']))
	lastupload_genlist();

	$ll = file($dirs['lastuploadlist'],FILE_IGNORE_NEW_LINES);
	$ff = array();
	$n = 0;
	$mf = $opts['lastuploadmaxfordir'];

	?>
	<h4 id="lastuploadlabel">Ultime foto:</h4>
	<div id="lastuploadlist">
	<?
	foreach($ll as $l)
	{
		if($n++>$opts['lastuploadmax']) break;
		#$d = current(explode(' ',$l));
		$p = next(explode(' ',$l));
		$d = str_replace('./','',dirname($p)).'/';
		$f = basename($p);
		$fotofile = decript_filename($f);

		$url = 'http:/'.'/'.$_SERVER['SERVER_NAME'].$urls['current'].$d."?ajax=lastupload&func=thumbremote&file=".$fotofile;
		echo @file_get_contents($url);
		//soluzione temporanea!!!
	}
	?></div><?
}

function lastupload_css()
{
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#lastuploadlist {
	clear:both;
	float:left;
}
#lastuploadlist * {
	float:left;
	margin:0;
	padding:0;
}
#lastuploadlabel {
	clear: both;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

?>
