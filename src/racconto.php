<?

function racconto_init()
{
	global $images;
	global $urls;
	global $dirs;
	global $dims;
	global $masks;

	$masks['raccontofile'] = $dirs['data']."_racconto.txt";  //nome del file che contiene il commento per il file immagine %s
}

if($admin)
{

function racconto_submit($fotofile)  //chiamata via ajax
{
	global $masks;
	global $alerts;
	global $dirs;
	global $ajax;

	$testo = @stripslashes(trim(@$_POST['testo']." "));

	//qui mettere altri filtri

    put_contents($masks['raccontofile'], $testo);
	cache_reset();//rigenera la cache html della pagina
#	$mess = $ok ? "Il racconto &egrave; stato salvato" : "Errore nel salvare il racconto";
//cosi non si può cancellare il racconto
	$ok = true;	
	$mess = "Il racconto &egrave; stato salvato";
	
	$J['head']['ok']= $ok ? 'true' : 'false';
	$J['head']['mess']=$mess;
	$J['data']='';
	$out =json_encode($J);
	if($ajax)
	  echo $out;
	else
	  $alerts[]= $mess;
}

function racconto_head_js()
{
  global $urls;
?>
<script  src="<?=$urls['plugins']?>jquery.wysiwyg.min.js"></script>
<?
}

function racconto_head_css()
{
  global $urls;
?>
<link href="<?=$urls['plugins']?>jquery.wysiwyg.css" rel="stylesheet" type="text/css" />
<?
}


}  //fine admin

function racconto_js()
{
  global $urls;
  global $dirs;
?>
<? if(false): ?>
<script>
<? endif; ?>
$(document).ready(function() {

    $('#editracc').click(function() {

      var optswys = {controls:
              {
              strikeThrough: { visible : true },
              underline:     { visible : true },
			  html: { visible: true },
			  undo: { visible: true },
			  redo: { visible: true },
              insertOrderedList:    { visible : true },
              insertUnorderedList:  { visible : true },
              insertHorizontalRule: { visible : true },
              indent : { visible : true },
              outdent: { visible : true },
              justifyLeft:   { visible : true },
              justifyCenter: { visible : true },
              justifyRight:  { visible : true },
              justifyFull:   { visible : true }
			  }
			 };
	  var wr = $('#racconto').width();
	  $('#racconto').html('<textarea>'+$('#racconto').html()+'</textarea>');

	  $('#racconto textarea').height(200).width(wr).wysiwyg(optswys);
	  $('#saveracc,#cancelracc,#editracc').toggle();
	  return false;
	});

   $('#cancelracc').click(function() {  //annulla cambiamenti
      var testo = $('#racconto textarea').val();  //NON USARE TEXT
	  $('#racconto').html(testo);
	  $('#saveracc,#cancelracc,#editracc').toggle();
	  return false;
   });

   $('#formracconto').bind('submit',function() {

	$('#raccloading').show();  // "sending..."

    $.post(ULG.urls.action,
      {
        ajax: "racconto",
        func: "submit",
        testo: $('#racconto textarea').val()
	  },
      function(resp) {

		if(resp.head.ok)  //tutto ok
		{
		  $('#cancelracc').click();
		}
		$('#raccloading').hide();

		ulgalert(resp.head.mess);

      },
	  'json'
     );
     return false;
   });

});
<? if(false): ?>
</script>
<? endif; ?>
<?
} # */

function racconto_content_top()
{
  global $admin;
  global $masks;
  global $images;
  global $public;
  global $fotopage;

  if($fotopage) return false;

  $testo = get_contents($masks['raccontofile']);

?>
<div class="racconto_wrap">
<? if($admin): ?>
	<form id="formracconto">
		<div id="racconto"><?=$testo?></div>
		<input id="editracc" type="button" name="editracconto" value="Modifica racconto" class="pulsante" />
		<input id="saveracc" style="display:none" type="submit" name="salvaracconto" value="Salva" class="pulsante" />
		<input id="cancelracc" style="display:none" type="reset" name="cancelracconto" value="Annulla" class="pulsante" />
	</form>
	<div id="raccloading">
		<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><img src="<?=$images['jqueryloadingcircle']['url']?>" alt="Loading..." />
		<br />
		<b>Salva racconto...</b>
	</div>
<?
else:
	?><div id="racconto"><?=$testo?></div><?
endif;
?>
</div>
<?

} # */

function racconto_css()
{

  global $colors;

if(false): ?>
<style type="text/css">
<? endif; ?>
.racconto_wrap {
  float: left;
  width:90%;
  position:relative; /*per il loader*/  
}
.racconto_wrap textarea {
	width:100%;
	margin-right:0;
}
#formracconto { margin:0; padding:0;}
#racconto {
  border-bottom:0.0625em solid <?=$colors['border']?>;
  margin-bottom:0.5em;
}
.racconto_wrap a,
.racconto_wrap a:link,
.racconto_wrap a:visited,
.racconto_wrap a:hover{
  text-decoration:underline;
  color:<?=$colors['hover']?>;
}
div.wysiwyg {
  background-color: <?=$colors['bginput']?>;
  color: <?=$colors['text']?>;
  padding:0;
}
div.wysiwyg ul.panel {
  background-color: <?=$colors['border']?>;
  color: <?=$colors['text']?>;
  padding:4px 0;
}
div.wysiwyg ul.panel li a { width: 14px; height: 14px;}

#raccloading {
	display:none;
	position:absolute;
	top:0;
	left:0;
	margin:0 -8px -8px 0;
	width:100%;
	height:100%;
	text-align:center;
	vertical-align:bottom;
	background-color: <?=$colors['bginput']?>;
	border:0.0625em solid <?=$colors['border']?>;
	opacity: 0.8;
}
<? if(false): ?>
</style>
<? endif;
}

function racconto_cache()
{
  global $images;
  global $masks;
  global $dirs;
  global $opts;

  @touch($masks['raccontofile']);
  @chmod($masks['raccontofile'],CHMOD);

  require('racconto.cache.php');

  if(!is_file($dirs['plugins'].'jquery.wysiwyg.gif'))
    put_contents($dirs['plugins'].'jquery.wysiwyg.gif',$iconswy);
} #*/

?>
