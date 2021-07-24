<?

function sitemap_init()
{
	global $start;
	global $admin;
	global $maxsubdir;
	global $dirs;
	global $urls;
	global $images;
	global $opts;
	global $sitemaptree;
	
	$dirs['sitemapfile'] = $dirs['base'].'sitemap.xml';
	$urls['sitemapfile'] = $urls['base'].'sitemap.xml';	
	
	$opts['sitemaptree']= isset($sitemaptree) ? $sitemaptree : true;	//rigenerare la cache css quando si cambia valore
    $opts['sitemaptreeopen'] = (isset($_COOKIE['sitemaptreeopen']) and $_COOKIE['sitemaptreeopen']=='false') ? false : true;

	$images['sitemapicon']['dir'] = $dirs['cache_base'].'_sitemapicon.png';
	$images['sitemapicon']['url'] = $urls['cache_base'].'_sitemapicon.png';

	$images['sitemap_top']['dir'] = $dirs['cache_base'].'_sitemap_top.png';
	$images['sitemap_top']['url'] = $urls['cache_base'].'_sitemap_top.png';
	
	if($admin and isset($_GET['sitemap']))
		sitemap_start();
}

function sitemap_start()
{
	global $dirs;
	global $urls;
	global $opts;
	global $masks;
	global $alerts;

	$rdirs = rgetdirs();

	if(count($rdirs)>0)
	{
		$fs = fopen($dirs['sitemapfile'],'w');

		fwrite($fs,'<?xml version="1.0" encoding="UTF-8"?>'."\n".
				   '<urlset '.
				   'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '.
				   'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
				   'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 '.
				   'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n");
		fwrite($fs,
		'<url>'.
			'<loc>'.'http://'.$_SERVER['SERVER_NAME'].$urls['base'].'</loc>'.
			'<priority>1.0</priority>'.
			#'<lastmod>2011-03-17T10:29:54+00:00</lastmod>'.
			'<changefreq>weekly</changefreq>'.
		"</url>\n");		
		$nn=0;
		foreach($rdirs as $d)
		{
			$nd = count(explode('/',$d))>10?10:count(explode('/',$d));
			$prio = 10 - $nd + 1;
			$prio = substr($prio/10+0.01,0,3);
			fwrite($fs,
			'<url>'.
				'<loc>'.'http://'.$_SERVER['SERVER_NAME'].$urls['current'].$d.'/'.'</loc>'.
				'<priority>'.$prio.'</priority>'.
				#'<lastmod>2011-03-17T10:29:54+00:00</lastmod>'.
				'<changefreq>weekly</changefreq>'.
			"</url>\n");
			//aggiungere anche pagine delle foto
			$nn++;
		}
		
		fwrite($fs,"\n<!--$nn-->\n</urlset>\n");//numero url inseriti
		fclose($fs);

		#header("Content-type: text/xml");
		#readfile($dirs['current'].'sitemap.xml');
		header("Location: ".$urls['sitemapfile']);
	}
	else
	  header('Location: '.$urls['current']);
}


function sitemap_tree()
{	
	global $dirs;
	global $opts;
	global $urls;
	
	if(isset($_SERVER['HTTP_REFERER']))
	{
		$p = parse_url($_SERVER['HTTP_REFERER']);
		$refp = $p['path'];
	}
	else
		$refp = '';
	
	function tree($d,$refp)  //funzione ricorsiva
	{
		global $dirs;
		global $opts;
		global $urls;
		
		$dircachedefault = $dirs['cache'];
		?><ul><?
		
		foreach($d as $dir=>$v):
		
			$curcond = strstr($refp,$dir)==$dir.'/';  //directory referente

			?><li<?=$curcond?' class="current"':''?>><?

			$dirs['data'] = dirname($dir).'/'.$opts['datadirname'] .'/';
			$title = trim(strip_tags(plugins_rawexec('thumb_title',$dir,false)));
			$href = $dir.'/';
			
			?><a href="<?=$urls['current'].$href?>"><?=$title?></a><?

			if(count($v)>0)
				tree($v,$refp);

			?></li><?
		endforeach;
		
		$dirs['cache'] = $dircachedefault;
		?></ul><?
	}

	if($urls['current']!=$urls['base']):
		?><a id="sitemap_ret" rel="<?=dirname($urls['current'])?>" tutle="Album superiore" href="#">&nbsp;</a> <?
		//&laquo; <b>..</b>
		$dirs['cache'] = dirname($dirs['current']).'/'.$opts['thumbdirname'] .'/';
		$title = trim(strip_tags(plugins_rawexec('thumb_title',basename($dirs['current']),false)));
		$title = empty($title) ? basename($dirs['current']) : $title;
		$dirs['cache'] = $dircachedefault;
		?><a class="current" href="<?=$urls['current']?>"><?=$title?></a><?
	endif;
	$ardir = rgetdirs('.',true);
	
	tree($ardir,$refp);  //funzione ricorsiva
}


function sitemap_menu()
{
	global $urls;
	global $admin;
	global $images;

	if(!$admin) return false;

	?>
	<a href="<?=$urls['base']?>?sitemap" title="Genera sitemap.xml per motori di ricerca"><img src="<?=$images['sitemapicon']['url']?>" /><span> SiteMap</span></a>
	<?
	return 'Genera Sitemap';
}

function sitemap_content_top()
{
	global $urls;
	global $dirs;
	global $imgdirs;
	global $index;
	global $opts;
	global $fotopage;

	if($fotopage or !$index or !$opts['sitemaptree']) return false;
	
	?><div id="sitemap_wrap"><?
	?><a id="sitemap_hide" title="Chiudi Albero" href="#"><big>&laquo;</big></a><?
	?><div id="sitemap"><?
	?><input id="sitemap_find" type="text" class="inactive" value="Cerca..." size="10" /><?

	$dircachedefault = $dirs['cache'];
	sitemap_tree();
	#cache_exec(array(0,0,1),'sitemap_tree');
	$dirs['cache'] = $dircachedefault;
	?></div></div><?
} # */

function sitemap_head_js()
{
	global $opts;
?>
<script>
ULG.opts.sitemaptree = <?=$opts['sitemaptree']?'true':'false'?>;
ULG.opts.sitemaptreeopen = <?=$opts['sitemaptreeopen']?'true':'false'?>;
</script>
<?
}

function sitemap_js()
{
?>
<? if(false): ?>
<script>
<? endif; ?>

$(document).ready(function() {

	if(ULG.opts.sitemaptree==false) return false;

	function sitemaptree_close() {
		$('#sitemap_wrap').addClass('closed');
		$("body.indice .cat-list, body.indice .thumbs").not('body.indice .cat-list .thumbs').css({marginLeft: 30});
		$("#sitemap_hide").attr({title: 'Apri Albero'}).blur().children('big').html("&raquo;");
		$.cookie('sitemaptreeopen', 'false');
		ULG.opts.sitemaptreeopen = false;
	}
	
	function sitemaptree_open() {
		$('#sitemap_wrap').removeClass('closed');
		$("body.indice .cat-list, body.indice .thumbs").not('body.indice .cat-list .thumbs').css({marginLeft: 160});			
		$('#sitemap_hide').attr({title: 'Chiudi Albero'}).blur().children('big').html("&laquo;");
		$.cookie('sitemaptreeopen', 'true');
		ULG.opts.sitemaptreeopen = true;	
	}
	
	if(ULG.opts.sitemaptree && ULG.opts.sitemaptreeopen)
		sitemaptree_open();	
	else
		sitemaptree_close();
	
	$('#sitemap_hide').click(function() {

		if(ULG.opts.sitemaptreeopen)
			sitemaptree_close();
		else
			sitemaptree_open();

		return false;
	});

	$('#sitemap_ret').live('click',function() {
		var purl = $(this).attr('rel');
		var wrap$ = $(this).parent();
		wrap$.addClass('loading').load( purl+'?ajax=sitemap&func=tree', function(data) {
			wrap$.find('li.current').parents('li:last').insertAfter('a.current');
			wrap$.removeClass('loading');
		});
		return false;
	});
	
	$.extend($.expr[':'], {	//definizione di :conaints() case insesitive
	  'containsi': function(elem, i, match, array)
	  {
		return (elem.textContent || elem.innerText || '').toLowerCase()
		.indexOf((match[3] || "").toLowerCase()) >= 0;
	  }
	});
	
	var tf;
	$('#sitemap a:not(#sitemap_ret)').live('click',function(e) {
		if(tf) clearTimeout(tf);
	});
	$('#sitemap_find')		// Ricerca titolo album!
		.keyup(function() {

			var t = $(this).val();
			if(t.length<2) {
				$('#sitemap li').show();
				$('#sitemap a').removeClass('select');
				return false;
			}
			$('#sitemap li').hide();
			$('#sitemap a').removeClass('select');
			$("#sitemap a:containsi('"+t+"')").addClass('select').parents().show();

		})
		.click(function() {
			$(this).removeClass('inactive').val('');
		})
		.blur(function() {
			tf = setTimeout(function() {	//senza non funziona il link agli album nell'albero
				$('#sitemap li').show();
				$('#sitemap a').removeClass('select');
			},300);
			$(this).addClass('inactive').val('Cerca...');			
		})
		.addClass('inactive').val('Cerca...');
	
});

<? if(false): ?>
</script>
<? endif; ?>
<?
} # */


function sitemap_head_css()
{
  global $opts;
  global $index;

if($index and $opts['sitemaptree'] and $opts['sitemaptreeopen']):
?>
<style>
body.indice .cat-list,
body.indice .thumbs {
	margin-left: 160px;
}
body.indice .cat-list .thumbs {
	margin-left:0;
}
</style>
<?
endif;
}

function sitemap_css()
{
  global $colors;
  global $dims;
  global $opts;
  global $images;

?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#sitemap_wrap {
	width:140px;
	clear:both;
	float:left;
	position:relative;
}
#sitemap_wrap.closed {
	width:10px;
}
#sitemap_wrap.closed #sitemap {
	display:none;
}
#sitemap {
	display:block;
	width: 142px;
	padding-right: 2px;
	position:absolute;
	float:left;
    border-right: 0.0625em solid <?=$colors['border']?>;
}
#sitemap.loading {
	background: url('<?=$images['jqueryloadingcircle1']['url']?>') no-repeat 120px 4px;
}
#sitemap_ret {
	background: url('<?=$images['sitemap_top']['url']?>') no-repeat center 2px;
}
#sitemap_ret,
#sitemap a.current {
	display:block;
	padding-left:.125em;

}
#sitemap a.current {
	font-size:.85em;
	line-height:1.5em;
	text-transform:capitalize;
}
#sitemap_ret {
	border-top: 1px solid <?=$colors['border']?>;	
}
#sitemap_ret:hover {
	background: url('<?=$images['sitemap_top']['url']?>') no-repeat center 2px <?=$colors['bgbox']?>;
}
#sitemap_hide {
	position:absolute;
	top:0;
	right: -1em;
	width: .7em;
	display: block;
	line-height: 1em;
	margin-left:0;
	height:1em;
	padding-left:1px;
	cursor: pointer;
	border-top: 0.0625em solid <?=$colors['border']?>;    
	font-size:large;
}
#sitemap_wrap.closed #sitemap_hide {
    border-top: 0.0625em solid <?=$colors['border']?>;
    border-right: 0.0625em solid <?=$colors['border']?>;
    border-bottom: 0.0625em solid <?=$colors['border']?>;
	line-height:3em;
	height:3.1em;
}
#sitemap ul {
	margin:0 0 .5em 0;
	list-style:disc;
	padding-left:1em;
}

#sitemap li {
	font-size:.85em;
	line-height:1.5em;
	font-weight:bold;
	text-transform:capitalize;
	padding-left:.125em;
}

#sitemap li.current {
    border: 1px solid <?=$colors['border']?>;
    background:<?=$colors['bgbox']?>;
    margin-right:-3px;
}

#sitemap a.select {
    background:<?=$colors['bghover']?>;
    padding:0 .25em;
	border-radius: .25em;
}
#sitemap_find.inactive {
	color: <?=$colors['border']?>;
    border: 1px solid <?=$colors['border']?>;
    margin:3px 0;
    background:<?=$colors['background']?>;
	font-style: italic;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function sitemap_cache()
{
	global $opts;
	global $dirs;
	global $images;
	
	@touch($dirs['base'].'sitemap.xml');
	@chmod($dirs['base'].'sitemap.xml',CHMOD);

	require('sitemap.cache.php');
	
	put_contents($images['sitemapicon']['dir'],$icon1);
	put_contents($images['sitemap_top']['dir'],$icontop);
}

?>
