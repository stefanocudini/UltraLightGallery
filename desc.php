<?

/*
usare urltitle per rinominare file e cartelle con il relativo titolo
e rinominare anchei file di cache, senno si perdono informazioni
*/


function desc_init()
{
	global $images;
	global $urls;
	global $dirs;
	global $dims;
	global $masks;
	
	global $imgtitles;
	global $imgdescrs;
	//cache a runtime per ridurre lettura su disco
	
	$masks['descrfile'] = "_descr_%s".".txt";  //nome del file che contiene il commento per il file immagine %s
	$masks['titlefile'] = "_title_%s".".txt";  //nome del file che contiene il commento per il file immagine %s
	
	$dims['descwidthchar'] = floor($dims['panelwidth']/11.5);  ///mah!! ad occhio ;)
	$dims['desctnsize'] = $dims['tnsize_default'];
	
	$images['descedit']['dir'] = $dirs['cache_base'].'_descedit.png';
	$images['descedit']['url'] = $urls['cache_base'].'_descedit.png';
}

function desc_gettitle($fotofile)	//titolo
{
	global $masks;
	global $dirs;
	global $imgtitles;  //cache a runtime per ridurre lettura su disco
	
    if(isset($imgtitles[$fotofile]))  //non ripete la lettura del file
   	  return $imgtitles[$fotofile];

	$filetitle = $dirs['data'].sprintf($masks['titlefile'],basename($fotofile));

	if(!is_file($filetitle))
	{
		if(is_dir($fotofile)) return basename($fotofile);
		elseif(is_file(getcript_filename($fotofile))) return '';  // non usare spazio vuoto senno su explorer implode!
	}

	$tit = get_contents($filetitle);

	if(trim($tit)=='')
	  if(is_dir($fotofile)) return basename($fotofile);
	
	$imgtitles[$fotofile] = $tit;

	return $tit;
}

function desc_getdesc($fotofile)	//descrizione
{
	global $masks;
	global $dirs;
	global $imgdescrs;  //cache a runtime per ridurre lettura su disco	

    if(isset($imgdescrs[$fotofile]))  //non ripete la lettura del file
   	  return $imgdescrs[$fotofile];
	
	$filedesc = $dirs['data'].sprintf($masks['descrfile'],basename($fotofile));

	if(!is_file($filedesc)) return '';

	$desc = trim(get_contents($filedesc));

	$imgdescrs[$fotofile] = $desc;

	return $desc;
}

function desc_title()
{
	global $urls;	
    global $dirs;
	global $opts;
	global $fotopage;
	
	#echo $dirs['base'].' - '.$_SERVER['DOCUMENT_ROOT'];
	#return;

	$urlbase = str_replace('http://','',$urls['base']);
	$urlcurr = str_replace('http://','',$urls['current']);
	
	if( $dirs['base']==$_SERVER['DOCUMENT_ROOT'] ):  //OSCENITA DA TOGLIERE IL PRIMA POSSIBILE!!
		$basepath = $dirs['base'];
		$basedir  = substr($dirs['base'],0,strlen($dirs['base'])-1);
		$basehref = substr($urls['base'],0,strlen($urls['base'])-1);	
	else:
		$basepath = dirname($urlbase);
		$basedir  = dirname(substr($dirs['base'],0,strlen($dirs['base'])-1));  //non togliere questo dirname
		$basehref = $basepath;
	endif;
	
	$urlbase = str_replace($basepath,'',$urlbase);
	$urlcurr = str_replace($basepath,'',$urlcurr);
	//pulisce urls e li tratta come array
	
	$links = explode('/',$urlcurr);
	
	$links = array_slice($links,2,-1); //toglie primo e ultimo

#echo print_r($links,true).'<br>';

    $path = $urlbase;
    foreach($links as $k=>$l)
	{
		$path .= $links[$k].'/';
		$links[$k] = $path;
	}
	
	$defdirdata = $dirs['data'];

#echo print_r($links,true).'<br>';
	
    foreach($links as $k=>$l)
	{
      $href = $basehref.$l;
	  
  	  $dirs['data'] = dirname($basedir.$l).'/'.$opts['datadirname'].'/';
	  $title = desc_gettitle($basedir.$l);
	  
	  $titles[] = '<a href="'.$href.'">'.$title.'</a>';
	}
/*	?>&bull; &nbsp;<? //*/
	echo @implode(' <big>&raquo;</big> ',$titles);
	
	$dirs['data'] = $defdirdata;

    if($fotopage)	//aggiunge titolo foto
	{
		$foto = desc_gettitle($_GET['foto'].'.jpg');
		echo ' <big>&raquo;</big> '.($foto=='' ? $_GET['foto'] : $foto);
	}

	return 'Titolo Album';
}

function desc_text()
{
	global $dirs;
	global $opts;
	global $fotopage;
	
	if($fotopage) return print(desc_getdesc($_GET['foto'].'.jpg'));

	$defdirdata = $dirs['data'];

	$ddir = $dirs['current'];

    $dirs['data'] = dirname($ddir).'/'.$opts['datadirname'].'/';

	$text = desc_getdesc($ddir);

	$dirs['data'] = $defdirdata;

	echo "$text";

	return 'Desc';
}# */


if($admin):

function desc_settitle($fotofile,$title)  //usato anche da altri plugins
{
	global $dirs;
	global $masks;

	$title = @stripslashes(trim($title." "));
	
    put_contents($dirs['data'].sprintf($masks['titlefile'],basename($fotofile)),$title);
	
	return $title;
}

function desc_submit($fotofile)  //chiamata via ajax
{
	global $masks;
	global $alerts;
	global $dirs;
	global $ajax;

	$descr = str_replace("\n",'<br />',@stripslashes(trim(@$_POST['descr']." ")));
	
	$title = desc_settitle($fotofile,@$_POST['title']);

    put_contents($dirs['data'].sprintf($masks['descrfile'],basename($fotofile)),$descr);
    
    cache_reset();//rigenera la cache

	if($ajax)
	{
		$title = htmlspecialchars(desc_gettitle($fotofile));
		$desc = htmlspecialchars(desc_getdesc($fotofile));

		echo '{"thumb": "'.$fotofile.'","title": "'.$title.'","descr": "'.$desc.'"}';
	}
	else
		$alerts[]= "Titolo e Didascalia della foto $fotofile sono stati modificati";
}



function desc_thumb_menu($fotofile)
{
  global $images;
  global $nopanel;

  if($nopanel) return false;

?><a class="icon desc" href="#" title="Modifica Didascalia e Titolo"><span>Titolo</span><img src="<?=$images['descedit']['url']?>" /></a><?
  return 'Modifica Didascalia e Titolo';
} # */


function desc_thumbnail($fotofile)  //richiamata quasi sempre attraverso ajax
{
	global $dims;

	$dims['tnsize'] = $dims['tndirsize'] = $dims['desctnsize'];
	thumb_link($fotofile);  //funzione interna di ulg
}

endif; //fine admin

function desc_thumb_title($fotofile)
{
	?><span class="titolo"><?=desc_gettitle($fotofile)?></span><?
	return 'Titolo';
}

function desc_thumb_text($fotofile)
{
    ?><div class="didascalia"><?=desc_getdesc($fotofile)?> </div><? /*lasciare lo spazio vuoto*/
    return 'Descrizione';
}

function desc_js()
{
  global $urls;
  global $dims;
?>
<? if(false): ?>
<script>
<? endif; ?>

var descwraptmp = [];  //se non è un array non può gestiore modifiche di piu foto insieme

add_thumb_event(function(obj) {

	$(".icon.desc", obj).one('click',function() {
	  showfotodesc(obj);
	  return false;
	});

});

function descsaveChanges(obj,resetta)
{
    var fotofile = $(obj).attr('id');
						
	if(!resetta)
	{
		var tit = $('.thumb_title .desceditinput',obj).val();
		var des = $('.didascalia .desceditinput',obj).val();

	    obj.css({height:obj.height(),width:obj.width()}).empty().addClass('loading');
  
		$.post(ULG.urls.action,
			{
				ajax: "desc",
				func: "submit",
				file: fotofile,
				title: tit,
				descr: des
			},
			function(resp) {
				obj.removeClass('loading');
				obj.replaceWith(descwraptmp[fotofile]);				
				$(".titolo",descwraptmp[fotofile]).html(resp.title);
				$(".didascalia",descwraptmp[fotofile]).html(resp.descr);
			},
			'json'
		);
	}
	else
	  obj.replaceWith( descwraptmp[fotofile] );
	
	thumb_event(descwraptmp[fotofile]);
	descwraptmp[fotofile].trigger('mouseout');
	//rimuove selezione sulla thumbnail alla fine del salvataggio, tanto non serve a niente
}

function showfotodesc(obj)  //da perfezionare
{
	var fotofile = $(obj).attr('id');
	var thumb_wrap = obj.parents('.thumb_wrap');
	var thumbtitle$ = obj.children('.thumb_title');
	
	obj.unbind();  //annulla animazioni thumb
	descwraptmp[fotofile] = obj.clone();
	
	
	obj.css({width:'auto',zIndex:21});
	
	thumbtitle$.css({overflow:'visible'});
	
    var title = $('.titolo',obj).html();
	
	obj.prepend('<div class="desceditbuttons">'+
			    '<input tabindex="3" type="submit" value="Salva" class="saveButton pulsante" /> '+
			    '<input tabindex="4" type="reset" value="Annulla" class="cancelButton pulsante" />'+
			    '</div>');
	//scaricare sta roba in ajax
	
	$(".thumb_title",obj).html('<input tabindex="1" type="text" class="desceditinput" value="'+ title +'" />')
		.height('auto')
		.children('input').focus();
		
	$(".didascalia",obj).append(' ').wrapInner('<textarea tabindex="2" class="desceditinput"></textarea>');

    $(".desceditinput",obj).TextAreaExpander();

	$('.saveButton',obj).click(function() {
	  descsaveChanges(obj, false);
	  	thumbtitle$.css({overflow:'hidden'});
	  return false;
	});
	
	$('.cancelButton',obj).click(function() {
		descsaveChanges(obj, true); //resetta senza salvare
	  	thumbtitle$.css({overflow:'hidden'});		
		return false;	  
	});
}
<? if(false): ?>
</script>
<? endif; ?>
<?
} # */

function desc_css()
{
    global $colors;
    global $images;
    global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#descform_wrap {
  width: <?=pixem($dims['panelwidth'])?>em;
}
#desctitfile {
  float:right;
}
#descthumb {
    text-align:center;
	vertical-align:middle;
	height: <?=pixem($dims['desctnsize'])?>em;
	width: <?=pixem($dims['desctnsize'])?>em;
	display:block;
/*  position:relative;*/
	padding: 0;
	margin: 0.25em;
	background-color: <?=$colors['bgbox']?>;
	border-radius: 0.625em;
	float:left;
}
#descthumb .thumb_link,
#descthumb .thumb_link a,
#descthumb .thumb_link img {
    height: <?=pixem($dims['desctnsize'])?>em;
    width: <?=pixem($dims['desctnsize'])?>em;
}

#desctitle,
.titolo {
    font-size: 1em;
    line-height: 1.1em;
    font-weight: bold;
    text-transform:capitalize;
}
#descdescr,
.didascalia {
    font-size: 1em;
    line-height: 1em;
}
.titolo,
.didascalia {
  width: auto;
}
#desctitle,
#descdescr {
  width: <?=pixem($dims['panelwidth']-20)?>em;
}

.editablehover {
  background-color: <?=$colors['bghover']?>;
}
.desceditbuttons {
  white-space:nowrap;
  margin:0.25em 0;  
}
.thumb_title .desceditinput {
  width: <?=pixem(back_tnsize(max($dims['tnsizes']),2))-1?>em;
  height:1em;
  white-space: nowrap; /*centra una riga sola*/
}
.thumb_text .desceditinput {
  width: <?=pixem(back_tnsize(max($dims['tnsizes']),2))-1?>em;
  height:3em;
}
.desceditinput textarea {
  margin:0;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function desc_cache()
{
  global $images;

  require('desc.cache.php');

  put_contents($images['descedit']['dir'],$icon1);
}


?>