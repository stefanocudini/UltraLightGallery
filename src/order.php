<?
	
function order_init()
{
  global $dirs;
  global $urls;
  global $imgfiles;
  global $imgdirs;
  global $images;
  global $ordertypes;  //criteri di ordinamento supportati
  global $masks;
  
  $ordertypes = array("title"=>'Titolo',
					  "date"=>'Data',
					  "size"=>'Dimensioni',
					  "width"=>'Larghezza',
					  "height"=>'Altezza');
	
	$images['orderarrow']['dir'] = $dirs['cache_base'].'_orderarrow.png';
	$images['orderarrow']['url'] = $urls['cache_base'].'_orderarrow.png';
  //creare file di cache con dentro l'ordine dei file

	$masks['orderfile'] = $dirs['data'].'_order.txt';
	
	if(is_file($masks['orderfile']))	//cambia ordine delle foto
	{
		$files = file($masks['orderfile']);
		$files = array_map('trim',$files);
		
		foreach($files as $f)
			if(is_file($f))
				$filesexist[]=$f;

		foreach($imgfiles as $f)
			if(is_file($f) and !in_array($f,$filesexist))
				$filesexist[]=$f;
		
		//salvare su $masks['orderfile'] se sono stati aggiunti o tolti file
		//	diversi da quelli contenuti in $masks['orderfile']

		$imgfiles = $filesexist;
	}
#  $imgfiles = order_sortfiles($dirs['current'], $imgfiles, 'asc');
# $imgdirs = order_sortfiles($dirs['current'], $imgdirs, 'desc');
//ordinamento di default
}

if($admin):

function order_setorder($files)	//salva nuovo ordine delle foto
{
	global $masks;
	global $imgfiles;
	global $imgfiles_decript;
  	
	$files = explode(',',$files);
	$files = array_map('trim',$files);
	$files = array_unique($files);
		
	foreach($files as $f)
		$data .= $imgfiles_decript[$f]."\n";
	
	put_contents($masks['orderfile'], $data );
	
	cache_reset();//rigenera la cache html della pagina
}


function order_content_top()
{
	global $fotopage;
	
	if($fotopage) return false;
?>
<div id="ordermenu">
	<br />
	<input type="button" id="orderbutton" class="pulsante" value="Organizza le Foto" />
	<!--input type="button" id="floatbutton" class="pulsante" value="Raggruppa" /-->
	<input type="button" id="orderbuttonsave" class="pulsante" value="Salva ordine" />
	<input type="button" id="orderbuttoncancel" class="pulsante" value="Annulla" />	
</div>
<?
}#*/

function order_head_js()
{
	global $urls;
?>
<script  src="<?=$urls['plugins']?>jquery.ui-1.8.mouse.sortable.js"></script>
<?
}

endif;//admin

function order_js()
{
  global $urls;
  global $dirs;
?>
<? if(false): ?>
<script>
<? endif; ?>

//$('#orderform input:submit').hide();

$(function() {

$('#orderbutton').on('click',function() {

	$(this).hide();
	$('#orderbuttonsave,#orderbuttoncancel').show();
	
	$('.thumb_wrap.photo').each(function() {
		var id = $(this).children('.thumb').attr('id');
		$(this).wrap('<div id="ord_'+ id +'" class="order_wrap">');
		$(this).after('<div class="over"></div>');
	});
	
	$('.thumbs.photos .page').sortable({items:'.order_wrap'});

	ulgalert('Trascina per cambiare ordinare alle foto');
});

$('#orderbuttonsave').on('click',function(e) {
	var orders  = $('.thumbs.photos .page').sortable('toArray');
	for(s in orders)
		orders[s]= orders[s].substr(4);//toglie 'ord_' all'inizio dell'id
		
	$.post(ULG.urls.action,
		{
		ajax: 'order',
		func: 'setorder',
		file: orders.join(',')
		},
		function(json) {
			ulgalert('Il nuovo ordine delle foto &egrave stato salvato');
			$('.thumbs.photos .page').sortable('destroy');
			$('.thumb_wrap.photo').unwrap();
			$('#orderbuttonsave,#orderbuttoncancel').hide();
			$('#orderbutton').show();
		});//,'json');
});

$('#orderbuttoncancel').on('click',function(e) {
	$('.thumbs.photos .page').sortable('destroy');
	$('.thumb_wrap.photo').unwrap();
	$('#orderbuttonsave,#orderbuttoncancel').hide();
	$('#orderbutton').show();
});

/*
	$('#orderselect').change(function() {
		ordina($('#orderselect').val(),$('#orderasc').val());
		return false;
	});
	$('#orderasc').change(function() {
		ordina($('#orderselect').val(),$('#orderasc').val());
		return false;
	});

	$('#floatbutton').on('click',function() {
		floatop($('.thumbs.photos .page'));
	});	
*/
});

function ordina(o,a)
{
    var t = 'album';
	var contest = $('.thumbs .page:first');

	$.post(ULG.urls.action,  //forse usare GET
		{
		  ajax: "order",
		  func: "submit",
		  ordina: o,
		  asc: a,
		  type: t
		},
		function(resp) {
			var id;
			while(id = resp.pop()) {  //riordina uno per uno, secondo l'id
			  $('.thumb[id="'+id+'"]',contest).parent('.thumb_wrap').prependTo(contest);
			}
		},
		'json'
	);

}


function floatop(content$) { //allinea thumb in alto
  lastParent = 0;
  galleryitems = content$.find('.thumb_wrap');
  galleryitems.each(function(i){
    if(this.parentNode != lastParent) {
      lastTop = 0;
      rowHeight = 0;
      rowStart = i;
      lastParent = this.parentNode;
    }
    this.style.height = 'auto';
    this.style.clear="none";
	this.style.border="1px solid red";
    if(this.offsetTop != lastTop) {
      this.style.clear="left";
      rowHeight = this.offsetTop - lastTop - (this.style.marginTop + this.style.marginBottom);
      for(j=rowStart;j<i;j++) {
        galleryitems.get(j).style.height = rowHeight+"px";
      }
      lastTop = this.offsetTop;
      rowStart = i;
    }
  });
}

<? if(false): ?>
</script>
<? endif; ?>
<?
}


function order_css()
{
    global $colors;
    global $images;
    global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>

#ordermenu {
	clear:both;
	margin-bottom:1em;
}

#orderform_wrap {
	text-align:right;
	clear:both;
}

.order_wrap {
	float:left;
	position:relative;
	border-radius: .5em;
}

.order_wrap .over {
	border: 2px solid <?=$colors['bgbox']?>;
	z-index:100;
	cursor:move;
	position:absolute;
	bottom:0;
	top:0;
	left:0;
	right:0;
	margin:.25em;
	border-radius: .5em;
}
.order_wrap .over:hover {
	border: 2px solid <?=$colors['hover']?>;
	background: url('<?=$images['orderarrow']['url']?>') center center no-repeat <?=$colors['background']?>;
	opacity: .7;
}

#orderbuttonsave,
#orderbuttoncancel {
	display:none;
}

<? if(false): ?>
</style>
<? endif; ?>
<?
}


function order_cache()
{
  global $images;

  require('order.cache.php');

  put_contents($images['orderarrow']['dir'],$icon1);
}

?>
