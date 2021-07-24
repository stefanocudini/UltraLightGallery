<?

if($admin)  //non mettere dentro init senno è inutile, deve essere eseguito prima
{
	if(isset($_GET['noimgs']))  $noimgs = true;
	if(isset($_GET['nobody']))  $nobody = true;
	if(isset($_GET['nocss']))   $nocss = true;
	if(isset($_GET['nojs']))    $nojs = true;
	if(isset($_GET['nopanel'])) $nopanel = true;
	if(isset($_GET['nomenu']))  $nomenu = true;
	//OPZIONI VIA GET
}

function debug_getmicrotime($reset=false, $label='TIME')
{
   static $TIME;

   list($usec, $sec) = explode(" ",microtime());
   $t = ((float)$usec + (float)$sec);

   $lt = $t - $TIME;
   $type = ' First';

   $ret = round($lt * 1000,3).'ms';

   if($reset or !isset($TIME))
   {
   $TIME = $t;  //azzera timer
   $ret .= ' - Restart';
   }

   return $label.': '.$ret;
}

function debug_init()
{
    global $admin;
    global $dirs;
    global $urls;
	global $alerts;

    if(isset($_GET['stop']))
	  debug_deactivate(); // dis/attiva ulg.php impostando $stop, richiede $admin==true

    if(isset($_GET['cartellizza']))
	  debug_cartellizza();  //genera tanti album nominati ddmmyyyy da una unica lista di foto nominate secondo la data( ddmmyyyy_numero.jpg ), richiede $admin==true

	if(isset($_GET['reindex']))    //mettere opzione di ricorsivita'
	{
	  if(empty($_GET['reindex']))
	    debug_remakeindex();
      elseif($_GET['reindex']=='all')
	    debug_remakeindexall();
	}

	if(isset($_GET['getsleep']))
	  debug_getsleep($_GET['getsleep']);

  	if(isset($_GET['bench']))
	  debug_benchmark();

	if(isset($_GET['base64']))
	  debug_base64();  //converte un file in base64 per poter essere incluso nel php, in fase di sviluppo di un plugin, richiede $admin==true

	if(isset($_GET['funcs']))
	  debug_funcs();      //mostra la lista di tutte le funzioni definite

    if(isset($_GET['alert']) and $admin)
	  $alerts[] = $_GET['alert'];

	if(isset($_GET['phpinfo']) and $admin)
	{
	  phpinfo();
	  exit(0);
	}

}

function debug_remakeindexall($fotodir='.', $options=array())
{
	global $dirs;
	global $admin;

	if(!$admin) return false;

	makeindex($fotodir, $options);  //index corrente

    foreach(getdirs($fotodir) as $d)
	  debug_remakeindex($d, $options);
}

function debug_remakeindex($fotodir='.', $options=array())
{
	global $dirs;
	global $admin;
	global $alerts;

	if(!$admin) return false;

	$fileindex = "$fotodir/index.php";

	if(is_file($fileindex))
	  unlink($fileindex);

	makeindex($fotodir, $options);

	$alerts[]= "index rigenerata per <b>$fotodir</b>";
}

function debug_deactivate()  //disattiva ulg.php
{
  global $admin;
  global $stop;

    if(!$admin) return false;

    if(!empty($_GET['stop']))
    {
        $ar = file(ULGFILE);

		if($_GET['stop']=='true') $ar[1]= '$stop=true;'."\n";
        elseif($_GET['stop']=='false') $ar[1]= '$stop=false;'."\n";
		else return false;

        $str = implode('',$ar);
        $f=fopen(ULGFILE,"w"); fwrite($f,$str);  fclose($f);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit(0);
    }
}

function debug_benchmark()
{
	global $bench;
	global $block;

	require_once('debug.benchmark.php');
	$bench = new Stoper(8,6);
	$bench->AdvStart();

	function debug_head($param)	{
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_head_css($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_title($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_text($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_content($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_thumb_wrap($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_panel($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_menu($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_user_menu($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }
	function debug_foot($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
	}
	function debug_head_js($param) {
	  global $block;
  	  global $bench;
	  $bench->AdvCheckPoint('<b>'.$block.'</b>:'.$param);
    }

    function debug_tail() {
        global $bench;
		global $benchlist;
		global $colors;
		global $block;

		$bench->AdvCheckPoint('<b>'.$block.'</b>:');

        $bench->AdvStop();

        ?>
		<div style="position:fixed; z-index:100; bottom:0; left:40%; width:20%; border:3px solid <?=$colors['hover']?>; background:<?=$colors['bghover']?>; padding:0.5em">
		<b>Benchmarks:</b><br />
		<?
		echo $bench->showAdvResults();
		?></div><?
	}
}

function debug_cartellizza()  //raggruppa le foto in album
{
  global $admin;
  global $alerts;
  global $opts;

  if(!$admin) return false;

  $files = getfiles();
  foreach($files as $fil)
  {
    list($ndir,$o) = explode('_',$fil);
    if(!is_dir($ndir)) mkdir($ndir, CHMOD);
    rename($fil,"./$ndir/$fil");
    $l .= "$ndir, ";
  }
  $alerts[]= 'Album generati: '.$l;
}


function debug_getsleep($file='',$delay=1)	//algoritmo per rallentare il download di un file
{
	$t = 50000*$delay;

	if(!is_file($file))
	{
		header("HTTP/1.1 404 Not Found");
		exit();
	}

	$fil = basename($file);

	if(strtolower(substr($fil,-4))=='.jpg' or strtolower(substr($fil,-5))=='.jpeg')
	  header("Content-type: image/jpeg");
	elseif(strtolower(substr($fil,-4))=='.gif')
	  header("Content-type: image/gif");
	elseif(strtolower(substr($fil,-4))=='.css')
	  header("Content-type: text/css");
	elseif(strtolower(substr($fil,-4))=='.js')
	  header("Content-type: application/x-javascript");

	$f = fopen($file, "rb");
	while(!feof($f))
	{
		echo fread($f,1024);
		flush();
		usleep($t);
	}
	fclose($f);

	exit(0);
}

function debug_base64()  //converte un file in base64 per poter essere incluso nel php, in fase di sviluppo di un plugin
{
  global $admin;

    if(!$admin) return false;

  header("Content-type: text/plain");
  echo '$icon1 = base64_decode("'.base64_encode(file_get_contents($_GET['base64'])).'");'."\n\n";
  exit(0);
}

function debug_printr($var,$ret=false)
{
	$out = '<pre>';
	$out = print_r($files,$ret);
	$out = '</pre>';

	if($ret) return $out;
	else echo $out;
}

function debug_out($var=null, $append=false, $exit=false)
{
  global $dirs;
  $fd = fopen($dirs['plugins']."/debug.txt", $append?"a":"w");
  fwrite($fd, "DEBUG DATA:".date("y-m-d h:i:s")."\n");
  if(is_string($var))
    fwrite($fd, $var);
  else
    fwrite($fd, print_r($var, true));
  fclose($fd);
  if($exit) exit(0);
}

function debug_funcs()
{
	global $plugins;
	global $imgfiles;
    global $admin;

    if(!$admin) return false;

	?>
	<style type="text/css">
	#funcs a { border:1px solid blue;margin:5px;padding:2px;float:left; font-weight:bold}
	#funcsthumb { border:1px solid #ff0000; clear:both; float:left; padding:1em; width:45%; position:relative;}
	#funcsthumbsource { border:1px solid #ff0000; clear: right; float:right; padding:1em; width:45%}
	</style>
	<div id="funcs">
	<?
	$funcs = get_defined_functions();
	echo count($funcs['user']).' funzioni<br />';
	foreach($funcs['user'] as $f)
	{
		list($pname,$func,$o) = explode('_',$f);
		if(in_array($pname, array_keys($plugins)))
		{
		  $fplugins[$pname][]= $f;
		  ?><a href="?ajax=<?=$pname?>&func=<?=$func.($o?'_'.$o:'')?>&file=<?=$imgfiles[0]?>"><?=$f?></a><?
		}
	}
	#debug($fplugins);
	?>
	</div>
	<div id="funcsthumb">Output:<div></div></div>
	<div id="funcsthumbsource">Source:<pre></pre></div>
	<script>
	var href;
	$('#funcs a').click(function() {
	  href = $(this).attr('href');
	  $('#funcsthumb div').load(href,
			function(r) {
				$('#funcsthumbsource pre').text(r);
			});
	  return false;
	});
	</script>
	<?

	exit(0);
}

function is_files($files)  //verifica esistenza di tutti i files
{
  foreach($files as $f)
      if(!is_file($f)) return false;

    return true;
}
?>
