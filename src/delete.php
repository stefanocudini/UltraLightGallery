<?

//TODO usare nuova fuzione thumbfdel($fotofile) per eliminare tutte le thumbs
//e tutti i dati collegati alla foto!

function delete_init()
{
	global $images;
	global $urls;
	global $dirs;

	$dirs['deltrash']= $dirs['base']."_trash/";
#	$dirs['deltrash']= false;
	
	$images['deleteicon']['dir'] = $dirs['cache_base'].'_deleteicon.png';
	$images['deleteicon']['url'] = $urls['cache_base'].'_deleteicon.png';
}

if($admin):

function delete_delfile($fotofile)  //richiamata via ajax
{
	global $dirs;

	$criptfotofile = getcript_filename($fotofile);
	
	if($dirs['deltrash']===false)//elimina senza cestino
	{    
		if(is_file($criptfotofile))
			unlink($criptfotofile);
		elseif(is_dir($criptfotofile))
			delete_deldir($criptfotofile);
	}
	else
	    rename($criptfotofile, $dirs['deltrash'].'_del_'.$criptfotofile);

	thumbdel($fotofile);

    cache_reset();//rigenera la cache html della pagina
}

function delete_deldir($fotodir)  //ircorsiva... per bacco!!
{
  if(is_dir($fotodir))
	{
		$files = array();
		$dd = opendir($fotodir);
		while($file = readdir($dd))
		  $files[] = $file;
		closedir($dd);
		
		foreach($files as $file)
			if($file != '.' && $file != '..')
				delete_deldir($fotodir.'/'.$file);
		
		@rmdir($fotodir);
  }
	elseif(is_file($fotodir))
	  @unlink($fotodir);
}

function delete_thumb_menu($fotofile)
{
  global $images;
  global $local;
  global $nomenu;

  if($nomenu) return false;
  
?><a class="icon delete" href="#" title="Elimina"><span>Delete</span><img src="<?=$images['deleteicon']['url']?>" /></a><?
  return 'Elimina';
} # */

endif; //fine admin

function delete_js()
{
  global $urls;
?>
<? if(false): ?>
<script>
<? endif; ?>

var deletefiles = [];  //lista di foto da eliminare

add_thumb_event(function(obj) {
	$(".icon.delete",obj).click(function() {
		deletefile(obj);
		return false;
	});
});

function deletefileserver(obj)
{
	var fotofile = obj.attr('id');

	obj.fadeTo("fast",0.2);
	
	$.get(ULG.urls.action,
		{
		ajax: 'delete',
		func: 'delfile',
		file: fotofile
		},
		function(resp) {
			thumb_remove(obj);
		});
	//*/
}

function deletefile(obj)
{
	var fotofile = obj.attr('id');
	var namefile = '<b>'+fotofile+'</b>';
	var msg = $('<span id="delmsg">Eliminare '+namefile+'?</span>');
	
	if($.inArray(fotofile,deletefiles)>-1) return false;
	//se la foto non presente nella coda di eliminazione
	
	obj.addClass('deleting');
	
	if(deletefiles.length > 0) //se ce gia qualche file in coda, modifica il messaggio
	{
		$('#delmsg b:last').after(', '+namefile);
		msg = $('#delmsg').clone(true); //include gli eventi di si e no
	}
	else
	{
		var si = $('&nbsp;<a href="#" class="pulsante" id="delfilesi"><span>&nbsp;Si&nbsp;</span></a>&nbsp;');
		var no = $('<a href="#" class="pulsante" id="delfileno"><span>&nbsp;No&nbsp;</span></a>');
		
		msg.append(si).append(no);

		$(no).click(function() {
			$('.thumb.deleting').removeClass('deleting');
			$('#alertclose').click();
			deletefiles = [];  //svuota tutta la coda
			return false;
		});	
		$(si).click(function() {
		    $('#alertclose').click();
			$.each(deletefiles,function() {  //elimina la coda di file
				var id = this;
				var obj = $(".thumbs .thumb:[id='"+id+"']");
				deletefileserver(obj);
			});
			deletefiles = [];
			return false;
		});		
	}
	deletefiles.push(fotofile);
	clearTimeout(alertclose);  //impedisce la chiusura se era gia stata innescata
	ulgalert(msg,false,false); //true non fa chiuede l'alert da solo
}
<? if(false): ?>
</script>
<? endif; ?>
<?
}


function delete_css()
{
  global $dims;
  global $colors;
  global $images;
?>
.deleting {
	opacity: .8;
	border-style:dotted;
}
.deleting img {
	opacity: .5;
}
.deleting.thumb {
	background-color: <?=$colors['background']?>;
}
.deleting.thumb.select .thumb_menu {
	display:none;
}

input.checkbox {
	display:block;
	height:20px;
	width:20px;
	z-index: 10;
	border: solid 1px <?=$colors['text']?>;
}
<?
}

function delete_cache()
{
  global $images;
  global $dirs;
  
  if($dirs['deltrash']!==false and !is_dir($dirs['deltrash']))
  {
  	mkdir($dirs['deltrash']);
  	@chmod($dirs['deltrash'],CHMOD);
  }
  
  require('delete.cache.php');
  
  put_contents($images['deleteicon']['dir'],$icon1);
} # */


?>
