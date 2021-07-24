<?

function jquery_init()
{
	global $images;
	global $dirs;
	global $urls;

	$images['jqueryloadingcircle']['dir'] = $dirs['cache_base'].'_loading_circle.gif';
	$images['jqueryloadingcircle']['url'] = $urls['cache_base'].'_loading_circle.gif';
	
	$images['jqueryloadingcirclebig']['dir'] = $dirs['cache_base'].'_loading_circlebig.gif';
	$images['jqueryloadingcirclebig']['url'] = $urls['cache_base'].'_loading_circlebig.gif';
	
	$images['jqueryloadingcircle_b']['dir'] = $dirs['cache_base'].'_loading_circle_b.gif';
	$images['jqueryloadingcircle_b']['url'] = $urls['cache_base'].'_loading_circle_b.gif';
	
	$images['jqueryloadingcircle1']['dir'] = $dirs['cache_base'].'_loading_circle1.gif';
	$images['jqueryloadingcircle1']['url'] = $urls['cache_base'].'_loading_circle1.gif';
	
	$images['jqueryloadingline']['dir'] = $dirs['cache_base'].'_loading_line.gif';
	$images['jqueryloadingline']['url'] = $urls['cache_base'].'_loading_line.gif';

    $images['jqueryloadingline_b']['dir'] = $dirs['cache_base'].'_loading_line_b.gif';
	$images['jqueryloadingline_b']['url'] = $urls['cache_base'].'_loading_line_b.gif';
	
}

function jquery_head_js()
{
	global $urls;         //modificare e far generare tutti i js dentro alla directory root del sito
	global $dirs;
	global $admin;
	global $recache;
	
	$urljs = $urls['base'];  //directory dei js
	
	#$js[] = "jquery-1.7.0.min.js";
	#$js[] = "jquery-2.1.1.min.js";
	$js[] = "jquery-1.8.3.min.js";

	$js[] = "jquery.cookie.min.js";
	$js[] = "jquery.ui-1.8.accordion.js";
	$js[] = "jquery.textarea-expander.min.js";
	$js[] = "swfobject.min.js";
	
	$js[] = "jquery.touchswipe.min.js";

	if($recache or islocal())
		@unlink($dirs['base'].'ulg.lib.js');
	
	if(!is_file($dirs['base'].'ulg.lib.js'))
		foreach($js as $j)
	    	put_contents($dirs['base'].'ulg.lib.js',"\r\n\r\n/*$j*/\r\n".get_contents($dirs['plugins'].$j),true);
?>
<script  src="<?=$urls['base'].'ulg.lib.js'?>"></script>
<?
}

function jquery_css()
{
  global $images;
  global $colors;
?>
.imgloader {
  background-image: url('<?=$images['jqueryloadingcircle']['url']?>');
  background-position: center center;
  background-repeat: no-repeat;
}
.loading {
  background-image: url('<?=$images['jqueryloadingcircle']['url']?>');
  background-position: center center;
  background-repeat: no-repeat;
}
.loadingbig {
  background-image: url('<?=$images['jqueryloadingcirclebig']['url']?>');
  background-position: center center;
  background-repeat: no-repeat;
}
<?
}

function jquery_cache()
{
  global $images;

  require('jquery.cache.php');

  put_contents($images['jqueryloadingcircle']['dir'],$circle);  
  put_contents($images['jqueryloadingcircle_b']['dir'],$circle_b);
  put_contents($images['jqueryloadingcircle1']['dir'],$circle1);
  put_contents($images['jqueryloadingcirclebig']['dir'],$circlebig);
  put_contents($images['jqueryloadingline']['dir'],$line1);
}

?>