<?

function zoom_init()
{
	global $images;
	global $dirs;
	global $urls;  
	global $dims;
	global $opts;
	global $admin;
	global $public;
	global $zoommenu;
	
	$zoommenu = false;  //permette lo zoom globale delle thumb
	
	$images['iconzoomin']['dir'] = $dirs['cache_base'].'_iconzoomin.png';
	$images['iconzoomout']['dir'] = $dirs['cache_base'].'_iconzoomout.png';
	
	$images['iconzoomin']['url'] = $urls['cache_base'].'_iconzoomin.png';
	$images['iconzoomout']['url'] = $urls['cache_base'].'_iconzoomout.png';
	
	if(!$public and !$admin)  //mostra zoomin e zoomout sul menu
	  $zoommenu = false;
}

function zoom_thumbnail($fotofile)
{
	global $dims;
	global $ajax;
	global $opts;

	$curtnsize = $dims['tnsize'];
	$maxtnsize = max($dims['tnsizes']);
    $mintnsize = min($dims['tnsizes']);
    $maxzoomsize = max($dims['tnsizes']);
    $minzoomsize = next_tnsize(min($dims['tnsizes']));
    
    if(!isset($_GET['zoomtype']))
    	return false;
	
	$ztnsize = (int)$_GET['ztnsize'];
	
	if($_GET['zoomtype']=='in')
	{
		$nextz = next_tnsize($ztnsize);
		$dims['tnsize'] = $nextz==$maxtnsize ? $maxzoomsize : $nextz;
		if($nextz>$curtnsize)

		$opts['thumbquad'] = 0;
		$opts['thumbcut'] = 0;
    }
	elseif($_GET['zoomtype']=='out')
	{
	  if($ztnsize<=$dims['tnsize'])  //ridurre di uno step di zoom e' sempre inutile, quindi non fa nulla, cioè crea UNA THUMB CON TNSIZE DI DEFAULT
	  {
		  $backz = back_tnsize($ztnsize);
		  $dims['tnsize'] = $backz==$mintnsize ? $minzoomsize : $backz;
	  }
	}

    thumb_link($fotofile);
}

function zoom_menu()
{
  global $dims;
  global $urls;
  global $images;
  global $zoommenu;
  global $imgfiles;
  
	if(count($imgfiles)==0 or !$zoommenu)
		return false;

    $zid = array_search($dims['tnsize'],$dims['tnsizes']);
    $zin = $dims['tnsizes'][ array_key_exists($zid+1, $dims['tnsizes']) ? $zid+1 : $zid ];
    $zout = $dims['tnsizes'][ array_key_exists($zid-1, $dims['tnsizes']) ? $zid-1 : $zid ];
    
?><a id="menuzoomin" title="Ingrandisci le miniature" href="<?=$urls['action']?>&amp;tnsize=<?=$zin?>"><span>zoom in </span><img src="<?=$images['iconzoomin']['url']?>" alt="zoom in" /></a><?
?><a id="menuzoomout" title="Riduci le miniature" href="<?=$urls['action']?>&amp;tnsize=<?=$zout?>"><span>zoom out </span><img src="<?=$images['iconzoomout']['url']?>" alt="zoom out" /></a><?

  return 'Zoom';
} # */

function zoom_thumb_menu($fotofile)
{
	global $images;
	global $dims;
	
	$fotofile = basename($fotofile);
	
	if(is_dir($fotofile)) return false;  //per ora non usare per gli album

?><a class="icon zoomin" href="#" title="Zoom In"><span>Zoom In</span><img src="<?=$images['iconzoomin']['url']?>" /></a><?
?><a class="icon zoomout" href="#" title="Zoom Out"><span>Zoom Out</span><img src="<?=$images['iconzoomout']['url']?>" /></a><?

  return 'Zoom';
} # */

function zoom_css()
{
    global $images;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
#menuzoomin span,
#menuzoomout span {
  display:none;
}
.icon.zoomout { display:none; }
.zoomedin .icon.zoomin { display:none; }
.zoomedin .icon.zoomout { display:block; }
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function zoom_js()
{
  global $dims;
  global $urls;
  global $zoommenu;
?>
<? if(false): ?>
<script>
<? endif; ?>

add_thumb_event(function(obj) {

     if(obj.is(".photo"))  //gli album, non hanno lo zoom per il moemento
	 {
		$(".icon.zoomin", obj).click(function() {
			zoom(obj,'in');
			$(this).blur();
			return false; //senno scrolla la pagina     
		});

		$(".icon.zoomout", obj).click(function() {
			zoom(obj,'out');
			$(this).blur();
			return false; //senno scrolla la pagina     
		});
    }
});

function zoom(obj,tipo,globale)
{
    var fotofile = obj.attr('id');
    var thumb_wrap = obj.parents('.thumb_wrap');
	var thumb_link = $('.thumb_link',obj);
	var imglink = $('a:first',thumb_link);
	var imgthumb = $('.imgthumb',imglink);
	
	var m = Math.max( imglink.width(),
	                  imglink.height() );  //cosi vale anche per thubnail rettangolari
		
	if ($.browser.msie) m+=4;  //soluzione disperata
	
    var w = globale ? dims["tnsize"] : m;  //se si usa globale ridimensione rispetto alla varibiale globale (utile in menuzoomin)
	
    thumb_wrap.addClass('loading');

	obj.addClass('zoomed'+tipo);
	obj.removeClass('zoomed'+(tipo=='in'?'out':'in'));

	//thumb_wrap.empty();	
	//TODO gestire cache senza sprecare ogni volta chiamate ajax

    $.get(ULG.urls.action,  //forse fare in post
         {
			ajax: 'zoom',
			func: 'thumbnail',
			file: fotofile,
			zoomtype: tipo,  // 'in' or 'out'
			ztnsize: w
		 },
		 function(resp) {
			var new_thumb_link = $(resp);
	        	var new_imgthumb = $('.imgthumb',new_thumb_link);

			new_imgthumb.hide().appendTo('body');  //rendere prima hide()
			
			//se non inserisco imgthumb da qualche parte non posso sapere .witdh() e .height()
			var nw = new_imgthumb.width();
			var nh = new_imgthumb.height();
			var callanim = function() {
				
				//imgthumb.remove();
				//new_imgthumb.show();
				
				imgthumb.attr('src',new_imgthumb.attr('src'));
				new_imgthumb.remove();
				
				thumb_wrap.removeClass('loading');		
				
				//if(tipo=='in') obj.addClass('active select');
				
				if(globale)
				{
					var nm = Math.max( nw,nh );
				    dims["tnsize"] = nm;  //tnsize di default della pagina
				}
				obj.trigger('mouseover');
				//fa ricomparire le icone di zoom a fine animazione!
            
            };//eseguita DOPO l'animazione
			
			//usare$([obj1,obj2,obj3]).animate()
			imgthumb.animate({width: nw,height: nh}, 300);  //.imgthumb vecchia
			obj.animate({width: nw}, 300);
			thumb_wrap.animate({width: nw,height: nh}, 300,callanim);

			//cercare modo di raggruppare sti 3 oggetti in una sola $.animate()
			//USARE $().selector!!!
         });
}

<? if($zoommenu): ?>
$(document).ready(function() {

//menu zoom
    $("#menuzoomin").click(function() {
        $('.page .thumb.photo').each(function(i) {
            zoom($(this),'in',true);
        });
        return false; //senno scrolla la pagina     
    });
    $("#menuzoomout").click(function() {
        $('.page .thumb.photo').each(function(i) {
            zoom($(this),'out',true);
        });
        return false; //senno scrolla la pagina     
    });
});
// */
<? endif; ?>
<? if(false): ?>
</script>
<? endif; ?>
<?
}

function zoom_cache()
{
  global $images;

  require('zoom.cache.php');

  put_contents($images['iconzoomin']['dir'], $icon1);
  put_contents($images['iconzoomout']['dir'], $icon2);
}
?>
