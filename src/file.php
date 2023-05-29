<?

function file_init()
{
  global $fileidfoto;

  $fileidfoto = true;  //mostra numero accanto alla foto
}

function file_rglob($sDir, $sPattern, $nFlags = NULL) 
{ 
	$sDir = escapeshellcmd($sDir); 
	$aFiles = glob("$sDir/$sPattern", $nFlags);
	$dd = array_diff(glob("$sDir/*", GLOB_ONLYDIR), glob("$sDir/_*",GLOB_ONLYDIR));
	
	foreach ($dd as $sSubDir) 
	{ 
		$aSubFiles = file_rglob($sSubDir, $sPattern, $nFlags); 
		$aFiles = array_merge($aFiles, $aSubFiles); 
	}
	return $aFiles;
}

function file_countfiles($dir='.') 
{
	global $opts;
	$ff = array_diff(file_rglob($dir,'*.jpg'), file_rglob($dir,'_*'));
	$ff = array_diff($ff, file_rglob($dir,$opts['thumbdirname'].'/*'));
	$ff = array_diff($ff, file_rglob($dir,$opts['datadirname'].'/*'));
    return count($ff);
}

function file_countdirs($dir='.')
{
	global $opts;
	$dd = array_diff(file_rglob($dir,'*',GLOB_ONLYDIR), file_rglob($dir,'_*',GLOB_ONLYDIR));
	$dd = array_diff($dd, file_rglob($dir,$opts['thumbdirname'], GLOB_ONLYDIR));
	$dd = array_diff($dd, file_rglob($dir,$opts['datadirname'], GLOB_ONLYDIR));
    return count($dd);
}

function file_thumb($fotofile)
{
	global $imgfiles;
	global $fileidfoto;

	if(is_dir($fotofile))
	{
	
		#$albums = count(rgetdirs($fotofile));
		#$photos = 1+count(rgetfiles($fotofile));
		$albums = file_countdirs($fotofile);
		$photos = file_countfiles($fotofile);
		
		if($albums==0 and $photos==0) return false;
		
		?><div class="numphotos"><?
		if($photos>0) $n[] = ($photos)."<small> foto </small>";
		if($albums>0) $n[] = ($albums)."<small> album </small>";
		
		echo implode('<br />',$n);
		?></div><?
	}
	elseif($fileidfoto and is_file(getcript_filename($fotofile)) )
	{
		?><div class="nphoto"><?=(thumbid($fotofile)+1)?></div><?
	}
	return 'Numero foto';
} # */

/*
function file_thumb_title($fotofile)
{
    if(!is_dir($fotofile)) return;
?>&nbsp;<span style="font-size:smaller; font-weight:normal;">(<?=count(getfiles($fotofile))-1?>)</span><?
} # */

function file_content_top()
{
	global $fotopage;
	global $imgfiles;

	if($fotopage):
		  ?><div class="nphoto"><?=(thumbid($_GET['foto'].'.jpg')+1)?> di <?=count($imgfiles)?></div><?
		  return false;
	endif;

	$albums = count(rgetdirs());
	$photos = 1+count(rgetfiles());

	if($albums==0 and $photos==0) return false;

    if($albums>0) $n[] = '<span>'.($albums)."<small> album</small></span>";
    if($photos>0) $n[] = '<span>'.($photos-1)."<small> foto</small></span>";

    ?>&nbsp;<span id="nalbumphoto" class="numphotos"><?=implode('<br />',$n)?></span><?
} # */

function file_css()
{
  global $colors;
  global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>

#nalbumphoto {
  float: right;
  clear:both;
  background: <?=$colors['bgbox']?>;
  margin-bottom:.5em;
}

span.file {
    font-size: 0.75em;
    font-size: 0.9em;
	color: <?=$colors['text']?>;
}
.numphotos {
	display:block;
	position:relative;
    padding: .25em;
    font-weight:bold;
    text-align: right;
    vertical-align:middle;
    background: <?=$colors['background']?>;
    border: 2px solid <?=$colors['border']?>;
    color: <?=$colors['text']?>;
	border-radius: .4em;
}
.numphotos small {
    letter-spacing:0.00125em;
    font-weight:normal;
}
.album .numphotos {
    position: absolute;
    top: 1.18em;
    right: 0.3em;
	opacity: 0.7;
}
.album.list .numphotos {
	position:static;
	float:left;
	display:inline;
	border:none;
	background:none;
}
.album.list .numphotos br {
	float:left;
}
.album.active .numphotos {
	filter: alpha(opacity=100);
	opacity: 1;
}
.nphoto {
	display:none;
}
#fotopage  .nphoto,
.active .nphoto {
	display:block;
    position: absolute;
    margin:0;
	top: .2em;
    right: .2em;
	line-height: 1.2em;
	min-width:1.2em;
    font-weight:bold;
    text-align: center;
	color: <?=$colors['hover']?>;
	border-radius: 0.4em; /* css3 */
}
#fotopage .nphoto {
	top: -.25em;
	left:50%;
	min-width:4em;
	margin-left:-2em;	
	right:auto;
	float:left;
    font-size:.8em;
    padding: .5em .25em .25em .25em;
	background: <?=$colors['bgbox']?>;
	color: <?=$colors['text']?>;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

?>
