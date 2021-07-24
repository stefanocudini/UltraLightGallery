<?

function refresh_init()
{
  global $images;
  global $dirs;
  global $urls;
  global $recache;
  global $noimgs;
  
  $images['refreshicon']['dir'] = $dirs['cache_base'].'_refreshicon.png';
  $images['refreshicon']['url'] = $urls['cache_base'].'_refreshicon.png';
  
  if(isset($_GET['refreshthumbs']))
    $recache = true;

  if(isset($_GET['refreshcache']))  //non rifà le mianiture quindi le nasconde
    $noimgs = $recache = true;  
}

if($admin)
{

function refresh_menu()
{
  global $images;
  global $urls;

?><a id="refreshthumbs" title="Rigenera tutte le miniature" href="<?=$urls['current']?>?refreshthumbs"><img src="<?=$images['refreshicon']['url']?>" /><span>Miniature</span></a><?
?><a id="refreshcache" title="Rigenera la cache" href="<?=$urls['current']?>?refreshcache"><img src="<?=$images['refreshicon']['url']?>" /><span>Cache</span></a><?

  return 'Rigenera le miniature';
}

function refresh_thumb_menu($fotofile)
{
  global $images;
?><a class="icon refresh" href="#" title="Rigenera la miniatura"><span>Refresh</span><img src="<?=$images['refreshicon']['url']?>" /></a><?
  return 'Rigenera la miniatura';
} # */


function refresh_thumbnail($fotofile)
{
  global $recache;
  $recache = true;
  //plugins_exec('cache',$fotofile);  //MODIFICARE LA GESTIONE DELLA CAHE IN MODO CHE VENGA ESEGUITA PER UN SINGOLO FILE!
  thumb_link($fotofile);
}

function refresh_cacheall()
{
  plugins_exec('cache');
}

} //fine admin

function refresh_js()
{
  global $recache;
  global $urls;
?>
<? if(false): ?>
<script>
<? endif; ?>
  
add_thumb_event(function(obj) {
  $(".icon.refresh",obj).click(function() {
    refreshthumb(obj);
    return false; //senno scrolla la pagina
  });
});

$(document).ready(function() {
    
	$('#refreshthumbs').click(function() {
    
        var msg = $('<span>Rigenerare tutte le miniature?</span>');
        var si = $('<a class="pulsante" id="refreshsi"><span>&nbsp;Si&nbsp;</span></a>');
        var no = $('<a class="pulsante" id="refreshno"><span>&nbsp;No&nbsp;</span></a>');
        
        msg.append(si);
        msg.append(no);
        
        ulgalert(msg);
        
        $(no).click(function() {
            $('#alertclose').click();
        });
        
        $(si).click(function() {
        
            $('.thumb').each(function(i) {
                refreshthumb($(this));
            });
        });
        return false;
    });
	
    $('#refreshcache').click(function() {
	    refreshcache();
		return false;
	});
});

function refreshthumb(obj)
{
	var fotofile = obj.attr('id');

	ulgalert('Aggiornamento miniature...');

	var thumb_wrap = obj.parents('.thumb_wrap');
	var thumb_link = $('.thumb_link',obj);
	var w = $('img',obj).width();

	//thumb_wrap.empty();
	thumb_link.css('visibility','hidden');
	obj.addClass('loading');

	$.get(ULG.urls.action,
	{
		ajax: 'refresh',
		func: 'thumbnail',
		file: fotofile,
		tnsize: w
	},
	function(resp) {
		var n = $(resp);
	
		obj.removeClass('loading');		
		//thumb_wrap.html(n.html());
	
		thumb_link.html(n.html()).css('visibility','visible');
		thumb_event($('.thumb',thumb_wrap));  //xke non viene eseguito lo script di animazione della thumbnail: ani();                                                    
		//thumb_select(obj);
	
	});
	return false;
}

function refreshcache()
{
  ulgalert('Aggiornamento della cache...');
  $.get(ULG.urls.action,
    {
    ajax: 'refresh',
    func: 'cacheall'
    },
    function(resp) {						
      ulgalert('Cache rigenerata');
    });
  return false;
}
<? if(false): ?>
</script>
<? endif; ?>
<?
}


function refresh_cache()
{
  global $images;

  require('refresh.cache.php');

  put_contents($images['refreshicon']['dir'],$icon1);
}


?>
