<?
function sidebar_content_bottom()
{
	global $fotopage;
	global $imgfiles;
	global $dims;
	global $opts;
	
	if(!$fotopage) return false;
	
	$fotofile = $_GET['foto'].'.jpg';
	
	$deftnsize     = $dims['tnsize'];
	$deftnmargin   = $dims['tnmargin'];
	$defthumbcut   = $opts['thumbcut'];	
	$defthumbquad  = $opts['thumbquad'];
	$defthumbround = $opts['thumbround'];
	$defthumbinter = $opts['thumbinterlace'];

	$dims['tnsize'] = min($dims['tnsizes']);
	$dims['tnmargin'] = 0;
	$opts['thumbcut'] = 1;
	$opts['thumbquad'] = 1;
	$opts['thumbround'] = 0;	
	$opts['thumbinterlace'] = 0;
	list($w,$h) = thumbsize($fotofile);

		
	?><div id="sidebar"><?

	$maxbacknet = 3;
	$totfiles = count($imgfiles);
	$curpos = thumbid($fotofile)+1;
	
	$maxbacknet += $curpos>$totfiles-$maxbacknet ? $curpos-($totfiles-$maxbacknet) : 0;
	
	for($bk=$maxbacknet; $bk>0; $bk--)
	{
		if($b = getback($fotofile,$bk) )
			thumb_link( decript_filename($b) );
		else
			$maxbacknet++;
	}
	?><div class="thumb_link sidebarsel"><?

		plugins_exec('thumb_link', $fotofile);
	
	?></div>
<?

	for($nk=1; $nk<=$maxbacknet; $nk++)
	{
		if($n = getnext($fotofile,$nk))
			thumb_link( decript_filename($n) );
	}

	?></div><?
	$dims['tnsize']   = $deftnsize;
	$dims['tnmargin'] = $deftnmargin;
	$opts['thumbcut'] = $defthumbcut;	
	$opts['thumbquad'] = $defthumbquad;
	$opts['thumbround'] = $defthumbround;
	$opts['thumbinterlace'] = $defthumbinter;	
}


function sidebar_css()
{
  global $colors;
  global $dims;
  
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>

#sidebar {
	position:absolute;
	right:-1.3em;
	padding-right: 1em;
	top:0;
	overflow:hidden;
	width:<?=pixem(min($dims['tnsizes']))+1?>em;
	background: <?=$colors['bgbox']?>;
	border-radius: .5em;
	z-index:10;
}
#sidebar .thumb_link a {
	border:2px solid <?=$colors['bgbox']?>;
	padding:5px 4px 0 0;
	margin:0;
	border-radius: .5em;
	opacity: .5;	
}

#sidebar .thumb_link a:hover,
#sidebar .thumb_link.sidebarsel a {
	border-color: <?=$colors['hover']?>;
	opacity: 1;
}


<? if(false): ?>
</style>
<? endif; ?>
<?
}

?>
