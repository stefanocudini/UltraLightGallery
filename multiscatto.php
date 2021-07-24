<?

function multiscatto_init()
{
	global $plugins;
	global $images;
	global $urls;
	global $dirs;	  
    
	#unset($plugins['multiscatto']);

    $images['multiscattoicon']['dir'] = $dirs['cache_base'].'_multiscattoicon.png';
    $images['multiscattoicon']['url'] = $urls['cache_base'].'_multiscattoicon.png';
}


function multiscatto_thumb($fotofile)
{
  global $images;
  
  if(is_file('_2_'.$fotofile))
  { ?><div class="iconseq" style="position:absolute; top:0.2em; right:0.5em;"><img title="Immagini in sequenza" src="<?=$images['multiscattoicon']['url']?>" /></div><? }
} # */


function multiscatto_listhumbs($fotofile)
{
  global $imgfiles;
  global $ajax;
  
  $i = 2;  //numero da cui partono i multiscatto
  
  if( is_dir($fotofile) or !is_file('_'.$i.'_'.$fotofile) ) return false;
  
	/*?><div class="multiscatto"><? # */
	while(is_file('_'.$i.'_'.$fotofile))
	  ulg_thumb_link('_'.$i++.'_'.$fotofile);  //genera: <A><IMG /></A>
	/*?></div><? # */
}

function multiscatto_js()
{
  global $urls;
  global $images;
?>
<? if(false): ?>
<script>
<? endif; ?>
add_thumb_event(function(obj) {

  if($('.iconseq',obj).size()>0)  //se è una foto multiscatto
  {

    var tlink = $(".thumb_link",obj);
    var fotofile = obj.attr('id');
  
	obj.one('mouseover',function() {  //scarica una volta solo i multiscatti della thumb
	
		$('.iconseq img',obj).addClass('imgloader').attr({src:''});
	
		$.get(ULG.urls.action,
			{ajax:'multiscatto',
			func:'listhumbs',
			file:fotofile
			},function(resp) {
				tlink.append(resp);
				tlink.cycle({speed:100,timeout:600});

				obj.hover(
					function() {
						tlink.cycle('resume');
					},
					function() {
						tlink.cycle('pause');
					}
				);//animazione thumb
						
				if(!obj.is('.active'))  //se non ce il mouse sopra la stoppa subito
				  tlink.cycle('pause');
	
				$('.iconseq img',obj)
				.removeClass('imgloader')
				.attr({src:'<?=$images['multiscattoicon']['url']?>'});
			}
		);
	});
	
  }

});
<? if(false): ?>
</script>
<? endif; ?>
<?
} # */

function multiscatto_css()
{
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
.multiscatto {
  float:left;
  border:1px solid red;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function multiscatto_cache()
{
  global $images;

  require('multiscatto.cache.php');

  put_contents($images['multiscattoicon']['dir'],$icon1);
}

?>