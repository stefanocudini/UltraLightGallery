<?

function guestbook_init()
{
	global $dims;
	global $urls;
	global $dirs;
	global $images;
	global $guestcomfile;
	global $guestcaptcha;
	global $plugins;
	global $larchar;
	global $admin;
	global $guestname;
	global $guestemail;
	global $guestmess;
	global $start;
	global $admin;

	$guestname = '';
	$guestemail = '';
	$guestmess = '';
	//default, oppure utile a settare valori se non funziona javascript, all'interno del _submit()

	$guestcomfile = $dirs['data']."_guestbook.txt";

	#$dims['guestbooktnsize'] = min($dims['tnsizes']);
	$dims['guestbooktnsize'] = next_tnsize(min($dims['tnsizes']));

	$images['guestcommenticon']['dir'] = $dirs['cache_base'].'_guestcomment.png';
	$images['guestcommenticon']['url'] = $urls['cache_base'].'_guestcomment.png';

	#$guestcaptcha = (isset($plugins['captcha']) and !islocal()) ? true : false;
	$guestcaptcha = true;

	$larchar = floor($dims['panelwidth']/11.5);  ///mah!! ad occhio ;)

	if($admin)
	{

	//sistemare guestbook_delmess() xke ho cambiato l'id dei messaggi

		if(isset($_POST['delmess']))
		  guestbook_delmess($_POST['delmess']);

		if(isset($_POST['editlistmess']))
		  guestbook_editlistmess();

		if(isset($_GET['guestbookadmin']))  //interfaccia di amministrazione dei commenti senza uso di ajax
		  $start = 'guestbook';
	}

} //fine init()

function guestbook_start()
{
	head();
	?>
	<body id="guestbookadmin">
	<h2>Modifica i messaggi</h2>
	<?
	guestbook_formedit();

	tail();
}



function guestbook_submit($fotofile)
{
    global $guestcomfile;
    global $alerts;
    global $masks;
    global $urls;
    global $dirs;
	global $dims;
	global $ajax;
	global $users;
	global $guestcaptcha;
	global $spammer;
	global $guestname;
	global $guestemail;
	global $guestmess;

	$cap = $nam = $mail = $mes = true;

  if($guestcaptcha)
    $cap = $spammer ? false : true;

  if(!isset($_POST['name']) or empty($_POST['name']) or trim($_POST['name'])=='')
  {
    $alerts[] = "Inserisci il tuo nome";
    $nam = false;
  }
  else
    $guestname = $_POST['name'];

  if(!isset($_POST['email']) or empty($_POST['email']) or trim($_POST['email'])=='')
  {
	$mail = false;
    $guestemail = '';
  }
  else
    $guestemail = $_POST['email'];

  if(!isset($_POST['msg']) or empty($_POST['msg']) or trim($_POST['msg'])=='')
  {
    $alerts[] = "Inserisci il messaggio";
    $mes = false;
  }
  else
    $guestmess = $_POST['msg'];

  $ok = ($cap and $nam and $mes) ? true : false;
  //condizioni per accettare il messagio

  if($ok)
  {
	$mid = 'mid'.date("dmyhi"); //id del messaggio
	$date = date("d.m.Y H:i");
	$name = @htmlentities(@stripslashes(@strip_tags(@utf8_decode($guestname))));
	$email = @htmlentities(@stripslashes(@strip_tags(@utf8_decode($guestemail))));
	$msg = @htmlentities(@stripslashes(@strip_tags(@utf8_decode($guestmess))));
	#$msg = trim(wordwrap($msg, $larchar, "\n", 1)); //ultimo parametro 1 per non tagliare le word
	$msg = str_replace(array("\n","\r"),array("<br />","<br />"),$msg);
	//aggiungere altri filtri per il testo inviato

	$dims['guestbooktnsize'] = min($dims['tnsizes']);

	$img = '';
	if(!empty($fotofile))
	$img = plugins_rawexec('thumbnail', $fotofile, 'guestbook');

	$divcom = '<div class="gmess" id="'.$mid.'"><b>'.$name.'</b> <small>('.$date.')</small>'.
			'<p>'.$img.$msg.'</p>'.
			'</div>';

	$imgphp = '';
	if(!empty($fotofile))
	$imgphp = plugins_rawexec('thumbnail', $fotofile, 'guestbook');
	/*		$imgphp = '<? guestbook_thumbnail("'.$fotofile.'"); ?>';*/

	$divcomphp = '<!--'.$mid.'--><div class="gmess" id="'.$mid.'"><b>'.$name.'</b> <small>('.$date.')</small>'.
			   '<div class="text">'.$imgphp.$msg.'</div>'.
			   "</div><!--/$mid-->"."\r\n\r\n";

	if(!is_file($guestcomfile))
	guestbook_cache();

	put_contents($guestcomfile, $divcomphp.get_contents($guestcomfile));  //!!! 1 righe! :D

	cache_reset();//rigenera la cache html della pagina

	$from = ($email=='') ? false : "$name <$email>";

	guestbook_email($divcom,$from); //email di notifica messaggio all'amministratore

	#$alerts[]= $notif;

	$alerts[]= "Il tuo messaggio &egrave; stato salvato";
  
	$J['head']['ok']= 'true';
	$J['head']['mess']= implode(', ',$alerts);
	$J['data']= $divcom;  
  }
  else {
	$J['head']['ok']= 'false';
	$J['head']['mess']= implode(', ',$alerts);
	$J['data']= '';
  }

	$out = json_encode($J);

	if($ajax)
		echo $out;
}

function guestbook_editlistmess()  //modifica archivio messaggi
{
  global $ajax;
  global $admin;
  global $guestcomfile;

  if(!$admin) return false;

  if(isset($_POST['text']))
  {
	$text = stripslashes($_POST['text']);
	put_contents($guestcomfile,$text);
	cache_reset();
  }
	if($ajax)
	  guestbook_listgmess();
	else
	  $alerts[]= 'I Commenti sono stati modificati';
}

function guestbook_formedit() //interfaccia di modifica archivio messaggi
{
  global $urls;
  global $guestcomfile;
?>
<form id="formeditmess" method="post" action="<?=$urls['current']?>" enctype="application/x-www-form-urlencoded">
	<textarea name="text" id="textmess" rows="10"><?
	if(is_file($guestcomfile))
		get_contents($guestcomfile,true);
	?></textarea><br />
	<input type="submit" name="editlistmess" value="Salva" class="pulsante" />
	&nbsp;<a href="<?=$urls['current']?>" id="resetmess" class="pulsante">Annulla</a>
</form>
<?
}

function guestbook_delmess($idmess) //elimina un singolo messaggio
{
	global $guestcomfile;
	global $alerts;
	global $ajax;

	if(!is_file($guestcomfile)) die("non ci sono messaggi");

	$fr = file($guestcomfile);
	foreach($fr as $r)
	{
		preg_match("<!--([0-9]{10})-->",$r,$m);
		$mess[$m[1]]= $r;
	}
	unset($mess[$idmess]);  //eliminazione messaggio

	$text = count($mess)>0 ? implode('',$mess) : '';

    put_contents($guestcomfile,$text);

	if($ajax) echo 'Messaggio eliminato';
	else $alerts[]= 'Messaggio eliminato';
}

function guestbook_thumbnail($fotofile)  //richiamata quasi sempre attraverso ajax
{
	global $dims;
	global $dirs;
	global $urls;
	global $opts;

	$fotofile = $fotofile;

	if(!is_file($dirs['current'].getcript_filename($fotofile))) return false;

	$opts['thumbquad'] = 1;
	$opts['thumbround'] = 1;
	$tmptnsize = $dims['tnsize'];
	$dims['tnsize'] = $dims['guestbooktnsize'];

	thumb_link($fotofile);

	$dims['tnsize'] = $tmptnsize;
}

function guestbook_listgmess()  //lista messaggi
{
  global $index;
  global $guestcomfile;
  global $opts;
  global $dirs;
  global $urls;
  global $dims;

  if(!is_file($guestcomfile))
	guestbook_cache();

  ?>
  <div id="listgmess_wrap">
  <div id="listgmess">
  <?

  $dims['guestbooktnsize'] = min($dims['tnsizes']);

  readfile($guestcomfile);  //xke dentro ce php che genera le thumbnail

  if($index) //tutti i sotto messaggi
  {
    $dd = rgetdirs();

    $defdircurrent = $dirs['current'];
    $defurlcurrent = $urls['current'];
	$defdircache = $dirs['current'].$opts['thumbdirname'].'/';
	$defurlcache = $urls['current'].$opts['thumbdirname'].'/';

    foreach($dd as $d)
	{
	  $fileguest = $d.'/'.$opts['thumbdirname'].'/'.basename($guestcomfile);

	  if(is_file($fileguest) and filesize($fileguest)>0):

	  $dirs['current'] = $defdircurrent.$d.'/';
	  $urls['current'] = $defurlcurrent.$d.'/';
	  $dirs['cache'] = $dirs['current'].$opts['thumbdirname'].'/';
	  $urls['cache'] = $urls['current'].$opts['thumbdirname'].'/';
	  #$title = strip_tags(plugins_rawexec('thmbtitle',basename($d),false));

	?><div class="albumgmess"><?
	  readfile($fileguest);  //xke dentro ce php che genera le thumbnail
	?>
	<h4><a href="<?=$urls['current']?>">album &raquo;</a></h4>
	</div><?

	  endif;
	  flush();
	}

	$dirs['current'] = $defdircurrent;
	$urls['current'] = $defurlcurrent;
	$dirs['cache'] = $defdircache;
	$urls['cache'] = $defurlcache;
  }
  ?>
  </div>
  </div>
  <?
}

function guestbook_email($mess,$from=false)
{
  global $dirs;
  global $urls;
  global $users;

    //EMAIL DI NOTIFICA
	$mailtext = '<html><head>'.
				"<style type=\"text/css\">\n".plugins_rawexec('css',false,'guestbook')."</style>".
				'</head><body>'.
				$mess.
				(!$from?"<b>L'autore del commento non ha inserito il proprio indirizzo email</b><br />":'').
				'<div style="clear:both"><hr />'.
				'<b>Album:</b> <a target="_blank" href="'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'">'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."</a><br />\n".
				"<b>Ip:</b> ".$_SERVER['REMOTE_ADDR']."<br />\n".
				"<b>Browser:</b> ".$_SERVER['HTTP_USER_AGENT']."<br />\n".
				'</div>'.
				'</body></html>';

	$resp = email($users['admin']['email'],  //to
				'Guestbook Gallery, commento in "'.basename($dirs['current']).'"',  //subject
				$mailtext,  //text
				$from);  //from
    //EMAIL DI NOTIFICA

	return $resp;
}

function guestbook_panel()
{
	global $guestcomfile;
	global $larchar;
	global $dirs;
	global $urls;
	global $admin;
	global $images;
	global $guestcaptcha;

	global $guestname;
	global $guestemail;
	global $guestmess;
?>
<div id="guestform_wrap" class="formwrap">
<div></div>
<form id="guestform" method="post" action="<?=$urls['action']?>">
<div><input type="hidden" name="submit" value="guestbook" /></div>
<div><input id="gfotofile" type="hidden" name="file" value="" /></div>

<div style="float:left">
<label>Nome</label><br />
<input tabindex="1" id="guestname" name="name" type="text" size="<?=intval($larchar/2)?>" value="<?=$guestname?>" title="Il tuo Nome" /><br />
</div>
<div id="gcomthumb" class="loading" style="float:right"></div>

<div style="clear:both">
    <label>Email</label><br />
    <input tabindex="2" id="guestemail" name="email" type="text" size="<?=$larchar?>" value="<?=$guestemail?>" title="Email per la risposta" /><br />
</div>
<div>
    <label>Messaggio</label><br />
    <textarea tabindex="3" id="guestmsg" name="msg" rows="4" cols="<?=$larchar?>" title="Scrivi un messaggio"><?=$guestmess?></textarea>
</div>
<div style="clear:both">
	<? if($guestcaptcha) captcha_form(); ?>
</div>
<div style="clear:both">
<label></label><br />
    <input type="submit" id="guestinvia" value="Invia" class="pulsante" /><label>&nbsp;</label>
    <input type="reset" id="guestannulla" value="Annulla" class="pulsante" />
</div>

</form>
<div id="guestloading">
<p>&nbsp;</p><p>&nbsp;</p><img src="<?=$images['jqueryloadingcircle']['url']?>" alt="Loading..." />
<br /><b>Invio messaggio...</b>
<br /><br /><small><a id="guestcancelsend" href="#" class="pulsante">annulla</a></small></div>
</div>
<br />
<?

  return 'Scrivi un commento';# <img src="'.$images['guestcommenticon'].'">';
} # */

function guestbook_panel_bottom()
{
  global $admin;

  if($admin)
  {
	?><div id="guestadmin"><a href="./?guestbookadmin" class="pulsante">Modifica commenti</a></div><?
  }
  guestbook_listgmess();

  return 'Commenti';
}

function guestbook_js()
{
  global $admin;
  global $dims;
  global $urls;
  global $guestcaptcha;

?>
<? if(false): ?>
<script>
<? endif; ?>

add_thumb_event(function(obj) {
  $(".icon.guestbook",obj).click(function() {
      var fotofile = $(obj).attr('id');
	  $('html, body').scrollTop($("#panel").offset().top);	  
      showfotocom(fotofile);
	  return false;
  });
});

add_panel_event(function(obj) {

if(obj.attr('id')!='panel_guestbook') return false;

	$('#guestform input:reset').bind('click',function() {
		$('#gcomthumb').hide().empty();
		$('#gfotofile').val('');
		<? if($guestcaptcha):?>

			$('#guestform .captcha').hide();

		<? endif; ?>
	});

  $('#guestform input:reset').click();

 // INVIO MESSAGGIO
  $('#guestform').bind("submit",function() {  //con .click() non funziona il collegamento all'evento

	if($('#guestmsg').val()=='')  //sadica ricerca della perfezione... :-s
	{
	  ulgalert('Scrivi il messaggio');
	  $('#guestmsg').focus();
	  return false;
	}
	/*
	if($('#guestemail').val()!='')
	{
	  if( !checkEmail($('#guestemail').val()) )
	  ulgalert('Email non valida');
	  return false;
	}*/

<? if($guestcaptcha):?>
	//poi spostare questo codice in  captcha.php in quelche modo
	if($('#guestform .captcha').css('display')=='none') {
	  $('#guestform .captcha').slideDown(function(){$(this).find('input').focus();});
	  return false;
	}
	if($('#guestform .captchacode').val()=='') {
	  ulgalert('Inserisci il codice di sicurezza');
	  return false;
	}
<? endif; ?>


	if(fotopage==true)
	  $('#gfotofile').val(fotofilecurrent);


    $('#guestloading').show();  // "sending..."

    $.post(ULG.urls.action,
      {
        ajax: "guestbook",
        func: "submit",
        file: $('#gfotofile').val(),
        name: $('#guestname').val(),
		email: $('#guestemail').val(),
        msg: $('#guestmsg').val()<?php if($guestcaptcha):?>,
        captchacode: $('#guestform .captchacode').val()  //spostare in captcha.php un giorno!
		<?php endif; ?>
      },
      function(resp) {

		$('#guestloading').hide();

		ulgalert(resp.head.mess);

		if(resp.head.ok)  //tutto ok
		{
			$('#guestform input:reset').trigger('click');  //nasconde anche captcha
			gmess_add($(resp.data));
		}

<? if($guestcaptcha): ?>

		captcha_refresh($('#guestform'));

<? endif; ?>

      },
	  'json'
    ).error(function() { ulgalert("Errore nell'invio del commento"); });
    
    return false;
  }); // */

	$('#guestcancelsend').click(function() {
		$('#guestloading').hide();
		return false;
	});

});  //fine add_panel_event()


$(document).ready(function() {
  //MODIFICA MESSAGGI
  $('#guestadmin').click(function() {

    $('#listgmess_wrap').addClass('imgloader')
    	.load(ULG.urls.action,
	      {
		  ajax:'guestbook',
		  func:'formedit'
		  },
		  function() {
		  $('#formeditmess',this).submit(function() {

			    var text = $('#textmess',this).val();

				$(this).html('Saving...');

				$.post(ULG.urls.action,
					{
					ajax: 'guestbook',
					func: 'editlistmess',
					text: text
					},
					function(resp) {
					  $('#listgmess_wrap').removeClass('imgloader').html(resp);
					});
					return false;
		        });
		  //reset
		  $('#formeditmess #resetmess',this).click(function() {
		    $(this).parents('#formeditmess').submit();
			return false;
		  });
    });
	return false;
  });
});


function checkEmail(value)
{
     var filter = /([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi;
     if(!filter.test(value))
     {
	    return false;
     }
	return true;
}

function gmess_add(obj)
{
  obj.addClass('new trasp').prependTo('#listgmess')
     .fadeTo("slow",1, function() {
		setTimeout(function() {
		  obj.removeClass('new trasp');
		}, 3000);  //basta cosi anche troppi effetti!!!!
	 });
}

function showfotocom(fotofile)  //da perfezionare
{
    panel_open();
	panel_event($('#panel_guestbook'));

    $('#panel').accordion('activate','.guestbook');  //incorporare come parametro di panel_open()

    $('#guestform input:reset').click();

	$('#gcomthumb')
	  .show()
	  .load(ULG.urls.action,
			{ajax: 'guestbook',
			 func: 'thumbnail',
			 file: fotofile
			},
			function() {
			 $('#gfotofile').val(fotofile);
			 $('#guestname').focus();
			});
}

<? if(false): ?>
</script>
<? endif; ?>
<?
} #*/

function guestbook_css()
{
  global $colors;
  global $images;
  global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#guestform_wrap {
  width: <?=pixem($dims['panelwidth'])?>em;  /* DD ha padding di 4px */
  clear: both;  /* non settare mai float left */
  position:relative;
}

#guestform label,
#guestname, #guestemail,#guestmsg,
#guestinvia, #guestannulla {
  float:left;
}

#guestloading {
	display:none;
	position:absolute;
	top:0;
	left:0;
	width:100%;
	height:100%;
	text-align:center;
	vertical-align:bottom;
	background-color: <?=$colors['bginput']?>;
	border:0.0625em solid <?=$colors['border']?>;
	opacity: 0.8;
}
#guestadmin {
	clear:both;
	margin: 1em 1em 0.5em 0;
	text-align:right;
}
.gmess {
  text-align: left;
  border-top: 0.0625em solid <?=$colors['border']?>;
  margin:0;
  margin-bottom: 1em;
  padding: 0.125em;
  clear:both;
  line-height:0.9em;
  overflow:hidden;
}
.gmess.new {
  border-color: <?=$colors['hover']?>;
  background-color: <?=$colors['bghover']?>;
}
.gmess img { border: 0; }
.gmess .thumb_link {
  margin-right: 0.25em;
  float: left;
  clear:left;
}
.gmess b { /*nome*/
	float:left;
	margin-bottom:0.25em;
}
.gmess small {/*data*/
	float:right;
	color:<?=$colors['border']?>;
}
.gmess .text {/*testo*/
	clear:left;
}
.albumgmess {
  border-right:0.0625em solid <?=$colors['border']?>;
  border-bottom:0.125em solid <?=$colors['border']?>;
  margin:2em 0;
  padding-right:0.25em;
}
.albumgmess h4 {
  font-size:0.8em;
  clear:both;
  text-align:right;
}
#guestmsg {
  width: <?=pixem($dims['panelwidth']-4)?>em;
}
#guestnametd {
  /*height: <?=pixem($dims['guestbooktnsize']-4)?>em;*/
  padding-top:22%;
}
textarea#textmess {
  width:100%;
  height:15em;
}
#gcomthumb {
  display:none;
  float:left;
  padding:0;
  margin:0;
  height: <?=pixem($dims['guestbooktnsize']-8)?>em;
  width: <?=pixem($dims['guestbooktnsize']+8)?>em;
}
#gcomthumb .thumb_link {
  border: 0.25em solid <?=$colors['bgbox']?>;
  border-radius: 0.8em;
  margin:0;
}
#listgmess_wrap {
  padding:0.25em;
  margin:0;
  clear: both;
/*
  overflow: scroll;
  overflow-x: hidden;
  height: 18em; */
  height:auto;

  /*oppure paginare in javascript i messaggi*/
  width: <?=pixem($dims['panelwidth'])?>em;
  float:left;
}
#listgmess {
}

<? if(false): ?>
</style>
<? endif; ?>
<?
}

function guestbook_thumb_menu($fotofile)
{
  global $images;
  global $dims;
  global $opts;
  global $local;
  global $nopanel;

  if($nopanel) return false;

  $fotofile = basename($fotofile);

  if(is_dir($fotofile)) return false;

?><a class="icon guestbook" href="#panel_guestbook" title="Commenta questa foto"><span>Commenta</span><img src="<?=$images['guestcommenticon']['url']?>" /></a><?
  return 'Commenta la foto';
} # */


function guestbook_cache()
{
  global $images;
  global $guestcomfile;
  global $opts;

  @touch($guestcomfile);  //file di cache per i messaggi del guestbook
  @chmod($guestcomfile,CHMOD);

  require('guestbook.cache.php');

  put_contents($images['guestcommenticon']['dir'],$icon1);
}

?>
