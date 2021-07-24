<?
$stop=false;
define('V', '3.0');
define('VERSION','Ultra Light Gallery v'.V);
/*
	Copyright Stefano Cudini, since 08/12/2006
	https://opengeo.tech/stefano-cudini/
	stefano.cudini@gmail.com
*/

if(!isset($users)):
	$users['demo'] = array('password'=>'demo','email'=>'demo@example.com','copyright'=>'www.example.com');
	$users['admin'] = $users['demo'];
endif;

$tnsize         = isset($tnsize)         ? $tnsize         : 120;  //dimensioni in pixel delle thumbnails
$tndirsize      = isset($tndirsize)      ? $tndirsize      : 120;  //dimensioni in pixel delle thumbnails per le directory 20-800
$tnmargin       = isset($tnmargin)       ? $tnmargin       : 0;    //margine attorno alla thumb
$thumbquad      = isset($thumbquad)      ? $thumbquad      : 1;    //genera le thumbnails delle foto quadrate
$thumbround     = isset($thumbround)     ? $thumbround     : 0;    //thumbnails quadrate con angoli tondi
$thumbcut       = isset($thumbcut)       ? $thumbcut       : 1;    //taglia le parti della foto che uscirebbero dalla thumbnail
$thumbresampled = isset($thumbresampled) ? $thumbresampled : 1;    //usa imagecopyresampled per create le thumbs, maggiore thumbqualita
$thumbinterlace = isset($thumbinterlace) ? $thumbinterlace : 0;    //genera le immagini thumbnail interlaccate che si scaricano progressivamente
$thumbqualit    = isset($thumbqualit)    ? $thumbqualit    : 85;   //qualità delle thumbnails 0-100
$cachedirname   = isset($cachedirname)   ? $cachedirname   : '_html';    //nome della directory della cache per html e altro
$datadirname    = isset($datadirname)    ? $datadirname    : 'data';     //nome della directory dei dati, esempio titolo,desc,guestbook
$thumbdirname   = isset($thumbdirname)   ? $thumbdirname   : 'thumbs';   //nome della directory della cache per le thumbnail
$thumbempty     = isset($thumbempty)     ? $thumbempty     : '_empty.jpg';  //nome per la thumbnail vuota(vedi in thumbfoto() )
$thumbdirscatter = isset($thumbdirscatter) ? $thumbdirscatter : true;  //immagini non allineante per thumbdir
$thumbdirmargin  = isset($thumbdirmargin)  ? $thumbdirmargin : 2;      //margine delle immagine all'interno della thumbdir

$ulgfile            = isset($ulgfile)    ? $ulgfile    : str_replace('\\','/', __FILE__ );  //file che verra' incluso in tutte le index.php create
$dirs['base']       = isset($dirbase)    ? $dirbase    : str_replace('\\','/',dirname($ulgfile).'/');          //directory principale della gallery
$urls['base']       = isset($urlbase)    ? $urlbase    : '/photos/demo/';   //url principale della gallery
$urls['current']    = isset($urlcurrent) ? $urlcurrent : dirname($_SERVER['PHP_SELF']).'/';  //url dell'album corrente
$dirs['current']    = isset($dircurrent) ? $dircurrent : dirname($_SERVER['SCRIPT_FILENAME']).'/';                           //directory dell'album corrente
$urls['back']       = isset($urlback)    ? $urlback    : dirname($urls['current']).'/'; 
$urls['action']     = isset($urlaction)  ? $urlaction  : './?'.urlclean($_SERVER['QUERY_STRING']);
$urls['plugins']    = isset($urlplugins) ? $urlplugins : $urls['base'];    //url dei plugins, utile per includere javascript di un pèlugin
$dirs['plugins']    = isset($dirplugins) ? $dirplugins : $dirs['base'];    //directory dei plugins
$dirs['data']       = isset($dirdata)    ? $dirdata    : $dirs['current'].$datadirname.'/';  //directory cache dell'album (dove creare thumbnail e file di cache degli album)
$urls['cache']      = isset($urlcache)   ? $urlcache   : $urls['current'].$thumbdirname.'/';  //url cache dell'album (dove creare thumbnail e file di cache degli album)
$dirs['cache']      = isset($dircache)   ? $dircache   : $dirs['current'].$thumbdirname.'/';  //directory cache dell'album (dove creare thumbnail e file di cache degli album)
$urls['cache_base'] = $urls['base'].$thumbdirname.'/';     //url della cache comune a tutti gli album (es: ulg.css,ulg.js)
$dirs['cache_base'] = $dirs['base'].$thumbdirname.'/';     //directory della cache comune a tutti gli album (es: ulg.css,ulg.js)
$urls['cache_html'] = $urls['base'].$cachedirname.'/';     //url della cache comune a tutti gli album (es: ulg.css,ulg.js)
$dirs['cache_html'] = $dirs['base'].$cachedirname.'/';     //directory della cache comune a tutti gli album (es: ulg.css,ulg.js)
//PERCORSI

$username = 'admin';  //username di default
$copyright = isset($copyright) ? $copyright : $users[$username]['copyright']; //testo applicato sulle foto
$copyrightfont = isset($copyrightfont) ? $copyrightfont : null;#$dirs['plugins'].'trebuc.ttf';
$copyrightsize = isset($copyrightsize) ? $copyrightsize : 14; //dimensioni testo in pixel(circa..)
$copyrightlogo = isset($copyrightlogo) ? $copyrightlogo : null; #$dirs['base'].'logo.gif';  //logo applicato sulle foto
//WATERMARK

if(isset($_GET['thumbfoto']) or isset($_GET['thumbdir'])):
	if(isset($_GET['tnsize']))         $tnsize = intval($_GET['tnsize']);
	if(isset($_GET['tnmargin']))       $tnmargin = intval($_GET['tnmargin']);
	if(isset($_GET['thumbcut']))       $thumbcut = intval($_GET['thumbcut']);	
	if(isset($_GET['tndirsize']))      $tndirsize = intval($_GET['tndirsize']);
	if(isset($_GET['thumbquad']))      $thumbquad = intval($_GET['thumbquad']);
	if(isset($_GET['thumbround']))     $thumbround = intval($_GET['thumbround']);
	if(isset($_GET['thumbqualit']))    $thumbqualit = intval($_GET['thumbqualit']);	
	if(isset($_GET['thumbinterlace'])) $thumbinterlace = intval($_GET['thumbinterlace']);
endif;

$dims['tnsizes']        = isset($tnsizes) ? $tnsizes : array(50,120,280,640);  //dimensioni permesse per le thumbnail, 640 e' meta' di 1024, quindi ottimale
$dims['tnsize_default'] = isset($dims['tnsize_default']) ? $dims['tnsize_default'] : $dims['tnsizes'][2];  //dimensione predefiniti delle thumbnail
$dims['tnsize']         = check_tnsize($tnsize);
$dims['tndirsize']      = check_tnsize($tndirsize);
$dims['tnmargin']       = $tnmargin;
$dims['thumbdirmargin'] = $thumbdirmargin;
$dims['tnforpage']      = isset($tnforpage)   ? $tnforpage   : 40;		//thumbs per pagina
$dims['panelwidth']     = isset($panelwidth)  ? $panelwidth  : 200;		//larghezza del pannello #panel
$dims['tnforalbum']     = isset($tnforalbum)  ? $tnforalbum  : 10;		//prime thumbs con immagine per ogni album
$dims['maxgetdirs']     = isset($maxgetdirs)  ? $maxgetdirs  : 100;		//massimo numero di album letti in una directory, utile per velocizzare caricamento in debug
$dims['maxgetfiles']    = isset($maxgetfiles) ? $maxgetfiles : 500;		//massimo numero di file letti in una directory, utile per velocizzare caricamento in debug
if(!defined('CHMOD'))   define('CHMOD', 0775);

$opts['datadirname']    = $datadirname;
$opts['thumbempty']     = $thumbempty;
$opts['thumbquad']      = $thumbquad;
$opts['thumbround']     = $thumbround;
$opts['thumbcut']       = $thumbcut;
$opts['thumbinterlace'] = $thumbinterlace;
$opts['thumbresampled'] = $thumbresampled;
$opts['thumbqualit']    = $thumbqualit;
$opts['thumbdirname']    = $thumbdirname;
$opts['thumbdirscatter'] = $thumbdirscatter;
//TODO funzione optsreset() che ripristina ai valori di default tutte le variabili $dims,$opts,$urls

$masks['thumbfile']   = '%s_%d_%d.jpg';//forse togliere _ iniziale
$masks['thumbdir']    = 'photos_'.$masks['thumbfile'];  //per directory
$masks['fotopageurl'] = '?foto=%s';            //url per visualizzare una pagina con la foto orginale
#$masks['fotopageurl'] = 'foto/%s/';  //da usare con mod_rewrite di apache

$dirs['js']  = isset($dirsjs)  ? $dirsjs  : $dirs['base'].'ulg.js';
$urls['js']  = isset($urlsjs)  ? $urlsjs  : $urls['base'].'ulg.js';
$dirs['css'] = isset($dirscss) ? $dirscss : $dirs['base'].'ulg.css';
$urls['css'] = isset($urlscss) ? $urlscss : $urls['base'].'ulg.css';

$colors['text'] = isset($colors['text']) ? $colors['text'] : '#646464';  //testo
$colors['hover'] = isset($colors['hover']) ? $colors['hover'] : '#ff8000';  //link e oggetti attivi
$colors['bgbox'] = isset($colors['bgbox']) ? $colors['bgbox'] : '#f0f0f0';  //sfondo oggetti
$colors['bgtext'] = isset($colors['bgtext']) ? $colors['bgtext'] : '#646464';  //testo
$colors['border'] = isset($colors['border']) ? $colors['border'] : '#e1e1e1';  //bordi oggetti
$colors['bgthumb'] = isset($colors['bgthumb']) ? $colors['bgthumb'] : '#d7d7d7';  //sfondo delle thumbnails
$colors['bghover'] = isset($colors['bghover']) ? $colors['bghover'] : '#ffd079';  //sfondo oggetti attivi
$colors['bginput'] = isset($colors['bginput']) ? $colors['bginput'] : '#f0f0f0';  //sfondo pulsanti,textbox,textarea
$colors['background'] = isset($colors['background']) ? $colors['background'] : '#ffffff';  //sfondo della pagina
$colors['bgfotopage'] = isset($colors['bgfotopage']) ? $colors['bgfotopage'] : '#bbbbbb';  //sfondo della pagina singola foto
//COLORI

$images    = array();  //lista immagini per la grafica(es.icone,sfondi)
$imgfiles  = array();  //lista nomifile delle foto
$imgfiles_decript = array();  //lista nomifile decriptati $imgfiles_decript =array('nomenormle' => nome_criptato, ...)
$imgdirs   = isset($imgdirs) ? $imgdirs : array();  //lista album del percorso attuale
$alerts    = isset($alerts)  ? $alerts  : array();  //coda messaggi utente
$nofuncs   = isset($nofuncs) ? $nofuncs : array();  //lista delle funzioni disabilitate
$noplugs   = isset($noplugs) ? $noplugs : array();  //lista dei plugins disabilitati
$public    = isset($public)  ? $public  : true;     //dice se l'album atttuale e' pubblico o privato
$index     = isset($index)   ? $index   : ($urls['current']==$urls['base']);   //e' un album principale, il percorso base lo è sempre

$nolistdef = array($thumbdirname, $cachedirname, $datadirname, $thumbempty);
$nolist    = isset($nolist)  ? array_merge($nolist,$nolistdef) : $nolistdef;  //lista dei file e directory da non considerare in getfiles() e getdirs()

$start      = isset($start)    ? $start    : 'ulg';   //indica a quale plugin passare l'output della pagina, dopo init() e cache()
$norun      = isset($norun)    ? $norun    : false;   //non genera nessun output, include solo tutte le funzioni e i plugins(da implementare per utilizzare ULG sottoforma di API)
$noimgs     = isset($noimgs)   ? $noimgs   : false;   //disabilit le thumbnail per velocizzare caricamento della pagina
$nobody     = isset($nobody)   ? $nobody   : false;   //pagina senza <html>,<head> e <body>(per inserire ulg dentro una qualunque pagina html)
$nocss      = isset($nocss)    ? $nocss    : false;   //non include il css
$nojs       = isset($nojs)     ? $nojs     : false;   //versione speriamo funzionante senza nessun javascript
$nofoot     = isset($nofoot)   ? $nofoot   : false;   //nasconde il footer della pagina
$nomenu     = isset($nomenu)   ? $nomenu   : false;   //nasconde il menu principale della pagina
$nobanner   = isset($nobanner) ? $nobanner : false;    //nasconde i banner
$nopanel    = isset($nopanel)  ? $nopanel  : false;    //nasconde il pannello a destra
$panelclose = isset($panelclose) ? $panelclose : false;   //chiude il pannello a destra in onload
//OPZIONI DI VISUALIZZAZIONE

$recache       = false;  //generazione della cache (rigenera i files di cache anche se gia esistono)
$recachethumbs = false;  //viene impostat a true se ce qualche thumb nella pagina che viene rigenerata
$recacheforce  = false;  //se true rigenera la cache delle chiamate a cache_exec()
$cachetime    = isset($cachetime) ? $cachetime : array(3,0,0);  //tempo di utilizzo della cache( array(giorni,ore,minuti) o false )
//STATO DELLA CACHE

$block      = '';    //nome della funzione che si sta eseguendo in plugins_exec
$admin      = false; //stato di autenticazione del proprietario dell'album
$ajax       = false; //dice sei si sta eseguendo una chiamata via ajax
$fotopage   = false; //dice se si sta mostrando la pagina della foto
$listafoto  = false; //pagina semplice con foto grandi, decidere se impostando questa variabile nell'include viene usata come modalità predefinita
$sessiontime = 604800; //durata della sessione di logine dei cookie
//MODALITA' DI ESECUZIONE

ulgStartSession();

$imgfiles = getfiles(); //restituisce i nomi file gia cripttati
$imgdirs  = getdirs();
//inzializzazione lista foto e album

if(!is_dir($dirs['cache_base']) or !is_dir($dirs['cache']))
	$recache = true;

if(!is_file('index.php'))
	makeindex();

/////////// PLUGINS ///////////
if(!isset($plugins)):  //se non è stata definita una lista plugins via FILE-INCLUDE
$plugins = array();
$plugins['ulg'] = null;                              //plugin base, incorporato  //va dopo jquery xke utilizza jquery
$plugins['jquery'] = "jquery.php";                   //libreria javascript/ajax http://www.web2tool.com/visual_jquery/
#$plugins['demo'] = "demo.php";          			 //dati di prensetazione e screenshots di ULG
$plugins['captcha'] = "captcha.php";                 //genera immagine captcha da usare in alcuni form(guestbook)
$plugins['login'] = "login.php";                     //gestore di autenticazione utente
$plugins['fontsize'] = "fontsize.php";               //dimensione dei caratteri della pagina
$plugins['zoom'] = "zoom.php";                       //varie funzionalità di zoom delle thumbnail
$plugins['refresh'] = "refresh.php";                 //aggiornamento del contenuto di una thumbnail
$plugins['uploadify'] = "uploadify.php";             //uploadify, upload multifile in flash (uploadify.com)
#$plugins['importa'] = "importa.php";                 //importa immagini da una pagina di un altro sito
$plugins['delete'] = "delete.php";                   //eliminazione foto via http
$plugins['crop'] = "crop.php";                       //ritaglia foto con jcrop
$plugins['desc'] = "desc.php";                       //aggiunta di descrizione e titolo alle foto
$plugins['popupwindow'] = "popupwindow.php";         //apre le foto in una finestra di popup
$plugins['rotate'] = "rotate.php";                   //ruota una foto
#$plugins['condividi'] = "condividi.php";             //invia la pagina ad altri siti
$plugins['file'] = "file.php";                       //informazioni su file immagine e directory
$plugins['calendario'] = "calendario.php";           //calendario degli album
$plugins['slideshow'] = "slideshow.php";             //apre le foto in una finestra di popup
#$plugins['lastupload'] = "lastupload.php";           //crea lista delle ultime foto caricate
$plugins['guestbook'] = "guestbook.php";             //guestbook per commentare anche le singole foto
#$plugins['disqus'] = "disqus.php";             	  //guestbook per commentare anche le singole foto
$plugins['download'] = "download.php";               //scarica le foto direttamente, e comprime e scarica tutte le foto in un file .zip
//PLUGINS OPZIONALI
$plugins['racconto'] = "racconto.php";               //inserimento di un articolo html dentro un album
$plugins['mappa'] = "mappa.php";                     //inserimento di una mappa con openlayers relativa all'album
$plugins['order'] = "order.php";                     //aggiunge filtro di ordinamento delle foto
$plugins['sidebar'] = "sidebar.php";                 //aggiunge elenco di foto scorrevoli in fotopage
$plugins['exif'] = "exif.php";                       //estrae dati di scatto dalle foto
#$plugins['feed'] = "feed.php";                       //feed RSS ricorsivo per gli album con $index attivo
$plugins['sitemap'] = "sitemap.php";                 //genera mappa del sito in xml in $urls['base']
#$plugins['chat'] = "chat.php";					 	//zopim chat: https://dashboard.zopim.com/#Widget/getting_started
$plugins['analytics'] = "analytics.php";             //google analytics javascript di monitoraggio
//PLUGINS SPERIMENTALI
#$plugins['themes'] = "themes.php";                   //definisce vari temi grafici (tenere sempre per ultimo)
#$plugins['multiscatto'] = "multiscatto.php";         //mostra foto in sequenza su uno stesso div.thumb
#$plugins['vota'] = "vota.php";                       //votazione delle foto
#$plugins['colors'] = "colors.php";                   //calcola colori css da colori delle foto
$plugins['responsive'] = "responsive.php";                //enable support for mobile devices

$plugins['compress'] = "compress.php";                //comprime/offusca css/javascript/html
$plugins['debug'] = "debug.php";                      //funzionalità di debug e svioluppo per creare nuovi plugins (caricare per ultimo)
//DISATTIVA TUTTI i plugins
#$plugins = array('ulg'=>null);
endif;

//TODO definire qui l'ordine di inserimento dei plugin, forse l'ordine per ciascun blocco

include("ulg.css.php");
include("ulg.js.php");
//funzioni messe fuori da ulg.php per comodità

foreach($noplugs as $noplug)  //disabilitazione dei plugins non permessi ($noplugs)
  unset($plugins[$noplug]);

foreach($plugins as $kplug=>$plug)
  if(is_file($dirs['plugins'].$plug)) include($dirs['plugins'].$plug);

plugins_exec('init');   //inizializza i plugin (es. inzializzazione di variabili globali quali $nolist[],$images,$dims,$urls )

if($stop and !$admin) die("<h4>Pagina in Manutenzione</h4>");

//CACHE
if($recache)
{
  plugins_exec('cache');  //genera i file di cache
  $alerts[]= 'Generazione miniature e cache...';
}
elseif(islocal()) //in locale rigenera file javascript e css sempre
{
  ulg_cache();
  if(isset($plugins['compress']))
    compress_cache();
}

//GESTIONE CHIAMATE AJAX e FORM
//TODO if(isajax())
if(isset($_POST['ajax']) and isset($_POST['func'])) //chiamata ajax
{
  $ajax=true;
  plugins_exec(@$_POST['func'], @$_POST['file'], $_POST['ajax']);
  exit(0);
}
elseif(isset($_GET['ajax']) and isset($_GET['func']))  //chiamata ajax
{
  $ajax=true;
  plugins_exec($_GET['func'], @$_GET['file'], $_GET['ajax']);
  exit(0);
}
elseif(isset($_POST['valid']))  //validazione dati in ajax
{
  $ajax=true;
  plugins_exec('valid', $_POST['file'], $_POST['valid']);
  exit(0);
}
elseif(isset($_POST['submit']))
   plugins_exec('submit', null, $_POST['submit']);
elseif(isset($_GET['submit']))
   plugins_exec('submit', null, $_GET['submit']);
//TODO if(isajax())

/////////// INIZIO OUTPUT PAGINA

if(isset($_GET['lista']))     listafoto($_GET['lista'],isset($_GET['links']));  //pagina con la lista semplice delle foto originali(utile per l'esportazione)
if(isset($_GET['thumbfoto'])) thumbfoto($_GET['thumbfoto']);  //genera thumbnail dell'immagine di larghezza massina $tnsize
if(isset($_GET['thumbdir']))  thumbdir($_GET['thumbdir']);  //genera thumbnail per le directory
if(isset($_GET['foto']))      fotopage($_GET['foto']);  //pagina con foto grande

//Output di default della pagina
if(!$norun)
	plugins_exec('start', false, $start);

//solo un plugin alla volta può eseguire lo start

//TODO QUI IMPLEMENTARE CACHE DI TUTTA LA PAGINA
//le request ajax non devono essere cachate qui ma in caso in file separati

/////////// FINE OUTPUT PAGINA

function ulg_start() //output di default
{
	global $nobody;
	global $nobanner;
	global $urls;
	global $plugins;
	global $index;
	global $cachetime;
	
	list($giorni,$ore,$min) = $cachetime;
	$dinsec = pow(60,2)*24*$giorni;
	$hinsec = pow(60,2)*$ore;
	$minsec = 60*$min;
	$cachetimesec = $dinsec + $hinsec + $minsec;
	header('Content-type: text/html; charset=utf-8');
	header('Cache-Control: max-age='.$cachetimesec);
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time()+$cachetimesec));
	//setta cache http con il valore di $cachetime
	
	cache_exec(array(6,0,0),'head');
	
	$bodyclass = $index ? ' class="indice"' :'';

	if(!$nobody) {
		?><body<?=$bodyclass?>><?
	}

	?>
	<table id="pager" cellpadding="0" cellspacing="0">
	<tr>
		<td id="pager_left">
		<?		
		  cache_exec(array(3,0,0),'content');  //contenuto nella pagina

		  flush();
		?>
		</td>
		<td id="content_banner">
		<?
		if(!$nobanner)
			plugins_exec('content_banner');
		?>
		</td>
		<td id="pager_right">
		<?
	       cache_exec(array(3,0,0),'panel');  //pannello a destra della pagina
		?>
		</td>
	</tr>
	</table>
	<?
		cache_exec(null,'menus');
	
	?><div id="foot_banner"><?
		if(!$nobanner)
			plugins_exec('foot_banner');
	?></div>
<?

	foot();  //footer della pagina
    js();  //javascript non cachare xke forse usera' i coockie
	tail();  //chiude <body> e <html>
}

function menus()  //accorpa le funzioni
{
	global $urls;
	
	?>
	<div id="homelink" >
	<?php if($urls['current']!=$urls['base']): ?>
		<a href="<?=$urls['back']?>"><big>&laquo;</big> Indietro</a> &bull;
	<?php endif; ?>
		<a href="<?=$urls['base']?>">Home Gallery</a>
	</div>
	<?php

	menu();        //menu in alto nella pagina
	user_menu();   //menu utente in alto a destra
	alert();       //riquadro per i messaggi all'utente
}

function content()
{
	global $public;
	global $admin;
	global $alerts;
	global $urls;

	?>
	<div id="content">
	<?

	title();  //titolo della pagina <H1>
	text();   //testo della pagina <P>

    if($public or $admin)
    {
		plugins_exec('content_top');
		plugins_exec('content');
		plugins_exec('content_bottom');
    }
    else
	  $alerts[] = "L'album non &egrave; pubblico, accedi per visualizzare il contenuto";

	?>
	</div>
	<div id="fotopage_wrap">
      <div id="fotopage"></div>
	</div>
	<?
}

function ulg_content()
{
    $albums = getdirs();
    $photos = getfiles();
    $nalbums = count($albums);
    $nphotos = count($photos);

  #if(!$public and !$admin) return false;

	if($nalbums>0)
	{
		thumbs($albums);
		$ret = true;
	}

	if($nphotos>0)
	{
		thumbs($photos);
		$ret = true;
	}

	if($nphotos==0 and $nalbums==0)
	{
	  #$alerts[] = "L\'album &egrave; vuoto";
	  //non ci serve mica dirlo :-S
	  ?><div class="thumbs empty"><div class="page"></div></div><?
	}
} # */

function ulg_content2()
{
    $albums = getdirs();
    $photos = getfiles();
    $nalbums = count($albums);
    $nphotos = count($photos);

  #if(!$public and !$admin) return false;

	if($nalbums>0)
	{
		thumbs($albums);
		$ret = true;
	}

	if($nphotos>0)
	{
		thumbs($photos);
		$ret = true;
	}

	if($nphotos==0 and $nalbums==0)
	{
	  #$alerts[] = "L\'album &egrave; vuoto";
	  //non ci serve mica dirlo :-S
	  ?><div class="thumbs empty"><div class="page"></div></div><?
	}
} # */

function panel()
{
  global $urls;
  global $nojs;
  global $nopanel;
  global $admin;

  if($nopanel) return false;

  $panels = plugins_exec('panel',null,false,true);  //quali plugin usano il pannello
  $panelsb = plugins_exec('panel_bottom',null,false,true);  //quali plugin usano il pannello

  if((count($panels) + count($panelsb))==0) return false;

  $pc = (isset($_COOKIE['panelclose']) and $_COOKIE['panelclose']=='true') ? true : false;

  if($admin) $pc=false;  //sempre aperto per amministratore

  ?>
  <table id="pager_panel" cellpadding="0" cellspacing="0">
  <tr>
  <td>
  	<a id="panel_hide" href="#" class="<?=$pc?'closed':''?>" title="<?=$pc?'Apri Pannello':'Chiudi Pannello'?>"><big><?=$pc?'&laquo;':'&raquo;'?></big></a>
  </td>
  <td id="panel" style="<?=$pc?'display:none':''?>">
  <?
  foreach($panels as $plug=>$pan):  	
    ?>
    <div class="panelitem" id="panel_<?=$plug?>">
		<h3 class="paneltitle <?=$plug?>"><a name="<?=$plug?>" href="#"><?=$pan['label']?></a></h3>
		<div class="panelcontent">
		<?
			if($nojs) echo $pan['output'];//inserisce il contenuto subito! invece che richiamarlo via ajax
		?>
		</div>
	</div>
	<?
  endforeach;
  #plugins_exec('panel_bottom');  //senza gestione titoli
  $panelsbottom = plugins_exec('panel_bottom',null,false,true);
  foreach($panelsbottom as $plug=>$pan)
  {
    ?>
	<div>
		<h2 class="paneltitle selected <?=$plug?>"><?=$pan['label']?></h2>
		<div class="panelcontent"><?=$pan['output']?></div>
	</div>
	<?
  }
  ?>
  </td>
  </tr>
  </table>
  <?
}

function thumb_wrap($fotofile)
{
	global $dims;
	global $ajax;
	global $nocss;

	list($w,$h) = thumbsize($fotofile);

    if(!$nocss) $style = ' style="width:'.$w.'px; height:'.$h.'px"';
    else $style = '';

	if(is_dir(getcript_filename($fotofile)))
      $class = 'album';
	elseif(is_file(getcript_filename($fotofile)))
	  $class = 'photo';
	else
	  $class = '';

?><div class="thumb_wrap <?=$class?>"<?=$style?>><?

	thumb($fotofile);

	plugins_exec('thumb_wrap', $fotofile);

?></div><?

}

function thumb_wrap_list($fotofile)  //versione di thumb_wrap() senza immagine
{
	global $dims;
	global $ajax;
	global $nocss;

	if(is_dir(getcript_filename($fotofile)))
      $class = 'album';
	elseif(is_file(getcript_filename($fotofile)))
	  $class = 'photo';
?>
<div class="thumb_wrap list <?=$class?>"><?

    thumb_list($fotofile);

	plugins_exec('thumb_wrap', $fotofile);

?>
</div>
<?
}

function thumb($fotofile)
{
	global $dims;
	global $opts;
	global $ajax;
	global $dirs;
	global $nocss;

	list($w,$h) = thumbsize($fotofile);

	if(!$nocss) $style = ' style="width:'.pixem($w).'em;"';  //l'altezza si deve adattare al contenuto
	else $style = '';

	$thumbid = $fotofile;

?><div class="thumb <?=is_dir(getcript_filename($fotofile))?'album':'photo'?>"  id="<?=$thumbid?>"<?=$style?>><?

	thumb_title($fotofile);
	thumb_link($fotofile);
	thumb_text($fotofile);
	plugins_exec('thumb', $fotofile);

?></div><?
}

function thumb_list($fotofile)  //versione di thumb() senza immagine
{
	global $dims;
	global $ajax;
	global $dirs;
	global $nocss;

	$thumbid = $fotofile;

?><div class="thumb list <?=is_dir(decript_filename($fotofile))?'album':'photo'?>"  id="<?=$thumbid?>"><?

	thumb_title($fotofile);
    $plugs = plugins_exec('thumb_text',$fotofile,false,true);
	$out = array_pop($plugs);
	?><div class="thumb_text"><?=$out['output']?></div><?
	plugins_exec('thumb', $fotofile);

?></div><?
}

function thumb_link($fotofile)
{
	global $dims;
	global $ajax;
	global $urls;
	global $nocss;
	global $noimgs;

	if(is_dir(getcript_filename($fotofile)))
	  makeindex(getcript_filename($fotofile));  //genera dircache e index
	#elseif(!is_file(getcript_filename($fotofile)))
	#  return false;

	if($noimgs)
	  return false;
?>
<div class="thumb_link">
<? plugins_exec('thumb_link', $fotofile); ?>
</div>
<?
}

function ulg_thumb_link($fotofile)
{
	global $recache;
	global $masks;
	global $opts;	
	global $dirs;
	global $urls;
	global $dims;
	global $images;
	global $noimgs;
	global $ajax;
	global $nocss;

	//$criptfotofile = $dirs['current'].getcript_filename($fotofile);
	// if(!is_file($criptfotofile) and !is_dir($criptfotofile))
	// {
	//   echo "<span>$fotofile<br />File Not Found</span>";
	//   return true;
	// }//forse poco utile

	list($w,$h) = thumbsize($fotofile);

	if(is_dir($dirs['current'].$fotofile))
	  $href = $urls['current'].$fotofile.'/';
	else
	{
		$thumbfile = $dirs['cache'].sprintf($masks['thumbfile'], rm_jpg($fotofile),'0', max($dims['tnsizes']) );

		if(is_file($thumbfile))
			$href = $thumbfile;
		else
			$href = $urls['current'].sprintf($masks['fotopageurl'], rm_jpg($fotofile) );
	}

	$src = thumburl($fotofile);
	$alt = thumbalt($fotofile);

    ?><a name="<?=$fotofile?>" href="<?=$href?>"><img class="imgthumb" width="<?=$w?>" height="<?=$h?>" src="<?=$src?>" alt="<?=$alt?>" /></a><?

} # */

function thumb_title($fotofile)
{
	global $dims;
	global $urls;
	global $masks;
	global $ajax;
	global $nocss;

    if($ajax and !$nocss) $style = ' style="width:'.pixem($dims['tnsize']).'em;"';
    else $style = '';

    $plugs = plugins_exec('thumb_title',$fotofile,false,true);

	foreach($plugs as $plug=>$out)
	{
	?><h4<?=$style?> class="thumb_title">
	<? if(is_dir(getcript_filename($fotofile))):?>
	  <a href="<?=$urls['current'].$fotofile.'/'?>"><?=$out['output']?></a>
	<? else: ?>
	  <a href="<?=$urls['current'].sprintf($masks['fotopageurl'],rm_jpg($fotofile))?>"><?=$out['output']?></a>
	<? endif; ?>&nbsp;</h4><?
	}
} # */

function thumb_text($fotofile)
{
	global $urls;

	?><div class="thumb_text"><?

		plugins_exec('thumb_text', $fotofile);

	?></div><?
}


function thumb_menu($fotofile=null)
{
	?><div class="thumb_menu"><? plugins_exec('thumb_menu', $fotofile); ?></div><?
}

function thumbs($imgfiles=array())  //paginazione thumbnail sia album che photo
{
  global $dims;
  global $nojs;
  global $index;
  global $colors;

  if(is_dir($imgfiles[0]))
    $class = 'albums';
  elseif(is_file($imgfiles[0]))
	$class = 'photos';

  ?>
  <div class="thumbs <?=$class?>">
  <?

  $ttot = count($imgfiles);

  $ptot = ceil($ttot/$dims['tnforpage']);

  if($dims['tnforpage']<1) //una sola pagina
  {
	  ?><div class="page"><?
		for($t=0; $t<$ttot; $t++)
		{
			thumb_wrap(decript_filename($imgfiles[$t]));
			flush();
		}
	  ?></div><?
  }
  else
  {
    for($tt=0,$p=1; $p<=$ptot; $p++)
	{
		?>
		<div class="page">
			<?
			for($tp=1; $tp<=$dims['tnforpage'] and $tt<$ttot; $tp++,$tt++)
			{
			    if($index and $class=='albums' and $tt >= $dims['tnforalbum'])
				{
				  if($tt==$dims['tnforalbum'])
				  	echo '<div class="tnforalbumdiv"></div>';
				  thumb_wrap_list(decript_filename($imgfiles[$tt]));
				}
				else
				  thumb_wrap(decript_filename($imgfiles[$tt]));
				flush();
			}
			?>
		  <div class="pagefoot">
		    <span class="fromthumbp"><?=($tt-$tp+2)?></span> - <span class="totthumbp"><?=$tt?></span> su <span class="totthumb"><?=$ttot?></span>
		  </div>
		</div>
		<?
	}
  }
  ?>
  </div>
  <?
}

function ulg_cache()
{
	global $dirs;

    #$recache = makedircache();  //se genera la dir di cache restituisce true
	makedircache();

	#if(!is_file($dirs['js']))
	  @unlink($dirs['js']);
	  plugins_fileexec($dirs['js'],'js');  //generazione cache javascript
	#if(!is_file($dirs['css']))
	  @unlink($dirs['css']);
      plugins_fileexec($dirs['css'],'css');  //generazione cache css
	//spostati prima di start()

} # */

function tail()  //chiude la pagina
{
	global $nobody;

    plugins_exec('tail');

	if($nobody) return false;
?>
<div id="cache"></div>
</body>
</html>
<?
}


function ulg_menu()
{
  global $admin;
  global $urls;
 
  if(!$admin) return false;

  ?><a href="<?php echo $urls['current']; ?>?lista" title="Mostra solo le foto grandi">Lista</a><?
}

function ulg_content_top()
{
	global $copyright;
	global $fotopage;

	if($fotopage) return false;

?><div id="copyright"><?=$copyright?></div><?
}

function ulg_head()  //inserisce i meta tag basilari
{
	global $urls;
	global $dirs;
	global $dims;
	global $opts;
	global $nobody;
	global $fotopage;
	global $users;
	global $username;
	global $nojs;
	global $imgfiles;
	global $imgdirs;
	global $panelclose;
	global $copyright;

	if($nobody) return false;

	if($fotopage) {

		$title = strip_tags( plugins_rawexec('thumb_title', $_GET['foto'].'.jpg') );
		$desc = strip_tags( plugins_rawexec('thumb_text', $_GET['foto'].'.jpg') );

		if( trim($title)=='' )
		{
			$fotopage = false;
			$title = trim(strip_tags(plugins_rawexec('title')));
			$fotopage = true;
		}
		
		if( trim($desc)=='' )
		{
			$fotopage = false;
			$desc = trim(strip_tags(plugins_rawexec('text')));
			if($desc=='')
				$desc= $title;
			$fotopage = true;
		}

	} else {
		$title = trim(strip_tags(plugins_rawexec('title')));
		$desc = trim(strip_tags(plugins_rawexec('text')));
		if($desc=='')
			$desc = $title;
		$desc = 'Foto gallery: '.$desc;
	}

	$title = 'Foto '.urltitle($title,' ');  //elimina entities

?>
<title><?=$title?></title>
<meta name="description" content="<?=$desc?>" />
<meta name="author" content="<?=$copyright?>" />
<meta name="generator" content="<?=VERSION?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=0.85, maximum-scale=1.0, user-scalable=yes" />
<? if(!$fotopage):

	$imagesrc = thumburl('.');
	if(strstr($imagesrc,'?thumbdir'))	//patch per generare subito la image_src
	{
		file_get_contents('http://'.$_SERVER['HTTP_HOST'].$imagesrc);
		$imagesrc = thumburl('.');
	}
?>
<link rel="image_src" href="<?=$imagesrc?>" />
<? endif;
}


function head()
{
	global $nobody;

	if($nobody) return false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
    plugins_exec('head');

	css();
?>
</head>
<?
  	flush();
}

function css()
{
	global $dirs;
	global $urls;
	global $recache;
	global $nocss;

    if($nocss) return false;

    plugins_exec('head_css');  //include tutti i file css necessari ai plugins

	#plugins_fileexec($dirs['css'],'css');
	//forse spostare questa parte su ulg_cache
	//cosi poi compress_cache la comprime
	//cosi i file vengono creati/modificati solo in fase di caching e non di esecuzione come avviene ora

?>
<link href="<?=$urls['css']?>" rel="stylesheet" type="text/css" />
<?
}

function js()  //genera tutto il javascript necessario
{
	global $urls;
	global $dirs;
	global $recache;
	global $alerts;
	global $nojs;

    if($nojs) return false;

	plugins_exec('head_js');  //include tutti i file js necessari ai plugins ed imposta le variabili


   //generazione del file $urls['js'] spostata in ulg_cache
?>
<script src="<?=$urls['js']?>"></script>
<?

	if(count($alerts)>0)  //mostra la coda degli alerts
	{
		$msg = implode(', ',$alerts);
		?>
		<script>ulgalert('<?=trim($msg)?>');</script>
		<?
	}
}

function title()
{
	global $urls;

?><h1 id="title"><? plugins_exec('title'); ?></h1><?
}

function text()
{
?><div id="text"><? plugins_exec('text'); ?>&nbsp;</div><?
}

function menu()
{
 global $nomenu;

 if($nomenu) return false;

?><div id="menu"><?
  $menus = plugins_exec('menu', null, false, true);
  $out = array();
  foreach($menus as $plug=>$menu)
    $out[]= $menu['output'];
  echo implode('| &nbsp;',$out);
?></div><!--/menu--><?
}

function user_menu()
{
 global $nomenu;

 if($nomenu) return false;

?><div id="user_menu"><?
  $menus = plugins_exec('user_menu', null, false, true);
  $out = array();
  foreach($menus as $plug=>$menu)
    $out[]= $menu['output'];
  #$out = array_reverse($out);
  echo implode('<div class="pipe">&nbsp;|&nbsp;</div>',$out);
?></div><?
}

function alert()  //ora funziona anche senza javascript, gli alert genereati da submit vengono subito visualizzati
{
  global $alerts;
  global $nomenu;

  if($nomenu) return false;

?>
<div id="alert_wrap" style="display:<?=count($alerts)?'block':'none'?>"><div id="alert">
<span id="alerttext">&nbsp;<?=count($alerts)?implode(', ',$alerts):''?></span><a href="#" id="alertclose" class="close"><span>&times;</span></a>
</div>
</div>
<?
  $alerts = array();  //gli alert sono gia mostrati per cui non serve piu visualizzarli in javascript
}

function foot()
{
 global $nofoot;

 if($nofoot) return false;

?>
<div id="foot">
<? plugins_exec('foot'); ?>
</div>
<?
}

function plugins_list($func=false)  //restituisce tutti i plugins che implementano la funzione func
{
  global $plugins;

  $func = strtolower($func);
  $out = array();  //array con i nomi dei plugin eseguiti per la funzione $func

    if($func==false)
      return array_keys($plugins);

  foreach($plugins as $nameplug=>$fileplug)
    if(function_exists($nameplug.'_'.$func))
      $out[]= $nameplug;

  return $out;  //restituisce un array con i plugins eseguiti
}

function plugins_reset()  //reimposta i plugin da caricare, passaati come argomenti
{
  global $plugins;
  if (func_num_args()>0)
  {
     $plugs = func_get_args();

     $newplugins = array();

     foreach($plugs as $plug)
       $newplugins[$plug]= $plugins[$plug];

     $plugins = $newplugins;
     return true;
  }
  else
     return false;
}

function plugins_rawexec($func, $par=null, $plugin=false)
//esegue le funzioni base interne dei plugins e restituisce l'output senza valori label
{
  $ret = '';
  $outs = plugins_exec($func, $par, $plugin, true);

  foreach($outs as $plug=>$out)
    $ret .= $out['output'].' ';
  return $ret;
}

function plugins_fileexec($fileout, $func, $par=null, $plugin=false)
//esegue le funzioni base interne dei plugins e restituisce HTML nel file $fileout
//rinominare questa funzione ed utilizzare put_contents()
{
	$out = '';
	$ret = false;

	$fo = fopen($fileout,"w");
	$ret = fwrite($fo, plugins_rawexec($func, $par, $plugin, true) );
	fclose($fo);

	return $ret ? true : false;
}

function plugins_exec($func=false, $par=null, $plugin=false, $return=false)
//esegue le funzioni base interne dei plugins e restituisce HTML in un array
{
  global $nofuncs;
  global $block;
  global $ajax;

  $out = array();  //array restituito da plugins_exec()s e $return==true
  $plugins = array();
  
  #$funcs = is_array($func) ? $funcs : array($func);
  #foreach($funcs as $func):	//esecuzione di piu funzioni con una stessa chiamata ajax

	  if(in_array($func,$nofuncs))
		return $out;//forse 404

	  if($plugin==false or empty($plugin))       //esegue la func di tutti i plugins
		$plugins = plugins_list($func);
	  elseif( in_array($plugin, plugins_list()) )//se e' tra i plugins caricati
		$plugins = array($plugin);               //esegue il foreach qui sotto una sola volta
	  else
	  {
	  	header('HTTP/1.0 404 Not Found');	//se il plugin non ce
		exit(0);
	  }

	  foreach($plugins as $plug)
	  {

		$namefunc = $plug.'_'.$func;

		if(!function_exists($namefunc)) continue;//passa alla funzione nel plugin successivo
	
	 	$block = $namefunc;

		if($return)  //se non serve ritornare output non usa nessun buffer... forse si guadagna RAM
		  ob_start();                    //ritorna l'output per ogni plugin, oppure stampa l'output immediatamente
		
		$label = call_user_func($namefunc, $par);

		if($return)
		  $output = ob_get_clean();

		if($return and $label!==false)
		{
		  $out[$plug]['label'] = $label;
		  $out[$plug]['output'] = $return ? $output : print($output);  //print() restituisce sempre 1
		}
		//OTTIMIZZARE L'ESECUZIONE DI QUESTA PARTE IN CASO DI $return FALSE
	 	$block = '';
	  }
  
  #endforeach;
  
  return $out;
}

function cache_reset($func=false, $par=null)  //annulla la cache e forza la rigenerazione
{
	#$url = str_replace(array("http://".$_SERVER['HTTP_HOST'],'//','/','?','&'),'_',$urls['current']);
	#$cachefile = $dirs['cache_html'].'_'.$func.'_'.$par.$url.'.html';

	//implementare limite a una specifica funzione della pagina! non tutta la pagina!
	//ad esempio solo content()
	
	@touch($_SERVER["SCRIPT_FILENAME"]);//cambia ora di modifica di index.php nell'album
}

function cache_exec($cachet=array(), $func=false, $par=null)  //esegue una funzione e genera cache dell'output
{// $cachet = array($giorni,$ore,$minuti);
	global $dirs;
	global $urls;
	global $admin;
	global $recache;
	global $cachetime;
	global $alerts;
	global $recacheforce;
	global $recachethumbs;
	global $opts;

    if(!function_exists($func)) return false;

	$ret = false;
	$cachet = count($cachet) ? $cachet : $cachetime;
	
	//fare una media del tempo di cache utilizzato e poi impostarlo come default
	$url = str_replace(array("http://".$_SERVER['HTTP_HOST'],'//','/','?','&'),'_',$urls['current']);

	$cachefile = $dirs['cache_html'].'_'.$func.'_'.$par.$url.'.html';
	//fare una maschera per questo file in modo che può essere modificata dai plugin

	if($admin or
	   islocal() or 
	   #isajax() or
	   $cachetime===false)
	{
		if($admin and is_file($cachefile))
			unlink($cachefile);
	    return call_user_func($func, $par);
	}
	else  // visualizza/genera la cache
	{
		list($giorni,$ore,$min) = $cachet;

		$dinsec = pow(60,2)*24*$giorni;
		$hinsec = pow(60,2)*$ore;
		$minsec = 60*$min;
		$cachetimesec = $dinsec + $hinsec + $minsec;
		//conversione in secondi

		if(!is_file($cachefile))
		{
			if(!is_dir($dirs['cache_html']))
				makedircache();

			$expired = true;			
		}
		else
		{
			$expired = $cachetimesec!=0 ? (time() - filemtime($cachefile)) >= $cachetimesec : false;
			
			if( filemtime($_SERVER["SCRIPT_FILENAME"]) > filemtime($cachefile) )
				$recacheforce = true;
			//se e' stata eseguita cache_reset()
		}

		if($recache or
		   $recacheforce or		//usato da login.php
		   $expired)  //genera e mostra la cache
		{
		    if(!$recachethumbs)
				ob_start();

			$ret = call_user_func($func, $par);

			if(!$recachethumbs)
				$output = ob_get_flush();

			if(!$recachethumbs) //mostra ma non salva cache con thumb dinamiche
				put_contents($cachefile,$output);
			else
				$recachethumbs = false;  //resetta il valore
			  //senno da qui in avanti annulla tutte le cache
		}
		else  //mostra la cache
		{
	      readfile($cachefile);  //con questo e' inutile mettere flush sulle thumb_wrap ad esempio
		  flush();
		}
    }
	return $ret;
}

function makedircache($fotodir='.')
{ 
	global $dirs;
	global $opts;

	if(!is_dir($dirs['current'].$fotodir.'/'.$opts['thumbdirname']))
	{
		if(isset($_ENV['windir']))  //windows
		  exec('mkdir '.$fotodir.'\\'.$opts['thumbdirname']);
		else //linux
		{
			$ret = @mkdir($dirs['current'].$fotodir.'/'.$opts['thumbdirname']);
			@chmod($dirs['current'].$fotodir.'/'.$opts['thumbdirname'], CHMOD);
		}
	}

	if(!is_dir($dirs['current'].$fotodir.'/'.$opts['datadirname']))
	{
		if(isset($_ENV['windir']))  //windows
			exec('mkdir '.$fotodir.'\\'.$opts['datadirname']);
		else //linux
		{
			$ret = @mkdir($dirs['current'].$fotodir.'/'.$opts['datadirname']);
			@chmod($dirs['current'].$fotodir.'/'.$opts['datadirname'], CHMOD);
		}
	}

	if(!is_dir($dirs['cache_html']))
	{
		if(isset($_ENV['windir']))  //windows
		  exec('mkdir '.str_replace('/','\\',$dirs['cache_html']));
		else //linux
		{
			$ret = @mkdir($dirs['cache_html']);
			@chmod($dirs['cache_html'], CHMOD);
		}
	}
	else
		$ret = false;

    return $ret;
}

function makeindex($fotodir='.',$options=array())  //chiamata dentro thumb
{
	global $users;
	global $username;
	global $opts;
	global $ulgfile;
	global $dims;

    if(!is_dir($fotodir)) return false;

#   if($recache and is_file("$fotofile/index.php")) @unlink("$fotofile/index.php");
#   echo "[[[$fotodir/index.php]]]";

    if(!is_file("$fotodir/index.php"))
    {
    
		$options = count($options)>0 ? $options : array(
					'#$users = array("user"=>array("password"=>"password","copyright"=>"Foto di "));',
					'#$tnsizes = array('.implode(',',$dims['tnsizes']).');',
					'#$index = true;',
					'#$tnmargin = 0;',
					'#$copyright="";');

        $out = "<?"."\r\n".
               "/* powered by ".VERSION." */"."\r\n".
			   "/* make from: ".$username." on: ".date("d/m/Y H:i:s")." */"."\r\n".
			   //sistemare questa cosa approssimativissima
			   //includere file config dell'utente
				implode("\r\n", $options)."\r\n".
                "@require_once('".$ulgfile."');"."\r\n".
                "?>";

	    put_contents("$fotodir/index.php", $out);
    }
}

//helper per data storage dei plugins
function put_contents($filename,$text,$append=false)  //mette dati in un file... oppure anche in un db con id == filename
{
  global $dirs;
  global $opts;

  $fc = @fopen($filename,$append?'ab':'wb');
  $w = @fwrite($fc, $text);
  @fclose($fc);
  @chmod($filename, CHMOD);	//poi vedere se è il caso

  return $w ? true : false;  //poi mettere condizione
}

function get_contents($filename,$stamp=false)  //legge dati da un file... oppure anche in un db con id == filename
{
  global $dirs;

  if($stamp)
    readfile($filename);
  else
  {
  	  $fc = @fopen($filename,"rb");
	  $text = @fread($fc,@filesize($filename));
	  @fclose($fc);
      return $text;
  }

}


function sortfiles($dir='.',$files,$ord='desc')  //ordina files e directory secondo la data di creazione
{
    $sfiles = array();
    $dates = array();

    foreach($files as $file)
        $dates[$file]= filectime($dir.'/'.$file);

    if($ord=='desc')
        arsort($dates);
    elseif($ord=='asc')
        asort($dates);

    reset($dates);

    $sfiles = array_keys($dates);

    return $sfiles;
}

//USARE serialize e cache_exec()

function rgetfiles($start_dir='.')  //lista files ricorsiva
{
  $files = array();

  if(is_dir($start_dir))
  {
	foreach(getfiles($start_dir) as $file)
		array_push($files, ($start_dir=='.'?'':$start_dir.'/').$file);

	foreach(getdirs($start_dir) as $dir)
		$files = array_merge($files, rgetfiles(($start_dir=='.'?'':$start_dir.'/').$dir));
  }
    else
    $files = false;

  return $files;
}

function rgetdirs($start_dir='.', $retarray=false)  //lista directory ricorsiva
{
	$dirs = array();
	$rdir = array();

	if(is_dir($start_dir))
	{
		$dadd = $start_dir=='.' ? '' : $start_dir.'/';
		
		if($retarray):
			foreach(getdirs($start_dir) as $dir)
				$dirs[$dadd.$dir]= rgetdirs($dadd.$dir,true);
		else:
			foreach(getdirs($start_dir) as $dir)
				array_push($dirs, $dadd.$dir);
			foreach(getdirs($start_dir) as $dir)
			{
				$rdir = rgetdirs($dadd.$dir);
				$dirs = is_array($rdir) ? array_merge($dirs, $rdir) : array();
			}
		endif;
	}
	else
		$dirs = false;

	return $dirs;
}

function getcript_filename($fotofile)  //resituisce il nome del file criptato relativo al file originale $fotofile
{
	global $imgfiles_decript;
	global $dirs;
	
	$ret = null;

	if(is_dir($fotofile))
		$ret = $fotofile;
	elseif(isset($imgfiles_decript[$fotofile]))
		$ret = $imgfiles_decript[$fotofile];

	return $ret;
}

function rm_jpg($fotofile)
{
	return preg_replace('/\\.jpg$/', '', $fotofile);
}

function cript_filename($fotofile)  //cripta il nome della foto, se non è gia stata crittata
{
  if(is_dir($fotofile))
    $ret = $fotofile;
  elseif(decript_filename($fotofile)==$fotofile) //se non è gia stato crittato
    $ret = strtolower(substr(md5($fotofile.time()),0,5)).'_'.basename($fotofile);  //codice casuale
  else
    $ret = $fotofile;  //è gia crittato
  return $ret;
}

function decript_filename($fotofilecript)
{
  if(is_dir($fotofilecript))
    $ret = $fotofilecript;
  elseif( preg_match("/^[a-z0-9]{5}_([-a-z0-9_.]+\.(jpg|jpeg|gif|png))$/",basename($fotofilecript),$filename) )  
    $ret = $filename[1];
  else
    $ret = $fotofilecript; //se non è un file crittato lo restituisce uguale
  return $ret;
}

function check_filename($filename,$cript=false)  //$t=true modifica il nome di un file di foto o album usando urltitle
{
	global $imgfiles_decript;

	$dir = dirname($filename)!='.' ? dirname($filename).'/' : '';
    $fil = urldecode(basename($filename));

	$urltitle = urltitle($fil);

	$criptfile = is_dir($fil) ? $urltitle : cript_filename($urltitle);
	//non cripta nome delle directory

	if($cript)
		$imgfiles_decript[ decript_filename($urltitle) ]= $criptfile;
	//critta il nome file

	if($fil != $criptfile)
		@rename($dir.$fil, $dir.$criptfile);

	return $dir.$urltitle;
}

//da modificare, aggiungendo i tipi di immagini supportate, passandolo come parametro
function getfiles($dir = '.',$max=0)  //restituisce un array con i nomi dei file immagine in una directory
{
    global $nolist;
    global $dims;
	global $dirs;
    global $imgfiles;
    global $imgfiles_decript;
    global $public;
    global $admin;
    global $block;

    if(!$public and !$admin) return array();
    //FONDAMENTALE!!!

	$dir = empty($dir) ? '.' : $dir;//non togliere utile sulle chiamate ajax con parametro FILE vuoto	
	
	$du = $dir=='.';
	
    if($du and !empty($imgfiles))  //non ripete la scansione della directory CORRENTE
    	return $imgfiles;

	$files=array();
    $dd = opendir($dirs['current'].$dir);
    $i = 0;
    $maxf = $max!=0 ? abs($max-1) : $dims['maxgetfiles'];
    while($fil = readdir($dd))
	{
		if( !is_dir($fil) and (strtolower(substr($fil,-4))=='.jpg' or strtolower(substr($fil,-5))=='.jpeg') and !in_array($fil,$nolist) and $fil{0}!='_' and $fil{0}!='.' )
		#pathinfo($fil); usare la chiave 'extension' per il tipo di file
		#strtolower(array_pop(explode('.',$filename)))  //alternativa
		#oppure: strtolower(array_pop(explode('.',$f)))=='jpg'
		
		{
		  $files[$i] = check_filename($fil,$du);
 		  //check_filename() esegue: $imgfiles_decript[decript_filename($urltitle)]= $criptfile;

		  #list($files[$i]['w'],$files[$i]['h']) = getimagesize("$dir/$fil");  //da sistemare gli altri attributi
		  #$files[$i]['s'] = round((filesize("$dir/$fil")/1024)*100)/100;  //dimensione file in KBytes
		  if(++$i>$maxf) break;
		}
    }
	closedir($dd);
	
	if($du) {  //ordina secondo il nome decrittato, PER ORA!!! solo file dell'album attuale!! :-s
	ksort($imgfiles_decript);
	$files = array_values($imgfiles_decript);
	}

    return $files;
}

function getdirs($dir = '.',$max=0)  //restituisce un array con i nomi delle directory dentro $dir
{
    global $nolist;
    global $dims;
    global $dirs;
    global $imgdirs;
    global $public;
    global $admin;

    if(!$public and !$admin) return array();
    //FONDAMENTALE!!!

	if($dir=='.' and !empty($imgdirs))  //non ripete la scansione della directory
		return $imgdirs;

	$dir = empty($dir) ? '.' : $dir;//non togliere utile sulle chiamate ajax con parametro FILE vuoto

	$files = array();
	$dd = opendir($dirs['current'].$dir);

	$i = 0;
	$maxf = $max!=0 ? abs($max-1) : $dims['maxgetdirs'];
	//con php5 usare: scandir()
	while($fil = readdir($dd))
		if( is_dir($dir.'/'.$fil) and $fil!="." and $fil!=".." and !in_array($fil,$nolist) and $fil{0}!='_' and $fil{0}!='.' ) #and ( $nf=count(getfiles($fil))-1 )>0 )
		{
		  $files[$i]= check_filename($fil);  //rinomina anche il file se ha nomi strani

		  #list($files[$i]['w'],$files[$i]['h']) = GetImageSize("$dir/$fil");  //da sistemare gli altri attributi
		  #$files[$i]['s'] = round((filesize("$dir/$fil")/1024)*100)/100;  //dimensione file in KBytes
		  if(++$i>$maxf) break;
		}
	closedir($dd);
	sort($files);
	return $files;
} # */


function getnext($fotofile,$n=1)
{
  global $imgfiles;
    $pos = thumbid($fotofile);
    $fnext = ($pos+$n<count($imgfiles)) ? $imgfiles[$pos+$n] : '';
    return $fnext;
}

function getback($fotofile,$n=1)
{
  global $imgfiles;
    $pos = thumbid($fotofile);
    $fback = ($pos-$n>=0) ? $imgfiles[$pos-$n] : '';
    return $fback;
}

function check_tnsize($tnsize)  //sistemare: restituire il valore piu vicino al valore richiesto!!!!
{
  global $dims;

    if(in_array($tnsize,$dims['tnsizes']))
      return $tnsize;
    else
	{
//IN DATA 19 nov 2008 ore 4:30 ho risolto il problema! :)
	  foreach($dims['tnsizes'] as $s)
		$rr[]= abs($tnsize - $s - 1);
      //-1 serve nei casi in cui es. si chiede 250 e si dereve sciegliere fra 200 o 300;
	  asort($rr);
	  return $dims['tnsizes'][key($rr)];
	}
}

function next_tnsize($tnsize,$spo=1)  //restituisce il valore successivo a quello specificato
{
  global $dims;

  $tn = check_tnsize($tnsize);

  $tnpos = array_search($tn,$dims['tnsizes']);

    $size = ($tnpos+$spo)>=count($dims['tnsizes']) ? max($dims['tnsizes']) : $dims['tnsizes'][$tnpos+$spo];

    return $size;
}

function back_tnsize($tnsize,$spo=1)  //restituisce il valore precedente a quello specificato
{
  global $dims;

  $tn = check_tnsize($tnsize);

  $tnpos = array_search($tn,$dims['tnsizes']);

    $size = ($tnpos-$spo)<=0 ? min($dims['tnsizes']) : $dims['tnsizes'][$tnpos-$spo];

    return $size;
}

function listafoto($size='large',$withlinks=false)
{
	global $nobody;
	global $admin;
	global $public;
	global $imgfiles;
	global $imgfiles_decript;
	global $dims;
	global $opts;
	global $dirs;
	global $urls;	
	global $masks;

    if(!$public) return false;
    
	if(!$nobody)
	{
?>
<html>
<?
	head();
?>
<body id="lista">
<?
	}
		
	switch($size)//	40,80,120,300,1024	
	{
		case 'x-small':
			$dims['tnsize'] = min($dims['tnsizes']);
		break;
		case 'small':
			$dims['tnsize'] = next_tnsize(min($dims['tnsizes']));
		break;
		case 'medium':
			$dims['tnsize'] = back_tnsize(max($dims['tnsizes']));		
		break;
		case 'large':
		default:
			$dims['tnsize'] = max($dims['tnsizes']);
	}
	
	if($withlinks)
		title();	

	$opts['thumbcut'] = 0;
	$opts['thumbquad'] = 0;
	$opts['thumbround'] = 0;
	$dims['tnmargin'] = 0;
	

	foreach($imgfiles as $fil)
	{
		$fotofile = decript_filename($fil);
		
		if($withlinks)
		{
			$page = $urls['current'].sprintf($masks['fotopageurl'], rm_jpg($fotofile));		
			echo "\n<a href=\"".$page."\"><img src=\"".thumburl($fotofile)."\" /></a>";
		}
		else
			echo "\n<img src=\"".thumburl($fotofile)."\" />";
		flush();
	}
	tail();
	exit(0);
}

function fotopage($fotofile)  //pagina con singola foto
{
    global $nolist;
    global $colors;
    global $dims;
    global $dirs;
	global $urls;
	global $recache;
	global $masks;
	global $opts;
	global $fotopage;
	global $public;
	global $admin;
	global $plugins;
	global $nobody;
	global $nocss;
	global $imgfiles;
	global $ajax;

	if(!$public and !$admin)
		return false;
    //FONDAMENTALE!!!

	if(strtolower(substr($fotofile,-4))=='.jpg')
	{
		//se la pagina richiesta è: ../?foto=foto.jpg fa redirect 301 verso  ../?foto=foto
		$url = $urls['current'].sprintf($masks['fotopageurl'], rm_jpg($fotofile) );
		header( "HTTP/1.1 301 Moved Permanently" );
		header( "Location: ".$url ); 
		exit(0);
	}	
	else
		$fotofile .= '.jpg';

	$nobody = isajax();  //per fotopage in ajax su un overlayer

	$fotofilecript = getcript_filename($fotofile);

    if(!is_file($fotofilecript) or in_array($fotofilecript, $nolist))
	{	
		header('HTTP/1.0 404 Not Found');
		exit(0);
	}
	
	$fotopage = true;

    head();	//non viene eseguito quando $nobody e' true nelle chiamate ajax

	if(!$nobody)
	{
		?>
		<body id="fotopage" class="loadingbig">
		<?
	}

	title();
	text();

    plugins_exec('content_top');

	$prevcript = $dirs['current'].getback($fotofile);
	$nextcript = $dirs['current'].getnext($fotofile);
	
	$prevdecript = is_file($prevcript) ? decript_filename($prevcript) : '';
	$nextdecript = is_file($nextcript) ? decript_filename($nextcript) : '';

	if(isajax()):
		$prev = is_file($prevcript) ? $urls['current'].'#'.rm_jpg($prevdecript) : '';
		$next = is_file($nextcript) ? $urls['current'].'#'.rm_jpg($nextdecript) : '';
	else:
		$prev = is_file($prevcript) ? $urls['current'].sprintf( $masks['fotopageurl'], rm_jpg($prevdecript) ) : '';
		$next = is_file($nextcript) ? $urls['current'].sprintf( $masks['fotopageurl'], rm_jpg($nextdecript) ) : '';	
	endif;

	$deftnsize     = $dims['tnsize'];
	$deftnmargin   = $dims['tnmargin'];
	$defthumbquad  = $opts['thumbquad'];
	$defthumbround = $opts['thumbround'];
	$defthumbcut   = $opts['thumbcut'];
	$defthumbinter = $opts['thumbinterlace'];
	
	$dims['tnsize'] = max($dims['tnsizes']);
	$dims['tnmargin'] = 0;
	$opts['thumbquad'] = 0;
	$opts['thumbround'] = 0;
	$opts['thumbcut'] = 0;
	$opts['thumbinterlace'] = 1;

	list($w,$h) = thumbsize($fotofile);

	$stylediv = $nocss ? '' : ' style="width:'.($w+34).'px;"';  //meglio quadrata
	$styleimg = $nocss ? '' : ' style="width:'.$w.'px; height:'.$h.'px;"';  //meglio quadrata
	
	$styleprev2 = 'visibility:'.($prev?'visible':'hidden');
	$stylenext2 = 'visibility:'.($next?'visible':'hidden');

	?>
    <div class="foto_wrap">
		<div class="foto" <?=$stylediv?>>
			<a id="prev2" style="<?=$styleprev2?>" href="<?=$prev?>" rel="prev" name="<?=rm_jpg($prevdecript)?>"><span>◄</span></a>
			<img class="imgfoto" width="<?=$w?>" height="<?=$h?>" src="<?=thumburl($fotofile)?>" alt="<?=thumbalt($fotofile)?>" />
			<a id="next2" style="<?=$stylenext2?>" href="<?=$next?>" rel="next" name="<?=rm_jpg($nextdecript)?>"><span>►</span></a>
		</div>
	<?

	$dims['tnsize']     = $deftnsize;
	$dims['tnmargin']   = $deftnmargin;
	$opts['thumbquad']  = $defthumbquad;
	$opts['thumbround'] = $defthumbround;
	$opts['thumbcut']   = $defthumbcut;
	$opts['thumbinterlace'] = $defthumbinter;
	
	plugins_exec('content_bottom');
	
	?>
	</div>
	<a class="foto_close" title="Chiudi" href="#">&times;</a>
	<? //fine #foto_wrap
	
	if(isajax()) exit(0);

	?><div id="content_banner"><?
	    plugins_exec('content_banner');
	?></div><?

    foot();
	js();
?>
<script>
ULG.fotofilecurrent = '<?=$fotofile?>';  //variabile globale
ULG.fotopage = true;	
fotopage_event();
</script>
<?
	tail();

	exit(0);
}

function thumbid($fotofile)  //restituisce un id numerico univoco per ogni file
{
  global $imgfiles;
  global $imgdirs;

  $fotofile = getcript_filename($fotofile);

  if(is_dir($fotofile))
    $id = array_search($fotofile,$imgdirs);
  else
    $id = array_search($fotofile,$imgfiles);

  return $id;
}

//TODO fare anche datadel()//
function thumbdel($fotofile)	//rimuove tutte le thumbnail alla foto, usato da rotate!
{
	global $dirs;
	global $urls;
	global $dims;
	global $opts;

	$d = opendir($dirs['cache']);
	while($f=readdir($d))	//elimina le thumbnails per tutte le dimensioni gia create
	{
		if(preg_match('#^'.rm_jpg($fotofile).'_[0-9]_[0-9]*\.jpg$#',$f))
			@unlink($dirs['cache'].$f);
	}
	closedir($d);
}

function thumburl($fotofile='.')  //restituisce l'url di una thumbnail per $fotofile
{
  global $recache;
  global $masks;
  global $dirs;
  global $urls;
  global $dims;
  global $opts;
  global $images;
  global $noimgs;
  global $ajax;
  global $recachethumbs;  //diventa true se qualche thumbnail è stata rigenerata
                         //evita di generare cache html con thumb dinamiche
  $criptfotofile = getcript_filename($fotofile);
  //il problema per cui non posso fare thumbnail di subalbum e' perche' getcript_filename
  //utilizza l'array $imgfiles_decript che e' locale

	if( $dims['tnsize'] == max($dims['tnsizes']) )
		$opts['thumbqualit'] = 95;

	if( $dims['tnsize'] == min($dims['tnsizes']) )
		$opts['thumbcut'] = 1;
	elseif( $dims['tnsize'] > $dims['tnsize_default'] )
		$opts['thumbcut'] = 0;

	if($noimgs)
		return $urls['current'].'?thumbfoto';  //mostra una croce

	$thumb_params = '&amp;'.'thumbqualit='.$opts['thumbqualit'].
					'&amp;'.'thumbquad='.$opts['thumbquad'].
					'&amp;'.'thumbcut='.$opts['thumbcut'].
					'&amp;'.'thumbround='.$opts['thumbround'].
					'&amp;'.'thumbinterlace='.$opts['thumbinterlace'].
					'&amp;'.'tnmargin='.$dims['tnmargin'].
					'&amp;'.rand();
	$thumb_hash = bindec(
					intval($opts['thumbquad']).
					intval($opts['thumbcut']).
					intval($opts['thumbround']) );

	if(is_file($criptfotofile))
	{
		$src = sprintf($masks['thumbfile'],
				rm_jpg($fotofile),
				$thumb_hash,
				$dims['tnsize']);

		if($recache or !is_file($dirs['cache'].$src))
		{
			$src = $urls['current']."?thumbfoto=".$fotofile.
				'&amp;'.'tnsize='.$dims['tnsize'].$thumb_params;
			$recachethumbs = true;
		}
		else
			$src = $urls['cache'].$src;
	}
	elseif(is_dir($criptfotofile))
	{
		$src = sprintf($masks['thumbdir'],
				$fotofile=='.' ? basename($dirs['current']) : $fotofile,
				$thumb_hash,
				$dims['tndirsize']);

		if( $recache or
			!is_file($fotofile.'/'.$opts['thumbdirname'].'/'.$src) or
			(filemtime($dirs['current'].$fotofile.'/index.php') > 
			 filemtime($fotofile.'/'.$opts['thumbdirname'].'/'.$src)) )
		{
			$src = $urls['current'].'?thumbdir='.$fotofile.
				'&amp;'.'tndirsize='.$dims['tndirsize'].$thumb_params;
			$recachethumbs = true;
		}
		else
			$src = $fotofile.'/'.$opts['thumbdirname'].'/'.$src;
	}
	else
		$src = '';

  return $src;
}

function thumbalt($fotofile) //testo alternativo per le immagini
{
	$title = urltitle( plugins_rawexec('thumb_title',$fotofile),' ');
	$desc  = urltitle( plugins_rawexec('thumb_text',$fotofile),' ');
	$alt = trim($title)!='' ? $title : $desc;
	$alt = $alt ? $alt : str_replace(array('_','.','jpg'),' ', urltitle($fotofile,' '));
	return trim($alt);
}

function thumbsize($fotofile)
{
  global $dims;
  global $dirs;
  global $opts;

  $criptfotofile = getcript_filename($fotofile);
	
  if(is_dir($dirs['current'].$criptfotofile))
    return array($dims['tndirsize'],$dims['tndirsize']);  //le thumb delle degli album sono sempre quadrate
  elseif(!is_file($dirs['current'].$criptfotofile))
    return false;

  list($sw,$sh) = getimagesize($dirs['current'].$criptfotofile);

  $maxtnsize = max($dims['tnsizes']);
  $mintnsize = min($dims['tnsizes']);

  $tnsizem = $dims['tnsize']-($dims['tnmargin']*2);

  if($sw==$sh)
  {
    $sx = 0;
	$sy = 0;
    $dx = $dims['tnmargin'];
    $dy = $dims['tnmargin'];
    $dw = $tnsizem>$maxtnsize ? $maxtnsize : $tnsizem;
    $dh = $tnsizem>$maxtnsize ? $maxtnsize : $tnsizem;
  }
  elseif($sw>$sh)
  {
  	if($opts['thumbquad'] and $opts['thumbcut']):
		$sx = ($sw-$sh)/2;
		$sy = 0;
		$sw = $sh;
		$sh = $sh;
		$dx = $dims['tnmargin'];
		$dw = $tnsizem;
		$dh = ($sw / $sh) * $tnsizem;
		$dy = ($dims['tnsize'] - $dw) / 2;
	else:
		$sx = 0;
		$sy = 0;
		$dx = $dims['tnmargin'];
		$dw = $tnsizem;
		$dh = ($sh / $sw) * $tnsizem;
		$dy = ($dims['tnsize'] - $dh) / 2;
	endif;
  }
  elseif($sw<$sh)
  {
	  if($opts['thumbquad'] and $opts['thumbcut']):
		$sx = 0;
		$sy = ($sh-$sw)/2;
		$sw = $sw;
		$sh = $sw;
		$dh = $tnsizem;
		$dw = ($sw / $sh) * $tnsizem;
		$dx = $dims['tnmargin'];
		$dy = $dims['tnmargin'];
	  else:
		$sx = 0;
		$sy = 0;
		$dh = $tnsizem;
		$dw = ($sw / $sh) * $tnsizem;
		$dx = ($dims['tnsize'] - $dw) / 2;
	    $dy = $dims['tnmargin'];
	  endif;
  }

  $s = $opts['thumbquad'] ? array($dims['tnsize'], $dims['tnsize']) : array(floor($dw+($dims['tnmargin']*2)), floor($dh+($dims['tnmargin']*2)));

  return $s;
}

function thumbdir($fotodir='.')  //genera un'immagine collage delle foto contenute in $fotodir
{
	global $nolist;
	global $recache;
	global $colors;
	global $images;
	global $masks;
	global $dims;
	global $dirs;
	global $urls;
	global $opts;
	global $imgfiles;

	if(!is_dir($fotodir)) {
		header('HTTP/1.0 404 Not Found');
		exit(0);
	}

	//dare una sfoltita a tutte queste variabili globali.. stanno qui dalla versione 0.8!!!
	$imgs = getfiles($fotodir);
	$nimgs = count($imgs);

	if($nimgs>6){     $nrig = 3; $nthumbs = 9; }
	elseif($nimgs>3){ $nrig = 2; $nthumbs = 6; }
	else{             $nrig = 1; $nthumbs = 3; }
	//migliora la thumbnail con poche foto

	$margin = $dims['thumbdirmargin'];
	$radius = 10;
	$scatter = $opts['thumbdirscatter'];

	makedircache($fotodir);

	$thumb_hash = bindec(
					intval($opts['thumbquad']).
					intval($opts['thumbcut']).
					intval($opts['thumbround']) );

	$filethumbdir = $fotodir.'/'.$opts['thumbdirname'].'/'.
  						sprintf($masks['thumbdir'],
  							$fotodir=='.' ? basename($dirs['current']) : $fotodir,
  							$thumb_hash,
  							$dims['tndirsize']);

  $ncol = ($nthumbs/$nrig);
  $htimg = ($dims['tndirsize']-($margin*($nrig+1)))/$nrig;  //altezza di UNA immagine NEL collage

  $lar = $alt = $dims['tndirsize'];

  $tnimage = imagecreatetruecolor($lar, $alt);

  list($r,$g,$b) = hexrgb($colors['background']);
  $background = imagecolorallocate($tnimage, $r, $g, $b);
  list($r,$g,$b) = hexrgb($colors['bgthumb']);
  $back = imagecolorallocate($tnimage, $r, $g, $b);
  list($r,$g,$b) = hexrgb($colors['bgbox']);
  $box = imagecolorallocate($tnimage, $r, $g, $b);

  imagefilledrectangle($tnimage, 0, 0, $lar, $alt, $back);
  #imagefilledrectangleround($tnimage, $back);
  ulgimagefilledrectangleround($tnimage, $radius, $box, $back,$lar-$dims['tnmargin']-1,$alt-$dims['tnmargin']-1,$dims['tnmargin'],$dims['tnmargin']);

  if($nimgs>0)
  {
    $i = 0;
    $dx = $dy = $margin;
    $dw = $dh = $htimg;

  	shuffle($imgs);

    for($r=1; $r<=$nrig; $r++)  //per ogni riga di foto
    {
      if(!$imgs[$i]) break;
      for($c=1; $c<=$ncol; $c++)  //per ogni colonna di foto
      {
        if(!isset($imgs[$i])) break;

        #$bigimage = @imagecreatefromjpeg($fotodir.'/'.$imgs[$i]);
		$bigimage = ulgimagecreate($fotodir.'/'.$imgs[$i]);

		#echo $fotodir.'/'.$imgs[$i]."<br>";

        $sw = imagesx($bigimage);
        $sh = imagesy($bigimage);

		if($scatter)  //incasina la posizione delle fotine! :D
		{
			$rw = rand(20,40);    //dimensione
			$rx = rand(-10,20)-$rw/2;  //posizione y
			$ry = rand(-10,20)-$rw/2;  //posizione x
			$rr = rand(-20,20);  //rotazione

			$tnimage2 = imagecreatetruecolor($dw+$rw, $dh+$rw);
			imagefilledrectangle($tnimage2, 0, 0, $dw+$rw, $dh+$rw, $back);

			imagecopyresampled($tnimage2, $bigimage, $margin, $margin, 0, 0, $dw+$rw-$margin*2, $dh+$rw-$margin*2, $sw, $sh);
			#imagecopyresized($tnimage2, $bigimage, $margin, $margin, 0, 0, $dw+$rw-$margin*2, $dh+$rw-$margin*2, $sw, $sh);
			#$trasp = imagecolorallocatealpha($tnimage2, 10, 10, 10,127);
			#$tnimage2 = imagerotate($tnimage2, $rr, $trasp);

			imagecopy($tnimage, $tnimage2, $dx+$rx, $dy+$ry, 0, 0, imagesx($tnimage2), imagesy($tnimage2));

			imagedestroy($tnimage2);
		}
		else
		  imagecopyresampled($tnimage, $bigimage, $dx, $dy, 0, 0, $dw, $dh, $sw, $sh);
		  //fotine ordinate in fila

		imagedestroy($bigimage);

        $dx = $htimg*$c + $margin*$c+$margin;
        $i++;
      }
      $dy = $htimg*$r + $margin*$r+$margin;
      $dx = $margin;  //alle 6:06 del mattino ho capito che andavano aggiunti questi 6 caratteri! :O
    }
  }

  if($opts['thumbround'])
    ulgimagerectangleround($tnimage,$radius,$back,$box);  //arrotonda spigoli

	imagejpeg($tnimage, $filethumbdir, $opts['thumbqualit']);
	@chmod($filethumbdir, CHMOD);	//poi vedere se è il caso
	imagedestroy($tnimage);

	// header("Content-type: image/jpeg");
	// readfile($filethumbdir);

	header( "HTTP/1.1 301 Moved Permanently" );
	header( "Location: ".$filethumbdir ); 

  exit(0);
}

function thumbfoto($fotofile)  //pagina con foto singola grande
{
	global $colors;
	global $images;
	global $dims;
	global $dirs;
	global $urls;
	global $masks;
	global $opts;
	global $recache;

	$fotofile = getcript_filename($fotofile);

	if(!is_file($fotofile)){
		header('HTTP/1.0 404 Not Found');
		exit(0);//senno vengono generate immagini vuote a prescindere
	}

	if(!is_dir($dirs['cache']))
		makedircache('');  //crea directory cache

	$thumb_hash = bindec(
					intval($opts['thumbquad']).
					intval($opts['thumbcut']).
					intval($opts['thumbround']) );

	$filethumb = sprintf($masks['thumbfile'],
						rm_jpg( decript_filename($fotofile) ),
						$thumb_hash,
						$dims['tnsize']);

	if(is_file($dirs['cache'].$filethumb) and $recache)
		@unlink($dirs['cache'].$filethumb);

	$image = $fotofile;
	$tnsize = $dims['tnsize'];
	$maxtnsize = max($dims['tnsizes']);
	$medtnsize = back_tnsize($maxtnsize);
	$mintnsize = min($dims['tnsizes']);
	$margin = $dims['tnmargin'];
	$tnsizem = $tnsize-$margin*2;

	$bigimage = ulgimagecreate($fotofile);

	$sw = imagesx($bigimage);
	$sh = imagesy($bigimage);

	if($sw==$sh)
	{
		$sx = 0;
		$sy = 0;
		$dx = $margin;
		$dy = $margin;
		$dw = $tnsizem>$maxtnsize ? $maxtnsize : $tnsizem;
		$dh = $tnsizem>$maxtnsize ? $maxtnsize : $tnsizem;
	}
  elseif($sw>$sh)
  {
  	if($opts['thumbquad'] and $opts['thumbcut']):
		$sx = ($sw-$sh)/2;
		$sy = 0;
		$sw = $sh;
		$sh = $sh;
		$dx = $margin;
		$dw = $tnsizem;
		$dh = ($sw / $sh) * $tnsizem;
		$dy = ($tnsize - $dw) / 2;
	else:
		$sx = 0;
		$sy = 0;
		$dx = $margin;
		$dw = $tnsizem;
		$dh = ($sh / $sw) * $tnsizem;
		$dy = ($tnsize - $dh) / 2;
	endif;
  }
  elseif($sw<$sh)
  {
	  if($opts['thumbquad'] and $opts['thumbcut']):
		$sx = 0;
		$sy = ($sh-$sw)/2;
		$sw = $sw;
		$sh = $sw;
		$dh = $tnsizem;
		$dw = ($sw / $sh) * $tnsizem;
		$dx = $margin;
		$dy = $margin;
	  else:
		$sx = 0;
		$sy = 0;
		$dh = $tnsizem;
		$dw = ($sw / $sh) * $tnsizem;
		$dx = ($tnsize - $dw) / 2;
	    $dy = $margin;
	  endif;
  }
  //calcola dimensioni foto contenuta nella thumbnail

  $thumbmax = (max($dw,$dh) >= $maxtnsize);
  $thumbmin = (max($dw,$dh) <= $mintnsize);

  $radius = $thumbmin ? 5 : 10;  //RAGGIO degli spigoli  //mettere piu valori per le vaire dimensioni

  if($opts['thumbquad'])
    $tnimage = imagecreatetruecolor($tnsize, $tnsize);
  else
    $tnimage = imagecreatetruecolor( floor($dw+$margin*2), floor($dh+$margin*2) );

  list($r,$g,$b) = hexrgb($colors['bgthumb']);
  $back = imagecolorallocate($tnimage, $r, $g, $b);
  list($r,$g,$b) = hexrgb($colors['border']);
  $bord = imagecolorallocate($tnimage, $r, $g, $b);
  list($r,$g,$b) = hexrgb($colors['bgbox']);
  $box = imagecolorallocate($tnimage, $r, $g, $b);

  if($opts['thumbround'])
    ulgimagefilledrectangleround($tnimage,$radius,$back,$box);  //crea sfondo thumbnail
  else
    imagefill($tnimage, 0, 0, $back);

  if($opts['thumbquad']):
  	if($thumbmax or $opts['thumbresampled'])
	  imagecopyresampled($tnimage, $bigimage, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);
	else
	  imagecopyresized($tnimage, $bigimage, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);
  else:
    if($thumbmax or $opts['thumbresampled'])
	  imagecopyresampled($tnimage, $bigimage, $margin, $margin, $sx, $sy, $dw, $dh, $sw, $sh);
    else
	  imagecopyresized($tnimage, $bigimage, $margin, $margin, $sx, $sy, $dw, $dh, $sw, $sh);
  endif;
  //ottimizza prestazioni per thumbnail piccole

  if($opts['thumbround'])
    ulgimagerectangleround($tnimage,$radius,$back,$box);  //arrotonda spigoli

  if($thumbmax or $tnsize>320)
    setcopyright($tnimage);

  imagedestroy($bigimage);

  if($opts['thumbinterlace'])
    imageinterlace($tnimage,1);

  imagejpeg($tnimage, $dirs['cache'].$filethumb, $opts['thumbqualit']);  //POI LA SCRIVE SU FILE, senza bisogno di readfile
  @chmod($dirs['cache'].$filethumb, CHMOD);
  imagedestroy($tnimage);

	header( "HTTP/1.1 301 Moved Permanently" );
	header( "Location: ".$urls['cache'].$filethumb ); 

	// @header("Content-type: image/jpeg");
	// readfile($dirs['cache'].$filethumb);

	exit(0);
}

function ulgimagecreate($fotofile)  //per ora solo file jpeg
{

  global $colors;
  global $dims;

  $tnsize = $dims['tnsize'];
  $maxtnsize = max($dims['tnsizes']);
  $mintnsize = min($dims['tnsizes']);
  $margin = $dims['tnmargin'];

  $border = $tnsize==$mintnsize ? 12 : 42;  //spessore della croce incaso di immagine inesistente

	list($x,$y) = getimagesize($fotofile);

		$bigimage = @imagecreatefromjpeg($fotofile);  //non togliere MAI la @ prima di imagecreatefromJPEG!! mai!!
		if(!$bigimage)
		{
			$bigimage  = imagecreatetruecolor($tnsize, $tnsize);
			  list($r,$g,$b) = hexrgb($colors['bgthumb']);
			  $back = imagecolorallocate($bigimage, $r, $g, $b);
			  list($r,$g,$b) = hexrgb($colors['text']);
			  $text = imagecolorallocate($bigimage, $r, $g, $b);
			  //colore sfondo e bordo
			imagefilledrectangle($bigimage, 0, 0, $tnsize, $tnsize, $back);
			imagestring($bigimage, 4, $tnsize/10, $tnsize/3,    "   FILE    ", $text);
			imagestring($bigimage, 4, $tnsize/10, $tnsize/3+20, "DANNEGGIATO", $text);
		}

  return $bigimage;
}

function ulgimagefilledrectangleround(&$img, $radius, $color, $back,$width=false,$height=false,$x=0,$y=0)  //mia funzione per fare rettangoli pieni con spigoli arrotondati
{
    global $colors;

	if(!$width) $width = imagesx($img)-1;
    if(!$height) $height = imagesy($img)-1;

    imagefilledrectangle($img, $x+$radius, $y, $width-$radius, $height, $color);
    imagefilledrectangle($img, $x, $y+$radius, $width, $height-$radius, $color);
    imagefilledellipse($img, $x+$radius, $y+$radius, $radius*2, $radius*2, $color);
    imagefilledellipse($img, $width-$radius, $y+$radius, $radius*2, $radius*2, $color);
    imagefilledellipse($img, $x+$radius, $height-$radius, $radius*2, $radius*2, $color);
    imagefilledellipse($img, $width-$radius, $height-$radius, $radius*2, $radius*2, $color);
}

function ulgimagerectangleround(&$img, $radius, $color, $back)  //mia funzione per fare cornici rettangolari con spigoli arrotondati
{
    global $colors;
	global $dims;

    $x = $y = 0;
    $width = imagesx($img);
    $height = imagesy($img);
	$thick = 2;
	imagesetthickness($img,$thick);

	if($dims['tnmargin']>0)
      imagerectangle($img,0,1,$width-1,$height-2,$color);  //questi +1-1-2+2+1-1 sono di origine empirica!

    $r=$radius+1;

	$radius++;
	if($dims['tnmargin']>0)
	{
		imagearc($img, $x+$r, $y+$r, $radius*2, $radius*2, 180, 270, $color);
		imagearc($img, $width-$r, $y+$r, $radius*2, $radius*2, 270, 0, $color);
		imagearc($img, $width-$r, $height-$r, $radius*2, $radius*2, 0, 90, $color);
		imagearc($img, $x+$r, $height-$r, $radius*2, $radius*2, 90, 180, $color);
		$radius++;
		imagearc($img, $x+$r, $y+$r, $radius*2, $radius*2, 180, 270, $color);
		imagearc($img, $width-$r, $y+$r, $radius*2, $radius*2, 270, 0, $color);
		imagearc($img, $width-$r, $height-$r, $radius*2, $radius*2, 0, 90, $color);
		imagearc($img, $x+$r, $height-$r, $radius*2, $radius*2, 90, 180, $color);
	}

	while(++$radius<$r+10)
	{
		imagearc($img, $x+$r, $y+$r, $radius*2, $radius*2, 180, 270, $back);
		imagearc($img, $width-$r, $y+$r, $radius*2, $radius*2, 270, 0, $back);

		imagearc($img, $width-$r, $height-$r, $radius*2, $radius*2, 0, 90, $back);
		imagearc($img, $x+$r, $height-$r, $radius*2, $radius*2, 90, 180, $back);
	}
}

function rgbhex($color)  //genera colori html da un array RGB
{
  if(is_array($color))
    return sprintf('#%02x%02x%02x', $color[0]>255?255:($color[0]<0?0:$color[0])
                                  , $color[1]>255?255:($color[1]<0?0:$color[1])
                                  , $color[2]>255?255:($color[2]<0?0:$color[2])
                                  );  //modifica fondamentale! e anche bella
	else return false;
}

function hexrgb($color)
{
  $rgb = array(0,0,0);
  if( preg_match( "/^[#]?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i", $color, $ret ) )
  {
    $rgb = array(hexdec($ret[1]),hexdec($ret[2]),hexdec($ret[3]));
  }
  return $rgb;
}

function pixem($pixel) //converte da pixel a em
{
  return round($pixel*0.0625,3);
}

function empix($pixel) //converte da em a pixel
{
  return round($pixel*16,0);
}

function islocal()
{
  if(!isset($_SERVER['REMOTE_ADDR'])) return true;
  //eseguito da shell
  $loc = explode('.',$_SERVER['REMOTE_ADDR']);
  if($loc[0]=='127' or $loc[0]=='192')  //indirizzi rete locale o lookup
    return true;
  else
    return false;
}

function isajax()
{	
  if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
    return true;
  else
    return false;
}

function setcopyright(&$image,$text='')  //scrive il testo o il logo di copyright sull'immagine $image
{
  global $users;
  global $username;
  global $copyright;
  global $copyrightlogo;
  global $copyrightfont;
  global $copyrightsize;
  global $dims;
  global $dirs;

  $copyrighttext = empty($text) ? $copyright : $text;

    $fsize = $copyrightsize/7;

    $dw = imagesx($image);
    $dh = imagesy($image);

	list($r,$g,$b) = hexrgb('#ffffff');
	$ctext = imagecolorallocatealpha($image, $r, $g, $b, 80);  //testo
	list($r,$g,$b) = hexrgb('#000000');
    $ctrasp = imagecolorallocatealpha($image, $r,$g,$b, 80);  //sfondo
	list($r,$g,$b) = hexrgb('#ffffff');
	$otext = imagecolorallocate($image, $r, $g, $b);  //testo opaco
	
    if(isset($copyrightlogo) and is_file($copyrightlogo) and strtolower(substr($copyrightlogo,-4))=='.gif')  //logo autore
	{
 	  $imgwm = imagecreatefromgif($copyrightlogo);
      imagecopymerge($image, $imgwm, 5, 5, 0, 0, imagesx($imgwm), imagesy($imgwm), 30);
	  //immagine in trasparenza del 30%
	}
                
	  imagefilledrectangle($image,
                $dw-($copyrightsize*1.3),$dh-($copyrightsize/2)*(strlen($copyrighttext)-1),
                $dw-($copyrightsize/3),$dh,
                $ctrasp);

	  if(is_file($copyrightfont))
	    imagettftext($image, $fsize*5, 90, $dw-8, $dh-12, $ctext, $copyrightfont, $copyrighttext);
	  else
	    imagestringup($image, $fsize, $dw-19, $dh-12, $copyrighttext, $ctext);
      #imagestringup($tnimage, 4, 4, 4+8*strlen($copyright), $copyrighttext, $text);
  return true;
}

function html4json($text)  //prepara l'html per essere inviato come stringa dentro json
{
	return str_replace(array("'","\n","\r","/"),array('"','','','\/'),trim($text));
}

function check_email($email)
{
    $pattern =  '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9_]))*\@([a-z0-9])'.
                '(([a-z0-9-])*([a-z0-9]))+'.
                '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
    return preg_match($pattern, $email);
}


function set_cookie($name,$value)
{
	global $urls;
	global $sessiontime;
	return setcookie($name, $value, time()+$sessiontime, dirname($urls['base']), $_SERVER['SERVER_NAME'], 1);
}

function ulgStartSession($new=false)
{
	global $urls;
	global $alerts;
	global $opts;
	
	@session_set_cookie_params($opts['sessiontime']);
	#time() + 42000 = 7 giorni
	if(isset($_REQUEST['sid']))
		session_id($_REQUEST['sid']);//per mantenere la session ad esempio con il plugin uploadify, che non usa cookie
	@session_name('ULG');
	@session_start();

	if($new)	//ricrea una nuova sessione, vedi in login.php login_in()
		@session_regenerate_id(true);
}

function ulgDelSession()
{
	unset($_SESSION['username']);
	#unset($username);

	session_regenerate_id(true);
	@session_destroy();    //Distruggo le sessioni
	//ma fors eutile cosi non invia piu cookie di sessione
}


function email($to, $obj, $text, $mit=false)
{
  global $users;

  if(islocal()) return true;

#  $from = $mit===false ? VERSION.' <postmaster@'.$_SERVER['SERVER_NAME'].'>' : $mit;
#  $from = $mit===false ? ' <'.$users['admin']['email'].'>' : $mit;
  $from = $mit===false ? VERSION.' <noreply@'.$_SERVER['SERVER_NAME'].'>' : $mit;

  $boundary = '--=='.md5(time());

  $heads = "From: $from"."\n".
	       "Reply-To: $from"."\n".
		   "Date: ".date("D, d M Y H:i:s O")."\n".
           "MIME-Version: 1.0"."\n".
           "X-Mailer: ".VERSION."\n".  //mmmah ste X booo
		   "User-Agent: ".VERSION."\n".
           "Content-Type: multipart/alternative;\n"."\tboundary=\"$boundary\"";

  $mtext = "This is a multi-part message in MIME format."."\n".
	       "--$boundary"."\n".
		   "Content-Type: text/plain; charset=UTF-8"."\n".
		   "Content-Transfer-Encoding: 7bit"."\n\n".
		   strip_tags(preg_replace("'<style[^>]*?>.*?</style>'si",'',$text))."\n\n";

  $mhtml = "--$boundary"."\n".
           "Content-Type: text/html; charset=UTF-8"."\n".
           "Content-Transfer-Encoding: 7bit"."\n\n".
		   $text."\n\n".
		   "--$boundary--"."\n";

  $mess = $mtext.$mhtml;
  
  return @mail($to, $obj, $mess, $heads);
}

function ulg_thumbnail($fotofile)  //interfaccia ajax per la funzione thumb()
{
	thumb($fotofile);
}

function ulg_thumbmenu($fotofile)  //interfaccia ajax per la funzione thumb()
{
	thumb_menu($fotofile);
}

function ulg_thumbtext($fotofile)  //interfaccia ajax per la funzione thumb()
{
	thumb_text($fotofile);
}

function ulg_foot()
{
  global $urls;
?>
<a href="https://github.com/stefanocudini/UltraLightGallery"><?=VERSION?></a><br />
&copy;Copyright Stefano Cudini
<?
}

//FUNZIONI PER LA GESTIONE DEI NOMI DEI FILE:
function urlclean($url)  //rimuove && multipli
{
	return !empty($url) ? preg_replace("/([&]{2,})/", '&', $url) : '';
}

function urltitle($title,$div='-')  //restituisce il nome file filtrato.. da un titolo con $div come divisore di parole
{
    $title = strip_tags($title);
    // Preserve escaped octets.
    $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    // Remove percent signs that are not part of an octet.
    $title = str_replace('%', '', $title);    
    // Restore octets.
    $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

    $title = str_replace("'", '-', $title);
    
    $title = remove_accents($title);
    if (seems_utf8($title)) {
        if (function_exists('mb_strtolower')) {
            $title = mb_strtolower($title, 'UTF-8');
        }
        $title = utf8_uri_encode($title);
    }

    $title = strtolower($title);
    $title = preg_replace('/&.+?;/', '', $title); // kill entities
    $title = preg_replace('/[^%a-z0-9 _.-]/', '', $title);  //HO AGGIUNTO IL . SENNO ME LO TOGLIE NEI FILE JPG
    $title = preg_replace('/\s+/', $div, $title);
    $title = preg_replace('|-+|', $div, $title);
    $title = trim($title, $div);

	return str_replace('.jpeg','.jpg', $title);
}


function utf8_uri_encode( $utf8_string ) {
  $unicode = '';
  $values = array();
  $num_octets = 1;

  for ($i = 0; $i < strlen( $utf8_string ); $i++ ) {

    $value = ord( $utf8_string[ $i ] );

    if ( $value < 128 ) {
      $unicode .= chr($value);
    } else {
      if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

      $values[] = $value;

      if ( count( $values ) == $num_octets ) {
    if ($num_octets == 3) {
      $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
    } else {
      $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
    }

    $values = array();
    $num_octets = 1;
      }
    }
  }

  return $unicode;
}


function seems_utf8($Str) { # by bmorel at ssi dot fr
    for ($i=0; $i<strlen($Str); $i++) {
        if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
        elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
        elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
        elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
        elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
        elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
        else return false; # Does not match any model
        for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
            if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
            return false;
        }
    }
    return true;
}

function remove_accents($string) {
    if (seems_utf8($string)) {
        $chars = array(
        // Decompositions for Latin-1 Supplement
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
        chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
        chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
        chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
        chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
        chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
        chr(196).chr(134) => 'C', chr(196).chr(134) => 'c',
        chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
        chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
        chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
        chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
        chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
        chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
        chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
        chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
        chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
        chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
        chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
        chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
        chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
        chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
        chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
        chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
        chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
        chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
        chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
        chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
        chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
        chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
        chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
        chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
        chr(197).chr(128) => 'l', chr(196).chr(129) => 'L',
        chr(197).chr(130) => 'l', chr(196).chr(131) => 'N',
        chr(197).chr(132) => 'n', chr(196).chr(133) => 'N',
        chr(197).chr(134) => 'n', chr(196).chr(135) => 'N',
        chr(197).chr(136) => 'n', chr(196).chr(137) => 'N',
        chr(197).chr(138) => 'n', chr(196).chr(139) => 'N',
        chr(197).chr(140) => 'O', chr(196).chr(141) => 'o',
        chr(197).chr(142) => 'O', chr(196).chr(143) => 'o',
        chr(197).chr(144) => 'O', chr(196).chr(145) => 'o',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
        chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
        chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
        chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
        chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
        chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
        chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
        chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
        chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
        chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
        chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
        // Euro Sign
        chr(226).chr(130).chr(172) => 'E');

        $string = strtr($string, $chars);
    } else {
        // Assume ISO-8859-1 if not UTF-8
        $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
            .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
            .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
            .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
            .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
            .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
            .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
            .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
            .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
            .chr(252).chr(253).chr(255);

        $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

        $string = strtr($string, $chars['in'], $chars['out']);
        $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
        $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
        $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
}

?>
