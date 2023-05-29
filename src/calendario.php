<?

function calendario_init()
{
	global $dirs;
	global $urls;
	global $images;
	global $dims;
	global $opts;
	global $mesi;
	global $mesi_corti;
	global $giorni;
	global $giorni_corti;
	global $masks;

	$mesi = array(1=>'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
	$mesi_corti = array(1=>'Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic');	
	$giorni = array(1=>'Domenica','Lunedi','Martedi','Mercoledi','Giovedi','Venerdi','Sabato');
	$giorni_corti = array(1=>'Do','Lu','Ma','Me','Gi','Ve','Sa');	

	$images['calendaricon']['dir'] = $dirs['cache_base'].'_calendaricon.png';
	$images['calendaricon']['url'] = $urls['cache_base'].'_calendaricon.png';

	$dims['caltnsize'] = intval($dims['panelwidth']/3);

	$masks['calendarfile'] = "_calendar_%s".".txt";

	$opts['calendarphoto'] = false;  //abilita calendario anche per le singole foto
}

function calendario_humandate($timestamp,$ret=false)//tipo facebook
{
	$difference = time() - $timestamp;
	$periods = array("secondi", "minuti", "ore", "giorni", "settimane",
	"mesi", "anni", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");

	if ($difference > 0) { // this was in the past
		$ending = "f&agrave;";
	} else { // this was in the future
		$difference = -$difference;
		$ending = "to go";
	}
	for($j = 0; $difference >= $lengths[$j]; $j++)
	$difference /= $lengths[$j];
	$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	$text = "$difference $periods[$j] $ending";
	return $ret ? $text : print $text;
}


function calendario_submit($fotofile)
{
	if(isset($_POST['date']))
		calendario_setdate($fotofile, $_POST['date']);

	calendario_thumb($fotofile);
}

function calendario_setdate($fotofile,$date) //$date dd-mm-yyyy
{
	global $masks;
	global $dirs;

	$filecalendar = $dirs['data'].sprintf($masks['calendarfile'],basename($fotofile));

	if(preg_match("#[0-9]{2}-[0-9]{2}-[0-9]{4}#",$date))
		put_contents($filecalendar,$date);

	cache_reset();
}

function calendario_getdate($fotofile='.')
{
	global $mesi;
	global $giorni;
	global $masks;
	global $dirs;
	global $opts;

	$criptfotofile = getcript_filename($fotofile);

	if($fotofile=='.')
		$filecalendar = dirname($dirs['current']).'/'.$opts['datadirname'].'/'.sprintf($masks['calendarfile'],basename($dirs['current']));
	else
		$filecalendar = $dirs['data'].sprintf($masks['calendarfile'],basename($fotofile));

	if(is_file($filecalendar))  //file di cache del calendario
	{
		list($giorno,$mese,$anno) = explode('-',get_contents($filecalendar));
	}
	else
	{
		if(is_dir($criptfotofile))
			$fd = filectime($criptfotofile);  //data creazione
		elseif(is_file($criptfotofile))
			$fd = filectime($criptfotofile);
		$anno = date("Y",$fd);
		$giorno = date("d",$fd);
		$mese = date("m",$fd);
	}

	return array('d'=>$giorno,'m'=>$mese,'y'=>$anno);//,'h'=>$ora,'c'=>$fd);
} #*/

function calendario_head()
{
	global $fotopage;

	$date = implode('/',calendario_getdate($fotopage?$_GET['foto'].'.jpg':'.'));
?>
<meta name="creation_date" content="<?=$date?>" />
<?
} # */

function calendario_content_top()
{
	global $dirs;
	global $fotopage;
	global $mesi;
	global $giorni;
	global $index;

  if($index or $fotopage) return false;

  $cal = calendario_getdate();
?>
<div class="calendar" id="calendarpage" title="<?=implode('-',$cal)?>">
<span class="giorno"><?=$cal['d']?></span>
<span class="mese"><?=$mesi[intval($cal['m'])]?></span>
<span class="anno"><?=$cal['y']?></span>
</div>
<?
}

function calendario_thumb($fotofile)
{
	global $opts;
	global $mesi;
	global $giorni;

  if(!$opts['calendarphoto'] and !is_dir(getcript_filename($fotofile))) return false;  //viene eseguito solo per la lista directory

  $cal = calendario_getdate($fotofile);

//  fare in modo che se la data di scatto  diversa da quella dell'album allora la stampi senno no

?>
<div class="calendar" title="<?=implode('-',$cal)?>">
	<span class="giorno"><?=$cal['d']?></span>
	<span class="mese"><?=$mesi[intval($cal['m'])]?></span>
	<span class="anno"><?=$cal['y']?></span>
</div>
  <?
} #*/


function calendario_thumb_menu($fotofile)
{
  global $images;
  global $opts;
  global $admin;

  if(!$admin) return false;

  if( !is_dir(getcript_filename($fotofile)) and !$opts['calendarphoto'] ) return false;

?><a class="icon calendario" href="#" title="Cambia Data"><span>Calendario</span><img src="<?=$images['calendaricon']['url']?>" /></a><?
  return 'Calendario';
} #*/

function calendario_js()
{
  global $colors;
  global $dirs;
	global $mesi;
	global $mesi_corti;
	global $giorni;
	global $giorni_corti;

?>
<? if(false): ?>
<script>
<? endif; ?>

var calwraptmp = [];

function saveCalendar(obj,datefotofile)
{
    var fotofile = $(obj).attr('id');

    obj.css({height:obj.height(),width:obj.width()}).empty().addClass('loading');
	    
	$.post(ULG.urls.action,
		{
			ajax: 'calendario',
			func: 'submit',
			file: fotofile,
			date: datefotofile
		},
		function(resp) {
			$('.calendarinput',obj).datepicker('destroy');
			$('.calendar',calwraptmp[fotofile]).replaceWith(resp);
			obj.removeClass('loading');
			obj.replaceWith(calwraptmp[fotofile]);
			thumb_event(calwraptmp[fotofile]);
		}
	);
}

add_thumb_event(function(obj) {

	$(".icon.calendario", obj).one('click',function() {
	  showcalendar(obj);
	  //obj.addClass('active select');
	  thumb_event(obj);
	  return false;
	});

});

function showcalendar(obj)  //da perfezionare
{
	var fotofile = $(obj).attr('id');
	var thumb_wrap = obj.parents('.thumb_wrap');
	var caltitle = 	$('.calendar',obj).attr('title');

	obj.unbind().removeClass('active');  //annulla animazioni thumb

	calwraptmp[fotofile] = obj.clone();

	obj.css({width:'auto',zIndex:21}).empty();

	var dateoptions = {
				   nextText:'<big class=\'pulsante\'>&raquo;</big>',
                   prevText:'<big class=\'pulsante\'>&laquo;</big>',
				   dateFormat: 'dd-mm-yy',
				   maxDate: '+1y',
				   firstDay: 1,
				   dayNames:['<?=implode("','",$giorni)?>'],
				   dayNamesMin:['<?=implode("','",$giorni_corti)?>'],
				   monthNames:['<?=implode("','",$mesi)?>'],
				   monthNamesShort:['<?=implode("','",$mesi_corti)?>'],				   
				   changeMonth: true,
				   changeYear: true,				   
				   onSelect: function(date,inst) {
				     $('.calendar',calwraptmp[fotofile]).attr('title',date);
				   }};

	var cal = caltitle.split('-');
	var date = new Date();
	date.setFullYear(cal[2]);
	date.setMonth(cal[1]-1);
	date.setDate(cal[0]);

	$('<div class="calendarinput" style="position:relative">')
	.datepicker(dateoptions).datepicker('setDate', date )
	.appendTo(obj);

	obj.prepend('<div class="calbuttons">'+
			    '<input type="submit" value="Salva" class="saveButton pulsante" /> '+
			    '<input type="reset" value="Annulla" class="cancelButton pulsante" />'+
			    '</div>');

	$('.saveButton',obj).click(function() {
		var fotofile = $(obj).attr('id');
		saveCalendar(obj,$('.calendar',calwraptmp[fotofile]).attr('title'));
	  	return false;
	});

	$('.cancelButton',obj).click(function() {
		$('.calendarinput',obj).datepicker('destroy');
		obj.replaceWith(calwraptmp[fotofile]);
		thumb_event(calwraptmp[fotofile]);
	  	return false;
	});
}
<? if(false): ?>
</script>
<? endif; ?>
<?
} #*/

function calendario_css()
{
  global $colors;
  global $dims;
  global $images;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>

.closecal {
	float:right;
	clear:both;
	height:1em;
}
.calbuttons {
	white-space:nowrap;
	margin:0.25em 0;
}
#calendarpage {
	float:right;
	clear: both;
	margin-bottom:.5em;
}
.calendar {
    font-size: 0.85em;
    margin:0;
	padding:.25em;
    background: <?=$colors['background']?>;
    border: 1px solid <?=$colors['border']?>;
	width: 3.3em;
    height: 3.6em;
	position:relative;
	border-radius: 0.4em;
}
.calendar span {
	display:block;
	text-align:center;
}
.calendar .anno {
	position:absolute;
	text-align:center;	
	top:0;
	right:0;
	left:0;
    font-size: 0.75em;
    line-height: 1em;
    letter-spacing: 0.4em;
    background-color: <?=$colors['bgbox']?>;
    border: 2px solid <?=$colors['border']?>;
    margin: -2px;
    padding: 0.125em;
    border-radius: .5em;
}
.calendar .mese {
    display:block;
    font-size: 0.8em;
    text-align:center;
	position:absolute;
	bottom:0;
	left:0;
	width:100%;
}
.calendar .giorno {
    margin-top:0.5em !important;
	margin-top:0.4em;
    display:block;
    font-size: 2em;
    line-height: 1em;
    font-weight: bold;
    text-align:center;
}

.album.list .calendar {
	top:0;
	right:0;
}
.album .calendar {
    position: absolute;
    top: <?=pixem($dims['tnsize']-20)?>em;
    right: 0.5em;
	opacity: 0.7;
}
.album.active .calendar {
	opacity: 1;
}

.ui-datepicker-title {
	text-align:center;
}
.ui-datepicker-next,
.ui-datepicker-prev {
	position:absolute;
	top:0;
}
.ui-datepicker-next { right:0;}
.ui-datepicker-prev { left:0;}
.ui-datepicker .ui-state-default {
	font-size:0.85em;
	font-weight:bold;
	display:block;
	width:1.2em;
	height:1.2em;
	padding:0 .125em .125em 0;
	text-align:center;
	border:.125em solid <?=$colors['border']?>;
	background:<?=$colors['background']?>;
}
.ui-datepicker .ui-state-hover,
.ui-datepicker .ui-state-active {
	background:<?=$colors['bghover']?>;
	border:0.0625em solid <?=$colors['border']?>;
}
.ui-datepicker-current {
	width:auto;
	height:auto;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
}

function calendario_head_js()
{
 global $urls;
 global $admin;

 if(!$admin) return false;
?>
<script  src="<?=$urls['plugins']?>jquery.datepiker.min.js"></script>
<?
} # */

function calendario_cache()
{
  global $images;

  require('calendario.cache.php');

  put_contents($images['calendaricon']['dir'],$icon1);
}

?>
