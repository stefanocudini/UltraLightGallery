<?

//File di configurazione personalizzata per l'intera gallery o un album specifico

/*
	HOWTO:
	1) creare una copia di questo file e chiamarla config.php
	2) creare o modificare il file index.php e includervi config.php
	3) per personalizzare solo un album modificare solo la index.php all'interno dell'album
*/

$users['demo'] = array( 'password'=>'demo',
						'email'=>'admin@example.com',
						'copyright'=>'www.example.com');
$users['admin'] = $users['demo'];

$cachetime = false;	//cache html disabilitata
$ulgfile = __FILE__;//file di configurazione per gli album creati successivamente

$urlbase = '/photos/';
$dirbase = '/var/www/mysite.com/photos/';

$urlplugins = $urlbase;
$dirplugins = $dirbase;

/////////// PLUGINS ///////////
$plugins = array();
$plugins['ulg'] = null;                              //plugin base, incorporato  //va dopo jquery xke utilizza jquery
$plugins['jquery'] = "jquery.php";                   //libreria javascript/ajax http://www.web2tool.com/visual_jquery/
$plugins['demo'] = "demo.php";          			 //dati di prensetazione e screenshots di ULG
$plugins['captcha'] = "captcha.php";                 //genera immagine captcha da usare in alcuni form(guestbook)
$plugins['login'] = "login.php";                     //gestore di autenticazione utente
$plugins['fontsize'] = "fontsize.php";               //dimensione dei caratteri della pagina
$plugins['zoom'] = "zoom.php";                       //varie funzionalità di zoom delle thumbnail
$plugins['refresh'] = "refresh.php";                 //aggiornamento del contenuto di una thumbnail
$plugins['uploadify'] = "uploadify.php";             //uploadify, upload multifile in flash (uploadify.com)
#$plugins['upload'] = "upload.php";                   //upload classico via form html
$plugins['importa'] = "importa.php";                 //importa immagini da una pagina di un altro sito
$plugins['delete'] = "delete.php";                   //eliminazione foto via http
$plugins['crop'] = "crop.php";                       //ritaglia foto con jcrop
$plugins['desc'] = "desc.php";                       //aggiunta di descrizione e titolo alle foto
$plugins['popupwindow'] = "popupwindow.php";         //apre le foto in una finestra di popup
$plugins['rotate'] = "rotate.php";                   //ruota una foto
$plugins['condividi'] = "condividi.php";             //invia la pagina ad altri siti
$plugins['file'] = "file.php";                       //informazioni su file immagine e directory
$plugins['calendario'] = "calendario.php";           //calendario degli album
#$plugins['wallpaper'] = "wallpaper.php";             //genera immagine ridimensionata per essere usata come sfondo del desktop
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
#$plugins['adv'] = "advertising.php";                //aggiunte contenuti di advertising tipo banner
$plugins['chat'] = "chat.php";					 	//zopim chat: https://dashboard.zopim.com/#Widget/getting_started
$plugins['analytics'] = "analytics.php";             //google analytics javascript di monitoraggio
$plugins['track'] = "track.php";					 //tracciamento e interazione in tempo reale con il visitatore
//PLUGINS SPERIMENTALI
#$plugins['themes'] = "themes.php";                   //definisce vari temi grafici (tenere sempre per ultimo)
#$plugins['multiscatto'] = "multiscatto.php";         //mostra foto in sequenza su uno stesso div.thumb
#$plugins['vota'] = "vota.php";                       //votazione delle foto
#$plugins['colors'] = "colors.php";                   //calcola colori css da colori delle foto
$plugins['compress'] = "compress.php";                //comprime/offusca css/javascript/html
$plugins['debug'] = "debug.php";                      //funzionalità di debug e svioluppo per creare nuovi plugins (caricare per ultimo)
//DISATTIVA TUTTI i plugins
#$plugins = array('ulg'=>null);
@require_once($dirplugins.'ulg.php');
?>
