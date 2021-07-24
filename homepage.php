<?
//TODO rinominare questo plugin, categorie.php

$categories = array(
'luoghi'=>array(
	'monti-della-laga',
	'valle-dei-calanchi',
	'monte-soratte',
	'monte-cosce',
	'terromoto-aquila-abruzzo',
	'strada-del-passatore',
	'parco-delluccellina',	
	'castelluccio-di-norcia',
	'calvi-dell-umbria',
	'poggio-catino-eremo-san-michele',
	'eremo-san-girolamo',
	'archeologia',
	'adamello-val-genova',	
	'pantani-di-accumoli',
	'lago-di-san-liberato',
	'lago-di-pilato-foce',
	'parco-valle-treja',
	'valserra',
	'vasanello',
	'stifone',
	'santa-pudenziana',
	'roccantica',
	'sicilia',
	'monaco-di-baviera',
	'otricoli',
	'tevere-esondato',
	'piani-di-cottanello',
	'tarquinia-bomarzo',
	),
'artistiche'=>array(
	'istanti',
	'bianco-nero',
	'ombre',
	'mosso',
	'materia',
	'teleobiettivo',
	'notturne',
	'riflessi',
	'fotoritocco',
	'fotografi',	
	'pittori',
	),
'natura'=>array(
	'foglie',
	'majella',
	'alba-vettore',	
	'terminillo-tramonto',
	'bosco-monte-fumaiolo',	
	'melograni',
	'vigneto',
	'acqua',
	'cielo',
	'oasi-di-nazzano',
	'torrenti',
	'nebbia',
	),
'sport'=>array(
	'arrampicata',
	'grotte',
	'canyoning',
	'alpinismo',
	'bike',
	'ferrata-gamma-1',
	'parapendio',
	'mondiale-deltaplano-montecucco-2008',	
	),
'altro'=>array(
	'libri',
	'disegni',
	'vecchie',	
	'animali',	
	'rendering',
	'bonsai',
	'piatti',	
	'web',
	'hackmeeting-2009',
	'hardware',
	'screenshot',
	'fossili-e-minerali',
	'barbecue-cottanello',
	'open-terni-festival',
	'stereogrammi',
	'elisoccorso-pecore-sul-tevere',
	'neve-2010',
	),
);

if(isset($_GET['category'])):

	$urlaction = './?';

	if( !array_key_exists($_GET['category'], $categories) )
	{
		header('HTTP/1.0 404 Not Found');	//se il plugin non ce
		exit(0);
	}

	$category = strtolower($_GET['category']);
	
	function ulg_title() {
		global $category;
		echo 'Foto '.$category;
	}
	
	foreach($categories as $cat=>$dirs)	//esclude gli album delle altre categorie
		if($cat != $category)
			foreach($dirs as $dir)
				$nolist[]= $dir;
	
	$categories = array($categories[ $category ]);

endif;

$norun=true;
$index=true;
$noplugs =array('calendario','file');
$tnforpage = 60;

if(isset($categories) and count($categories)>1)
	$sitemaptree = false;
else
{
	$noplugs = array_merge(array('racconto','condividi','mappa','order'),$noplugs);
	$nopanel = true;
	$sitemaptree = true;
}
		
require_once('ulg.php');

#cache_exec(array(6,0,0),'head');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="verify-v1" content="A/oRz+zKrdWpY/A+DnQASOLrVItnot2tjmyPIbXtizA=" /><!--google webmasters-->
<?
    plugins_exec('head');
    #cache_exec(array(20,0,0),'head_new',$category);

	css();
	css_new();
?>
</head>
<?
if(!$nobody) {
	?><body class="indice"><?
}
?>
<table id="pager" cellpadding="0" cellspacing="0">
<tr>
	<td id="pager_left">
	<?

	cache_exec(array(20,0,0),'content_new',$category);  //contenuto nella pagina
	#content_new();

	function content_new()
	{
		global $admin;
		global $public;
		global $alerts;
		global $nobody;
		global $nobanner;
		global $urls;
		global $opts;
		global $dims;		
		global $categories;
		global $category;
		
		?>
		<div id="content">
		<?

		title();
		text();
		
		if(count($categories)>1):
		
			$dims['tnforalbum'] = 4;
		
			plugins_exec('content_top');
			
			?><div class="cat-list"><?
				foreach($categories as $cat=>$albums)
				{
					$albums = array_slice($albums, 0, $dims['tnforalbum']);
					?><div class="cat-col"><h2 class="cat-tit"><a href="?category=<?=$cat?>"><?=$cat?> &raquo;</a></h2><?
					thumbs($albums);
					?></div><?	
		   		    flush();
				}
				thumbs(getfiles());
			?></div><?
		
		else:

			$dims['tnforalbum'] = 6;

			plugins_exec('content_top');
			
			?><div class="cat-list"><?
				thumbs($categories[0]);
			?></div><?		
		endif;

		plugins_exec('content_bottom');

		?>
		</div>
		<div id="fotopage_wrap">
		  <div id="fotopage"></div>
		  <a id="fotopageclose" href="#">&times;</a>
		</div>
		<?
	} //fine content_new()
	?>
	</td>
	<td id="content_banner">
	<? if(!$nobanner) plugins_exec('content_banner'); ?>
	</td>
	<td id="pager_right">
	<?
		if(!$nopanel)
			cache_exec(array(3,0,0),'panel');
	?>
	</td>
</tr>
</table>
<?php

	cache_exec(null,'menus');

?>
<div id="foot_banner">
<?php
	if(!$nobanner)
		plugins_exec('foot_banner');
?>
</div>
<?php

foot();
js();
tail();

function css_new()
{
	global $colors;
?>
<style>
.cat-list {
	clear:both;
}
.cat-col {
	float:left;
	margin:3em .5em 1em .8em;
	padding:.125em;
	overflow:visible;
	background:<?=$colors['bgbox']?>;
	border-radius: 1em;
}
.cat-col .pagefoot{
	border:none;
}
.cat-col .thumb_wrap {
	clear:both;
	margin:.5em 0 0 0;
}
.cat-tit {
	margin-left:.5em;
	text-transform:capitalize;
	font-size:1.25em;
	letter-spacing:.125em;
	position:relative;
	top:-1.5em;
	margin-bottom:-1.5em;
}
.cat-col .thumb_wrap.list,
.cat-col .thumb.list {
	width:8.85em;
	height:auto;
	position:static;
	clear:both;
	float:left;
	border:none;
	margin-bottom:.5em;
}
.cat-col .thumb,
.cat-col .thumb_text,
.cat-col .active .thumb_text,
.cat-col .thumb_link {
	background:none;
	border:none;
}
.thumb.list .thumb_text {
	overflow:hidden;
	height:auto;
	border:none;
}
.cat-col .thumb {
	border:2px solid <?=$colors['bgbox']?>;
}
.cat-col .active {
	border:2px solid <?=$colors['hover']?>;
	background:#fefefe;
}
.cat-col .album.active .numphotos {
	background:#fff;
}
.cat-col .active.list,
.cat-col .thumb.list {
	width:8em;
	padding:.3em;
	border:2px solid <?=$colors['bgbox']?>;
}
.cat-col .thumb.list .thumb_text {
	font-size:.9em;
}
.cat-col .active.list {
	border:2px solid <?=$colors['hover']?>;
	border-radius: .5em;
}
.cat-col .album.list .calendar {
	top:1em;
	right:.125em;
}
.trips a:hover {
	border:1px solid <?=$colors['hover']?>;
}
#racconto {
	border:none;
}
.trips a:link,
.trips a:visited,
.trips a {
	display:block;
	text-decoration:none;
	float:right;
	border:1px solid <?=$colors['border']?>;
	background: <?=$colors['bgbox']?>;
	padding:3px;
	margin:.25em;
	border-radius: .5em;
}
.trips a:hover {
	text-decoration:underline;
}
</style>
<?
}

?>
