<?

// Depends:	ui.core.js

//	RIVEDERE TUTTO IL MECCANISMO DI VOTAZIOONE, NON SERVE CHE L'UTENTE VEDA IL VOTO
//  L'UTENTE DEVE SOLO VOTARE CON LE STELLINE POI IL RANK DELLE VOTAZIONI LO METTI IN HOMEPAGE

$images['iconvota']['dir'] = $dirs['cache_base'].'_iconvota.png';
$images['iconvota']['url'] = $urls['cache_base'].'_iconvota.png';

$images['ratingstars']['dir'] = $dirs['cache_base'].'_ratingstars.png';
$images['ratingstars']['url'] = $urls['cache_base'].'_ratingstars.png';

$masks['votofile'] = "_voti_%s".".txt";  //nome del file che contiene il commento per il file immagine %s

#unset($plugins['vota']);

function vota_submit($fotofile)  //chiamata via ajax
{
	global $masks;
	global $alerts;
	global $dirs;
	global $ajax;
    global $urls;
	
	if(isset($_POST['voto']))
	{
	
	$voto = (int)$_POST['voto'];
	
	//FARE INMODO CHE IL VOTO MINIMO ED IL VOTO MASSIMO VALGONO DOPPI COSI CI SARA' PIU DIFFERENZA NELLA VOTAZIONE TOTALE
	
	$vot = vota_getvoto($fotofile);
	
	$vot['voto'] = ($vot['voto']*$vot['nvoti'] + $voto) / ++$vot['nvoti'];
	  //IL VALORE DEL VOTO VA SALVATO CON LA VIRGOLA!!!! senno non funziona niente
	
	$votfile = $vot['voto']."\n".$vot['nvoti'];
	
    put_contents($dirs['data'].sprintf($masks['votofile'],basename($fotofile)), $votfile);
	
	if($ajax)
	{
		#header("Content-type: application/json; charset: utf-8");
		echo utf8_encode('{"voto": "'.round($vot['voto']).'","nvoti": "'.$vot['nvoti'].'"}');
	}
	else
		$alerts[]= "Il tuo voto &egrave; stato salvato";	
    
	}
	
    //EMAIL DI NOTIFICA
	$title = strip_tags(plugins_rawexec('thumb_title',$fotofile));
	$mailtext = '<html><head>'.
#				"<style type=\"text/css\">\n".plugins_rawexec('css',false,'guestbook')."</style>".
				'</head><body>'.
				'<h3>'.$title.'('.$fotofile.')'.'</h3>'.
				plugins_rawexec('thumb_link',$fotofile).
				'Voto inviato: '.$voto."<br />\n".
				'Voto raggiunto: '.$vot['voto']."<br />\n".
				'Voti totali: '.$vot['nvoti'].
				'<div style="clear:both"><hr />'.
				'<b>Data:</b> '.date("d.m.Y H:i")."<br />\n".
				'<b>Album:</b> <a target="_blank" href="'.$urls['action'].'">'.$urls['action']."</a><br />\n".
				"<b>Ip:</b> ".$_SERVER['REMOTE_ADDR']."<br />\n".
				"<b>Browser:</b> ".$_SERVER['HTTP_USER_AGENT']."<br />\n".
				'</div>'.
				'</body></html>';
		
	$resp = email($users['admin']['email'],  //mailfrom
				"Votazione in ".basename($dirs['current'])." della foto ".$title,  //subject
				$mailtext,  //text
				'Votazione Gallery');  //from
    //EMAIL DI NOTIFICA	
}

function vota_getvoto($fotofile)  //return array('voto'=>X,'nvoti'=>Y)
{
	global $dirs;
	global $masks;
    
	$vot = array('voto'=>0,'nvoti'=>0);

	$fil = $dirs['cache'].sprintf($masks['votofile'],basename($fotofile));

	if(is_file($fil)) 	
	  list($vot['voto'],$vot['nvoti']) = file($fil);
	
	return $vot;
}


function vota_head_js()
{
  global $urls;
?>
<script  src="<?=$urls['plugins']?>ui.stars.min.js"></script>
<?
} # */


function vota_thumb($fotofile)
{
  global $images;

  if(is_dir($fotofile)) return false;
  
  $vot = vota_getvoto($fotofile);  
  
?><span class="voto"><?=$vot['voto']?></span><?
  return 'Vota';
} 

function vota_form($fotofile)
{
  global $urls;
  
  if(is_dir($fotofile)) return false;
  
  $vot = vota_getvoto($fotofile);
?>

<div class="votazione_wrap">
<em></em>
<form class="votazione" action="<?=$urls['action']?>">
<input type="hidden" name="submit" value="vota" />
<input type="hidden" name="fotofile" value="<?=$fotofile?>" />
<select name="voto">
<?  //uso select.option
  for($i=1;$i<=5;$i++)
    echo '<option class="star" value="'.$i.'" '.($i==round($vot['voto'])?'selected="selected"':'').'>'.$i.'</option>';
  //il round  fondamentale per il calcolo del voto!! non togliere mai
?>			 
</select>
<input type="submit" value="vota" />
</form>
<span class="voti">(<?=$vot['nvoti']?>)</span>
</div>

<?

  return 'Vota';
}


function vota_js()
{
  global $urls;
  global $dirs;
?>
<? if(false): ?>
<script>
<? endif; ?>

add_thumb_event(function(obj) {
  ratings(obj);
});

function ratings(obj)
{
  var rating = obj.find(".votazione");
  
  $("input:submit",rating).remove();

  rating.stars(
	{
	 inputType: "select",
	 captionEl: $(this).prev(),
	 oneVoteOnly: true,
	 showTitles: true,
     callback: function(ui, type, value) {
	    
		var fotofile = $(obj).attr('id');
		var rat = $(this);
		$.post(ULG.urls.action,
			{
			  ajax: "vota",
			  func: "submit",
			  file: fotofile,
			  voto: value
		    },
			function(resp) {
			   rating.parent()
			   .fadeTo("slow",0,function(){
			     rating.stars("select", resp.voto).next('.voti').text('('+resp.nvoti+')');
			     $(this).fadeTo("slow",1,function(){ulgalert("Il tuo voto &egrave; stato salvato");});
			   });
			},
			'json'
		);	   
     }
	});
	
  //$("a",rating).Tooltip({showURL: false});
  
  rating.parent().show();
}

/*
function showfotovota(obj)  //da perfezionare per l'amministrazione
{
  var fotofile = $(obj).attr('id');

  setTimeout(function() {
      $('#panel').accordion('activate','.vota');
    }, 500);

    $('#votathumb').empty().addClass('loading')
        .load(ULG.urls.action,
			{ajax:'vota', func:'thumbnail', file:fotofile},
			function() {  //callback
			 $(this).removeClass('loading');
			 
			});
}
// */
<? if(false): ?>
</script>
<? endif; ?>
<?
} # */


/*
function vota_thumbnail($fotofile)  //richiamata quasi sempre attraverso ajax
{
  thumb_link($fotofile);  //funzione interna di ulg
}


function vota_panel()
{
  global $imgfiles;
  
	?>
	<form>
	<div id="votathumb"></div>
	<?
	vota_stars($imgfiles[0]);
	?>
	<input type="submit" value="Vota" class="pulsante" />
	</form>
	<?
	return 'Vota le foto';
} # */


function vota_css()
{
    global $colors;
    global $images;
    global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
.votazione_wrap {
	position:absolute;
    top: 1.6em;
	right:0.75em;
	display:none;
	display:block;
}
.votazione {
    float:right;
}
.voto {
	display:block;
    background: <?=$colors['background']?>;
    border: 0.0625em solid <?=$colors['border']?>;
}
.voti {
  float:right;
  clear:both;
  font-size: x-small;
  color:#ffffff;
}
.ui-stars-cancel, .ui-stars-star {width:12px;height:12px; float:left;display:block;text-indent:-999em;cursor:pointer;overflow:hidden;margin:0 0.0625em 0.0625em 0; background:trasparent}
.ui-stars-cancel a, .ui-stars-star a {display:block;width:12px;height:100%}
.ui-stars-cancel, .ui-stars-cancel a {background:url('<?=$images['ratingstars']['url']?>') no-repeat 0 0}
.ui-stars-star, .ui-stars-star a {background:url('<?=$images['ratingstars']['url']?>') no-repeat 0 -28px}
.ui-stars-star-on a {background-position:0 -41px!important}
.ui-stars-star-hover a {background-position:0 -54px}
.ui-stars-cancel-hover a {background-position:0 -13px}
.ui-stars-cancel-disabled a, .ui-stars-star-disabled, .ui-stars-star-disabled a {cursor:default !important}
.ui-stars-star {background:transparent!important;overflow:hidden!important;}

<? if(false): ?>
</style>
<? endif; ?>
<?
} # */

function vota_cache()
{
  global $images;

  require('vota.cache.php');

  put_contents($images['ratingstars']['dir'], $icon1);
}

?>
