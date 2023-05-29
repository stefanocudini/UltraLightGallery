<?

function compress_init()
{
  global $dirs;
  global $urls;
  global $recache;

  $urls['cssmin'] = str_replace('.css','.min.css',$urls['css']);
  $dirs['cssmin'] = str_replace('.css','.min.css',$dirs['css']);
  $urls['jsmin']  = str_replace('.js','.min.js',$urls['js']);
  $dirs['jsmin']  = str_replace('.js','.min.js',$dirs['js']);  

  if(!is_file($dirs['cssmin']) or !is_file($dirs['jsmin']))
  {
    ulg_cache();  //soluzione piu razionale... forse
  compress_cache();
  }

  if(!$recache and !islocal())
  {
  $urls['css'] = $urls['cssmin'];
  $urls['js']  = $urls['jsmin'];
  }
}

function compress_cache()  //comprime file css e javascript in cache dopo che sono stati creati da ulg_cache() o css(),js()
{
  global $dirs;
  global $urls;


  require_once('compress.cssmin.php');
  require_once('compress.jsmin.php');

  put_contents($dirs['cssmin'], cssmin::minify(get_contents($dirs['css'])));
  @unlink($dirs['css']);
  $urls['css'] = $urls['cssmin'];
  //nuovo file css ufficiale

  $myPacker = new JavaScriptPacker( get_contents($dirs['js']), 'None', true, false); //$_specialChars==true incasina uploadify
  //	function JavaScriptPacker($_script, $_encoding = 62, $_fastDecode = true, $_specialChars = false)
  $packed = $myPacker->pack();
  put_contents($dirs['jsmin'], $packed);
  @unlink($dirs['js']);
  $urls['js'] = $urls['jsmin'];
  //nuovo file css ufficiale
}


?>
