<?

function importa_init()
{
	global $start;
	global $images;
	global $dirs;
	global $urls;
	global $numfileimported;
	global $admin;
	global $dims;
	global $defnumimp;
	global $maxnumimp;

	$defnumimp = 1;  //numero predefinito di input file disponibili
	$maxnumimp = 6;

	$images['importicon']['dir'] = $dirs['cache_base'].'_importicon.png';
	$images['importicon']['url'] = $urls['cache_base'].'_importicon.png';

	$numfileimported = 0;
	$dims['importdims']= array($dims['tnsize_default'],'default'=>300,700,1024,1600);  //lato piu lungo delle immagini da trovare
	$dims['importnums']= array('default'=>$dims['maxgetfiles'],1,5,10,30,50);  //lato piu lungo delle immagini da trovare

	if($admin and isset($_GET['import']))  //interfaccia di importazione
	  $start = 'importa';
}

if($admin)
{

function importa_menu()
{
    global $urls;
	global $images;

    ?>
	<a href="<?=$urls['action']?>&import" title="Copia immagini da altri siti web"><img src="<?=$images['importicon']['url']?>" /><span>Importa</span></a>
    <?
    return 'Importa immagini';
}

function importa_form()
{
	global $urls;
	global $dims;
	global $defnumimp;
	global $maxnumimp;
?>
    <form id="formimporta" action="<?=$urls['action']?>" method="post">
    <input type="hidden" name="submit" value="importa" />
	<label>Indirizzi URL:</label>

    <ul id="urllist">
  <?
  $numimp = $defnumimp>$maxnumimp ? $maxnumimp : $defnumimp;
  for($i=1; $i<=$numimp; $i++)
  { ?>
    <li><input id="url<?=$i?>" name="siteurl[]" type="text" size="60" value="<?=isset($_POST['siteurl'])?$_POST['siteurl'][$i-1]:''?>" title="Indirizzo di una Pagina o Immagine" />
    <?
	if($i+1>$numimp) {?><a href="#" id="addurl" class="pulsante" title="Aggiungi un Indirizzo"><big>+</big></a><? }
	?>
    </li>
  <? } ?>
  </ul>

	<br />
	<label>Tipo sito web:</label>
	<select name="type" disabled="disabled">
        <option value="default" selected="selected">Generico</option>
        <option value="ulg">Ultra Light Gallery</option>
		<option value="picasa">Picasa Web Album</option>
		<option value="flickr">Flickr</option>
		<option value="facebook">Facebook</option>
	</select>&nbsp;&nbsp;

	<label>Immagini pi&ugrave; grandi di:</label><select name="mindim">
	<? foreach($dims['importdims'] as $k=>$dim): ?>
      <option value="<?=$k?>"<? echo $k==='default'?' selected="selected"':''; ?>><?=$dim?> pixel</option>
	<? endforeach; ?>
	</select>&nbsp;&nbsp;

	<label>Limite numero di Immagini:</label><select name="maxnumimg">
	<? foreach($dims['importnums'] as $k=>$num): ?>
	  <option value="<?=$k?>"<? echo $k==='default'?' selected="selected"':''; ?>><?=$num==$dims['maxgetfiles']?'tutte':$num?></option>
	<? endforeach; ?>
	</select>

	<br /><br />
	<a href="<?=$urls['current']?>" class="pulsante"><big>&laquo;</big> Torna all'Album</a>&nbsp;
    <input type="reset" id="importareset" value="Annulla" class="pulsante" />&nbsp;
    <input type="submit" id="importasubmit" value="Importa" class="pulsante" />&nbsp;
    </form>
<?
}

function importa_submit()
{
	global $alerts;

	$alerts[]= 'Importazione immagini in corso...';
//viene eseguto importa_get() in importa_start()
}

function importa_start()
{
    global $urls;
	global $dirs;
	global $importedfiles;
	global $numfileimported;
	global $urlsite;
	global $alerts;
    global $importsubmit;

	head();

    ?>
    <body id="importa">
    <h2>Importa immagini da Web</h2>
    <?

	importa_form();

	?><hr /><?

	alert();

	?><div class="thumbs photos" style="position:relative"><?

	if(isset($_POST['siteurl']))
	  importa_get(); //importa immagini e stampa il nome

	?></div><?

    js();  //javascript non cachare xke forse usera' i coockie
    tail();
}

function importa_copiathumb($urlimg)
{
	global $dirs;
	global $dims;
	global $urlsite;
	global $opts;

	require_once('importa.url.php');
	$src = InternetCombineURL($urlsite, $urlimg);  //genere url assoluti per i tag IMG della pagina
	$url = parse_url($src);
	$basesrc = check_filename(basename($url['path']));

	if( strtolower(substr($basesrc,-4))=='.jpg' or strtolower(substr($basesrc,-5))=='.jpeg' )  //filtro sul tipo di immagini
	{
		$destname = 'import'.(microtime()*1000000).'_'.$basesrc;
		if( copy($src, $dirs['current'].$destname) )
		{
			@chmod($dirs['current'].$destname, CHMOD);
			list($w,$h) = getimagesize($dirs['current'].$destname);

			if( max($w,$h) < $dims['importmindim'] )             //se immagine troppo piccola
				unlink($dirs['current'].$destname);
			else
			{
				importa_thumbnail($src,$destname);
				cache_reset();//rigenera la cache html della pagina
			}
		}
	}
}

function importa_thumbnail($src,$fotofile)  //wrapper
{
	global $numfileimported;
	global $sourceurlfiles;
	global $importedfiles;
	global $dims;

	if($numfileimported >= $dims['importmaximg']) return false;

	$numfileimported++;
	$sourceurlfiles[]= $src;
	$importedfiles[]= check_filename($fotofile);

	thumb_wrap($fotofile);
	#echo "[[$fotofile]]&nbsp;&nbsp;";
    flush();
}

function importa_begin($tag, $attributes, $readSize)  //richiamata per ogni tag trovato nella pagina remota
{
	global $cnt;
	global $dirs;
	global $colors;
	global $dims;
	global $alerts;

	if($tag=='img' and isset($attributes['src']))  //!!!trovare anche tag A con collegamenti a jpg/jpeg
	{
		importa_copiathumb($attributes['src']);
	}

	//fare in modo le img non devono essere importate se linkano una img piu grande

	if($tag=='a' and isset($attributes['href']))  //!!!trovare anche tag A con collegamenti a jpg/jpeg
	{
		importa_copiathumb($attributes['href']);
	}
}

function importa_get()
{
    global $urls;
	global $dirs;
	global $sourceurlfiles;
	global $importedfiles;
	global $numfileimported;
	global $urlsite;
	global $dims;
	global $alerts;

	 $siteurls = is_array($_POST['siteurl']) ? $_POST['siteurl'] : array($_POST['siteurl']);

     $mindim = isset($_POST['mindim']) ? $_POST['mindim'] : 'default';
	 $dims['importmindim'] = $dims['importdims'][$mindim];

     $maxnum = isset($_POST['maxnumimg']) ? $_POST['maxnumimg'] : 'default';
	 $dims['importmaximg'] = $dims['importnums'][$maxnum];

	 foreach($siteurls as $purl):

	    if(!fopen($purl,"r"))  //se l'url è valido
	      continue;

		$urlsite = dirname($purl); //non togliere dirname
		$numfileimported = 0;

		$url = parse_url($purl);
		$basesrc = check_filename(basename($url['path']));

		if( strtolower(substr($basesrc,-4))=='.jpg' or strtolower(substr($basesrc,-5))=='.jpeg' )  ///////////SINGOLA IMMAGINE
		{
			importa_copiathumb($url['path']);
		}
		else  ////////////PAGINA CON IMMAGINI
		{
		  if(!isset($parser))
		  {
			  require_once('importa.parser.php');  //questo deve stare dopo la definizione di importa_begin
			  $cnt = 0;
			  $parser = new HTML_SAXParser();
		  }
		  $parser->initFunc('importa_begin');
		  $parser->parse($purl);
		}

	  endforeach;
	  $alerts[]= 'Importazione terminata con '.$numfileimported.' immagini';
}

}  //fine admin


function importa_js()
{
	global $defnumimp;
	global $maxnumimp;

if(false): ?>
<script>
<? endif; ?>

var idurl = <?=$defnumimp?>;
var maxidurl = <?=$maxnumimp?>;

var rmurl = $('<a href="#" class="removeurl pulsante" title="Rimuovi url"><big>-</big></a>');

$(document).ready(function() {

  $('#addurl').click(function() {
    addurl($(this).prev());
    return false;
  });

  rmurl.click(function() {
    $(this).parents('li').remove();
	maxidurl++;
    return false;
  });

});

function addurl(obj)  //crea un nuovo file input e lo aggiunge sotto agli altri
{
  if(idurl==maxidurl)
    return false;

  var newinput = obj.clone(); //true clona anche gli eventi
  var id = obj.attr('id');
  var add = $('#addurl').clone(true);

  var rm = rmurl.clone(true);

  obj.next('#addurl').remove();
  obj.after(rm);

  idurl++;
  newinput.attr('id', 'url' + idurl );  //cambia l'id ad ogni input file aggiunto o modificato
  newinput.val('');
  newinput.prependTo('#urllist').wrap('<li></li>');
  newinput.after(add);
}

<? if(false): ?>
</script>
<? endif;
} # */


function importa_css()
{
  global $images;
	global $colors;
	global $dims;
?>
<? if(false): ?>
<style type="text/css">
<? endif; ?>
body#importa {
  margin:1em;
}
body#importa #alert_wrap {
position:static;
z-index:30;
}
body#importa #alert {
z-index:30;
}

#urllist {
  list-style: none;
  padding:0;
  margin:0;
}
#urllist li {
  clear: both;
  list-style: none;
  padding:0;
  margin-bottom: 0.25em;
  overflow:hidden;
}
#urllist input {
  float:left;
  width:31em;
}
#addurl,
.removeurl {
  display:block;
  width:1em;
  height:1em;
  text-align:center;
  vertical-align:middle;
  float:left;
  margin: .25em;
}
#addurl big,
.removeurl big {
  font-weight:bold;
  font-size:1.6em;
  font-family: "Courier New", Courier, monospace;
}
<? if(false): ?>
</style>
<? endif; ?>
<?
} # */

function importa_cache()   //in futuro generare i dati exif all'interno di questa funzione.. ua volta per tutte
{
  global $images;

  require('importa.cache.php');

  put_contents($images['importicon']['dir'],$icon1);
}

?>
