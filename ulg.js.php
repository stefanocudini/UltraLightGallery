<?

function ulg_head_js()  //questa parte javascript cambia per ogni pagina
{
	global $colors;
	global $dims;
	global $masks;
	global $recache;
	global $panelclose;
	global $dirs;
	global $urls;
	global $imgfiles;
	global $imgdirs;
	global $fotopage;
	global $sessiontime;

	$dims["maxtnsize"] = max($dims["tnsizes"]);
	$urls['fotopageurl'] = $masks["fotopageurl"];

	$config = array(
		'dims'=> $dims,
		'urls'=> $urls,
		'opts'=>array(
			'sid'=>	session_id(),
			'cookies'=>array(
				'expires'=> $sessiontime,
				'path'=> dirname($urls['base']
			))
		),
		'panelclose'=> $panelclose,
		'fotopage'=> $fotopage,
		'fotofilecurrent'=>'',
		'skells'=>array(
			'thumb_menu_album'=> plugins_rawexec('thumbmenu',$imgdirs[0],'ulg'),
			'thumb_menu_photo'=> plugins_rawexec('thumbmenu',null,'ulg')
			//rispetto a  plugins_rawexec('thumb_menu') ulg_thumbmenu restituisce html di contorno
		)		
	);
	//TODO controllare se viene definito dopo tutti gli altri plugin che a loro volta definiscono altre opts
?>
<script>
var ULG = <?php echo json_encode($config); ?>;
</script>
<?
}  //questa parte javascript cambia per ogni pagina

function ulg_js()
{
  global $colors;
  global $tndirsize;
  global $images;
  global $dims;
  global $recache;
  global $panelclose;
  global $dirs;
  global $urls;
  global $masks;

?>
<? if(false): ?>
<script>
<? endif; ?>

ULG.alertclose = null;
ULG.panelautoclose = true;
ULG.fotopageloading = false;
ULG.fotopagesize = {w:0, h:0, top:0};
ULG.scrolltop = false;
//variabili di stato di alcuni oggetti

function ulgrand()  //valore da aggiungere agli url per eliminare il problema della cache
{
  return Math.floor(Math.random()*10000);  //a 5 cifre
}

function ulgalert(msg,appendmsg,autoclose)
{
	appendmsg = typeof(autoclose)=='undefined' ? false : appendmsg;
	autoclose = typeof(autoclose)=='undefined' ? true : autoclose;
	msg = typeof(msg)=='undefined' ? false : msg;
	
	if(!msg)
		$("#alerttext").empty();
	else
	{
		if(appendmsg) $("#alerttext").append(msg);
		else $("#alerttext").empty().html(msg);

		$("#alert_wrap").slideDown();

		if(autoclose)
		{
			clearTimeout(ULG.alertclose);
			ULG.alertclose = setTimeout(function() {
				ulgalertclose();
			}, 2000);
		}
	}

	return true;
}

function ulgalertclose()
{
	$("#alert_wrap").slideUp();
}

function basename(path) {
  //var ret = path.replace( /.*\//, "" ).replace( /.*\\/, "" );
  return path.substring(path.lastIndexOf('/') + 1);
  //return ret;
}

function rm_jpg(path) {
	var ret = path.replace( /\.jpg$/, "" );
	return ret;
}

function dirname(path) {
  var ret = path.match( /(.*)[\/\\]/ )[1];
  /**/
  return ret;
}

function islocal() {
	return (document.location.hostname=='localhost' || document.location.hostname=='127.0.0.1');
}

function thumb_event(obj)
{
  var fotofile = $(obj).attr('id');

  if(obj.is('.photo'))
	obj.append(ULG.skells.thumb_menu_photo);
  else
	obj.append(ULG.skells.thumb_menu_album);

  obj.hover(
	function() { $(this).addClass('active select').children('.thumb_text').css({height: 'auto'}); },
	function() { $(this).removeClass('active select').children('.thumb_text').css({height: '1.125em'}); }
  );

	if($(obj).is('.photo'))
	{
	  obj.children('.thumb_link').children('a:first').on('click',function() {
		  contentfotoshow(fotofile, $(obj).attr('id') );
		  return false;
	  });
	}

  return obj;
}

function add_thumb_event(func)
{
  var events = thumb_event;
  if(typeof thumb_event != 'function') {
    thumb_event = func;
  } else {
    thumb_event = function(obj)
    {
      if(events) events(obj);
      func(obj);
    }
  }
}

function add_panel_event(func)
{
  var events = panel_event;
  if(typeof panel_event != 'function') {
    panel_event = func;
  } else {
    panel_event = function(obj)
    {
      if(events) events(obj);
      func(obj);
    }
  }
}

function thumb_select(objthumb)
{
  $(objthumb).addClass('select');
}

function thumb_add(objthumbwrap)
{
	if(objthumbwrap.hasClass('thumb_wrap')==false) return false;

	var thumbsf$ = $('.thumbs:first');
	
	if(thumbsf$.hasClass('empty')==false)
		thumbsf$.before('<div class="thumbs empty"><div class="page"></div></div>');

	var w = objthumbwrap.css('width');  //larghezza orginale
	var h = objthumbwrap.css('height');  //larghezza orginale

	objthumbwrap
	.css({width:1, height:1})
	.addClass("trasp")  //diventa invisibile
	.prependTo('.thumbs.empty .page')
	.animate({width:w,height:h}, "slow", function() {  //si allarga per fare spazio.. lentamente... con rullo di tanburi.. :)
		$(this)
		.fadeTo("slow",1, function() {  //torna visibile

			$(this).removeClass("trasp");

			thumb_event( $(this).children('.thumb') );
			$(this).children('.thumb').children(".thumb_text").css({height: '1.125em'});
			//attacca gli eventi per la thumb appena aggiunta
		});
	});
}

function thumb_remove(objthumb)  //elimina una thumb e il suo thumb_wrap
{
	var wrap = objthumb.parent();
	objthumb.fadeTo("fast",0, function() {
		wrap.empty();
		wrap.animate({width:1,height:1}, "slow", function() {
			$(this).remove();
		});
	});
}

function applythumbevent() {  //applica gli eventi la prima volta che ci si passa sopra :) 8 ago 2009, 4:02
		thumb_event($(this));
		$(this).trigger('mouseover');
}

function thumbs_event()
{
	$('.thumbs .thumb').each(function(i) {
		$(this).children('.thumb_text').css({height: '1.125em'});
		$(this).one('mouseover', applythumbevent );  //applica eventi la prima volta che ci si passa sopra
	});
}

function panel_close()  //chiude senza animazione
{
	$("#panel").hide();
	$("#panel_hide big").html("&laquo;");
	$("#panel_hide").addClass('closed').attr({title: 'Apri Pannello'}).blur();
	$.cookie('panelclose', 'true',ULG.opts.cookies);
	ULG.panelclose = true;
}

function panel_open()  //chiude senza animazione
{
	$("#panel").show();
	$("#panel_hide big").html("&raquo;");
	$("#panel_hide").removeClass('closed').attr({title: 'Chiudi Pannello'}).blur();
	$.cookie('panelclose', 'false',ULG.opts.cookies);  
	ULG.panelclose = false;
}

function panel_event(obj)  //viene chiamato anche dai pulsanti sulle thumb (guestbook)
{
    var paneltitle = obj.children('.paneltitle'),
    	panelcontent = obj.children('.panelcontent'),
    	plug = obj.attr('id').split('_').pop(),
    	urlpanel = ULG.urls.action+"ajax="+ plug +"&func=panel";

	paneltitle.toggleClass('panel-loading');
	$.ajaxSetup({async:false});
	panelcontent.load( urlpanel );//, function(){	paneltitle.toggleClass('panel-loading');	});
	$.ajaxSetup({async:true});
	paneltitle.toggleClass('panel-loading');
	//QUI a runtime vengono accodati tutti gli eventi dei plugin per i pannelli
}

function panels_event()
{
	$("#panel_hide").on('click',function(e) {

		switch( $.cookie('panelclose') )
		{
			case 'true':
				panel_open();
				break;
			case 'false':
			default:
				panel_close();
		}
		return false;		
	});

	$('#panel')
		.accordion({
			header: 'h3',
			//icons:false,
			autoHeight: false,
			active: false
		});
	
	$('#panel .panelitem').each(function(i) {//riempimento pannello in ajax
	    var panel = $(this);
		//caricamento ajax del pannello(solo al primo click sul titolo del pannello)
		panel.children('.paneltitle').one('click',function(e) {
	        e.preventDefault();
	        e.stopPropagation();
	        $(this).blur();
			panel_event(panel);
		});
	});
}

function fotopage_resize()
{
	var	fotopagediv$ = $('#fotopage'),
		fotodiv$ = fotopagediv$.find('.foto');

	var	margin = 32,//bordo grigio
		w,h,
		body = {
			h: $(window).height(),
			w: $(window).width()
		};
		
	if(ULG.fotopagesize.h > body.h-margin)
	{
		h = body.h-ULG.fotopagesize.top;
		w = (h/ULG.fotopagesize.h) * ULG.fotopagesize.w;//mantiene proporzioni img
		w = parseInt(w);
	}


	// if(w > body.w-margin)
	// {
	// 	w = body.w-ULG.fotopagesize.left;
	// 	h = (w/ULG.fotopagesize.w) * ULG.fotopagesize.h;//mantiene proporzioni img
	// 	h = parseInt(h);
	// }

	if(Math.min(w,h) > ULG.dims.tnsizes[1])
		fotodiv$.css({height:h, width: w})
			.children('img').css({height: h-margin, width: w-margin});
}

function contentfotoshow(fotofile)
{
	var contentdiv$ = $('#content'),
		fotopagediv$ = $('#fotopage'),
		fotopagewrap$ = $('#fotopage_wrap');

	if(fotofile=='' || ULG.fotopageloading)//se sta gia aprendo un altro file..
		return false;
	else
		ULG.fotopageloading = true;
	
    ULG.fotofilecurrent = rm_jpg(fotofile)+'.jpg';  //variabile globale
    ULG.fotopage = true;
	//variabili globali
	
	if($('body').is('#fotopage'))
		location.href = ULG.urls.fotopageurl.replace('%s', rm_jpg(fotofile) );
	else
	{
		//fotopagediv$.find('.foto').hide();
		fotopagewrap$.show();
		$.get('./', {foto: rm_jpg(fotofile) }, function(pagehtml) {

			if(ULG.fotopage==false)//se nel frattempo l'utente ha chiuso #fotopage
				return false;
			
			var wh = $(window).height();

			fotopagediv$.html(pagehtml).css({minHeight: Math.max(wh+160, ULG.dims.maxtnsize+160) });

			var	fotodiv$ = fotopagediv$.find('.foto');
			
			contentdiv$.hide();			
			
			if(!ULG.scrolltop) //solo la prima volta
			{
				$('html, body').scrollTop(fotopagewrap$.offset().top+2);
				//deve stare sempre dopo lo show di fotopagediv
				ULG.scrolltop = true;
			}
			
			fotopagediv$.find('.imgfoto').on('load', function() {
				ULG.fotopageloading = false;
				fotopage_event();
			});

			ULG.fotopagesize.h = fotodiv$.height();
			ULG.fotopagesize.w = fotodiv$.width();
			ULG.fotopagesize.top = fotodiv$.offset().top - fotopagediv$.offset().top;

			fotopage_resize();

			location.href = '#'+rm_jpg(fotofile);

		});	//fine $.get
	}
}

function contentfotoclose()
{
	if($('body').is('#fotopage'))
		return location.href = ULG.urls.current+'#'+ ULG.fotofilecurrent;

	$('#fotopage_wrap').hide().find('#fotopage').empty();
	$('#content').show();
	$(document).off('keydown');
	
	location.href = '#'+ ULG.fotofilecurrent;  //torna alla relativa immagine piccola	   	
	//ULG.fotofilecurrent = '';//causa problemi chiudendo con hotkeys
	ULG.fotopage = false;
	ULG.fotopageloading = false;
	ULG.scrolltop = false;
	ULG.panelautoclose = true;	
}

function fotopage_event()
{
	var fotopagediv$ = $('#fotopage'),
		prevnext$ = $('#prev2,#next2', fotopagediv$);

	fotopagediv$.find('#title a:last, .foto_close').on('click',function() {
		contentfotoclose();
		return false;
	});
	fotopagediv$.find('#prev2, #next2').on('click',function() {
		contentfotoshow( $(this).attr('name') );
		return false;
	});
	fotopagediv$.find('.imgthumb').parent().on('click',function(e) {
		e.preventDefault();
		contentfotoshow( $(this).attr('name') );
	});
	fotopagediv$.find('.foto').on('click',function(e) {
		e.preventDefault();

		var clicks = $(this).data('clicks');

		if(clicks)
			prevnext$.find('span').css({visibility:'hidden'});
		else
			prevnext$.find('span').css({visibility:'visible'});

		$(this).data('clicks', !clicks);
	});
	
	$(document).on('keydown', function(e) {
		switch(e.keyCode)
		{
			case 37:
				fotopagediv$.find('#prev2').trigger('click');
				break;
			case 39:
				fotopagediv$.find('#next2').trigger('click');
				break;
			case 27:
				fotopagediv$.find('.foto_close').trigger('click');
				break;
		}
	});

}

//$(window).load(function() { });

$(function() {

	thumbs_event();
	panels_event();

	$('#alertclose').click(function() {
		ulgalertclose();
		return false;
	});

	if(ULG.fotopage && $('#fotopage').size())
	{
		var fotopagediv$ = $('#fotopage'),
			fotodiv$ = fotopagediv$.find('.foto');
		ULG.fotopagesize.h = fotodiv$.height();
		ULG.fotopagesize.w = fotodiv$.width();
		ULG.fotopagesize.top = fotodiv$.offset().top - fotopagediv$.offset().top;
	
		fotopage_resize();
	}

	$(window).on('resize',function(e) {
		if(ULG.fotopage)
			fotopage_resize();
	});

	var url = document.location.toString();
	var ffile = '';
	if (url.match('#') && (ffile = url.split('#')[1])!='' )
	{
		ffile += '.jpg';
		$(".thumb.photo").each(function() {

			if( ffile == $(this).attr('id') ) {
				contentfotoshow(ffile);
				return false;
			}
		});
		
	}
	//BUG DI JQUERY VULNERABILITA XSS!!!!
	//http://ma.la/jquery_xss/#<img src=/ onerror=alert(1)>

});

<? if(false): ?>
</script>
<? endif; ?>
<?
}
?>