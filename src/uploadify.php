<?

function uploadify_init()
{
	global $maxnumup;
	global $maxfilesizeup;
	global $images;
	global $dirs;
	global $urls;
	global $start;
	global $opts;
    global $ajax;
	global $users;
	global $username;
	global $admin;

	$maxnumup = 20;  //massimo numero di upload contemporanei

	$maxfilesizeup = (islocal()? 10 : 3 )*1024*1024;//MB

	$images['uploadifycancelicon']['dir'] = $dirs['cache_base'].'_upfycancel.png';
	$images['uploadifycancelicon']['url'] = $urls['cache_base'].'_upfycancel.png';

	$images['uploadifyswf']['dir'] = $dirs['cache_base'].'_uploadify.swf';
	$images['uploadifyswf']['url'] = $urls['cache_base'].'_uploadify.swf';
}

if($admin):

function uploadify_submit($fotofile)
{
  global $alerts;

  if(isset($_FILES['upfile']))
    uploadify_upfile();  //non serve passare il parametro $fotofile xke usa solo $_FILES
  else
    uploadify_makealbum($fotofile);
}

function uploadify_upfile()
{
	global $ajax;
	global $alerts;
	global $admin;
	global $imgfiles_decript;
		
	$tempfile = $_FILES['upfile']['tmp_name'];
	$namefile = $_FILES['upfile']['name'];
	$typefile = $_FILES['upfile']['type'];
	$sizefile = round(($_FILES['upfile']['size']/1024)*100)/100;  //dimensioni del file in KBytes

	$out = "";
    if($typefile != "image/jpeg")
      $out .= "<b>Errore:</b><i>$namefile</i> non &egrave; un file .jpg\n";

	$newnamefile = urltitle($namefile);
	$filenames = array_keys($imgfiles_decript);

	if(count($filenames) > 0):	//rinomina file esistenti
		$N = 2;
		$newnamefile_tmp = $newnamefile;
		while(in_array($newnamefile_tmp, $filenames))
			$newnamefile_tmp = preg_replace('/\\.jpg$/', ($N++).'.jpg', $newnamefile);
		$newnamefile = $newnamefile_tmp;
	endif;

    if(is_uploaded_file($tempfile))
    {
	  if(move_uploaded_file($tempfile, $newnamefile))
      {
      	@chmod($newnamefile, CHMOD);
		$newnamefile = check_filename($newnamefile,true);
		//true: per aggiungere il file a $imgfiles_decript

        if($ajax)
			thumb_wrap($newnamefile);  //rinomina il file e poi genera thumb di output
		
        $out .= "<b>File:</b><i>$namefile</i> caricato ($sizefile KB) ";
		cache_reset();//rigenera la cache html della pagina
      }
    }
    else
      $out .= "<b>Errore:</b><i>$namefile</i> non caricato";

  $alerts[]= $out;
}

function uploadify_makealbum($fotofile)
{
  global $ajax;
  global $opts;
  global $plugins;

  $albumtitle = trim($fotofile);

  $dirname = urltitle($albumtitle);

  if(is_dir($dirname))
  {
    //TODO mettere rinominazione automatica se esiste già la dir
    $out = "Esiste gi&agrave; un album che si chiama <b>".$albumtitle."</b>";
    if($ajax) echo $out;
    $alerts[]= $out;  //modalità form
  }
  else
  {
    @mkdir($dirname, CHMOD);
    @chmod($dirname, CHMOD);

	if(isset($plugins['desc']))
		$title = desc_settitle($dirname,$albumtitle);
	//Crea anche il titolo, tramite il plugin desc

    if($ajax)
	  thumb_wrap($dirname);  //crea anche l'index.php

	cache_reset();//rigenera la cache html della pagina

    $alerts[]= "Il nuovo album ".$title." &egrave; stato creato";
  }
}

function uploadify_head_js()
{
  global $urls;
  global $admin;

  if(!$admin) return false;
?>
<script  src="<?=$urls['plugins']?>jquery.uploadify.v2.1.0.min.js"></script>
<?
} # */

function uploadify_panel()
{
  global $err_upload;
  global $maxnumup;
  global $urls;
  global $dims;
  global $maxfilesizeup;
  global $admin;

  if(!$admin) return false;

  ?>
  <div id="upform_wrap" class="formwrap">
  <form id="albumformupfy" action="<?=$urls['action']?>" method="post">
  <input type="hidden" name="submit" value="uploadify" />
      <label>Crea un nuovo album</label><br />
    <input id="ufotofile" type="text" name="file" value="" title="Titolo album" size="15" />
    <input type="submit" value="Crea" class="pulsante" />
  </form>

  <form id="upfileformupfy" action="<?=$urls['action']?>" method="post" enctype="multipart/form-data">
  <input type="hidden" name="submit" value="uploadify" />
    <label>Carica nuove foto</label>
	<br />

	<div id="sfogliaupfile_wrap">
		<input id="sfogliaupfile" type="button" value="Seleziona le foto" title="Seleziona le foto da caricare" class="pulsante" />
		<div id="fileInput_wrap">
			<input id="fileInput" name="fileInput" type="file" />
		</div>
	</div>

	<div id="upfilebuttons">
		<input id="submitupfile" type="submit" value="Carica" class="pulsante" />
		<input id="resetupfile" type="submit" value="Annulla" class="pulsante" />
		<div id="upfylistfile"></div>
	</div>

  	<div style="clear:both"><small>Puoi selezionare fino a <?=$maxnumup?> file<br /> da <?=round(($maxfilesizeup/1024*100)/100,0)?>KBytes ognuno</small></div>

  </form>

  </div>
  <?
  return 'Carica nuove foto';
}

endif; //fine admin

function uploadify_js()
{
  global $maxnumup;
  global $maxfilesizeup;
  global $images;
  global $urls;
  global $dirs;
  global $dims;
  global $colors;
?>
<? if(false): ?>
<script>
<? endif; ?>

var maxidfile = <?=$maxnumup?>;
var upping = false;  //variabile di stato di upload

add_panel_event(function(obj) {

if(obj.attr('id')!='panel_uploadify') return false;

/*$("#panel_uploadify").children("input").bind('',function(e) {
	console.log(e);
});//*/

	var sfogliaupfile = $('#sfogliaupfile');

	var postvars = {ajax:'uploadify', func:'upfile', sid: ULG.opts.sid };
	//aggiunge sessione nel POST
	//xke flash non ha i cookie condivisi col browser

	//opzioni uploadify
	var upfyopts = {
	uploader: '<?=$images['uploadifyswf']['url']?>',
	script: ULG.urls.action,
	scriptData: postvars,
	fileDataName: 'upfile',
	queueID: 'upfylistfile',
	folder: '', //non serve su ulg
	displayData: 'speed',
	buttonText: 'Sfoglia',
	width: sfogliaupfile.outerWidth(),
	height: sfogliaupfile.outerHeight(),
	//fileExt: '*.jpg;*.jpeg',
	fileDesc: 'Immagini JPG',
	//sizeLimit: <?=round($maxfilesizeup)?>,
	queueSizeLimit: <?=$maxnumup?>,
	//onInit: function() {  ulgalert('uploadify init');	//mettere qui controllo se ce flash },
	onOpen: function() {
		upping=true;
	},
	onComplete: function(event,queueID,fileObj,response,data) {
		ulgalert('<b>'+fileObj.name+'</b> caricato, ',true);
		thumb_add( $(response) );
		if(upping==false) ulgalert('Caricamento annullato');
		return true;
	},
	onAllComplete: function(event,data) {
		var nup = data.filesUploaded;
		ulgalert(nup+' foto caricat'+(nup>1?'e':'a')+'!');
		$('#upfilebuttons').hide();
		upping=false;
	},
	onQueueFull: function(event,nn) {
		ulgalert('Puoi caricare fino a '+ nn +' foto contemporaneamente');
		return false; //inibisce alert
	},
	onCancel: function(e,q,f,d) {
		if(d.fileCount<1)
		  $('#upfilebuttons').hide();
	},
	//onProgress: function(e,q,f,d) {	//ulgalert(d.bytesLoaded+', ',true,false); },
	onError: function(e,w,q,t) { ulgalert('errore:'+t.info ,true); },
	onSelectOnce: function() {
		var t = setTimeout(function() {
		  $('.uploadifyQueueItem .cancel a img').replaceWith('<big>&times;</big>');
		},0);
		$('#upfilebuttons').show();
		$('#submitupfile').focus();
	},
	hideButton: true,
	wmode: 'transparent',
	multi: true
	};

	//ulgalert(print_r(postvars),false,false);

	//carica foto
	if($('#fileInput').length>0) //#fileInput esiste solo nell'area amministrativa
	{
		$('#fileInput').uploadify(upfyopts);
		$('#fileInputUploader').hover(function() {
			sfogliaupfile.css({color:'<?=$colors['hover']?>'});
		},function() {
			sfogliaupfile.css({color:'<?=$colors['text']?>'});
		});
	}

	$('#submitupfile').click(function() {
		ulgalert('Caricamento...');
		$('#fileInput').uploadifyUpload();
		$(this).blur();
		return false;
	});

	$('#resetupfile').click(function() {
		if(upping) {
			$('#fileInput').uploadifyCancel();
		} else {
			$('#fileInput').uploadifyClearQueue();
			$('#upfilebuttons').hide();
		}
		upping=false;
		$(this).blur();
		return false;
	});

  //crea album
  $('#albumformupfy').bind("submit",function() {
    var nomealbum = $('#ufotofile').val();
    if(nomealbum!='') {
      $.post(ULG.urls.action,
        {
          ajax: "uploadify",
          func: "makealbum",
          file: nomealbum
        },
        function(resp) {
		  if($(resp).is(".thumb_wrap")) {
            thumb_add($(resp));
			ulgalert("Il nuovo album <b>"+nomealbum+"</b> &egrave; stato creato, puoi entrare e caricare nuove foto");
		  } else {
		    ulgalert(resp);
		  }
          $('#ufotofile').val('');
        });
    }
    return false;
  });

});

//$(document).ready(function() { panel_uploadify_events($('<div id="panel_uploadify">')); });  //se sta dentro panel_bottom

//add_panel_event(panel_uploadify_events);


//$('#content').prepend('<div>'+print_r($('#fileInput'))+'</div>');


<? if(false): ?>
</script>
<? endif; ?>
<?
}


function uploadify_css()
{
  global $colors;
  global $dims;
  global $urls;
  global $images;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#upform_wrap {
	overflow:hidden;
}
#sfogliaupfile_wrap {/*contiene pulsante Sfoglia e swf*/
	float:left;
	position:relative;
	/* overflow:hidden;*/
	margin:.125em 0;
	margin-bottom:.25em;
}
#fileInput_wrap {/*contiene swf*/
	position:absolute;
	top:0;
	left:0;
}
#upfilebuttons {/*contine pulsanti carica,annulla e lista file*/
	display:none;
	clear:both;
}
#upfylistfile { /*lista selezioni*/
	padding:.25em 0;
	margin-top:.5em;
}

.paneltitle.uploadify sup {
	font-size:.75em;
	color:<?=$colors['hover']?>;
}
.uploadifyQueueItem .cancel a big {
	font-weight:bold;
	font-size:1.5em;
	line-height:.5em;
}
#albumformupfy,
#upfileformupfy {
	margin-bottom:1em;
}
.uploadifyQueueItem {
	border: 0.0625em solid <?=$colors['border']?>;
	font-size:small;
	background: <?=$colors['bgbox']?>;
	margin-bottom: .5em;
	padding: .25em;
	border-radius: 0.4em;
}
.uploadifyError {
	border: 0.0625em solid <?=$colors['hover']?>;
	background-color: <?=$colors['bghover']?>;
}
.uploadifyQueueItem .cancel {
	float: right;
}
.uploadifyProgress {
	background: <?=$colors['bgbox']?>;
	border:.125em solid <?=$colors['border'];?>;
	height:.8em;
	border-radius: 0.25em;
}
.uploadifyProgressBar {
	background: <?=$colors['border']?>;
	width: 0;
	height: .8em;
	border-radius: 0.125em;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}


function uploadify_cache()
{
	global $images;

	require('uploadify.cache.php');

	put_contents($images['uploadifycancelicon']['dir'],$icon1);
	put_contents($images['uploadifyswf']['dir'],$swf1);
}

?>
